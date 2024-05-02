<?php

defined('MOODLE_INTERNAL') || die();

class block_last_activity_users extends block_base {
    public function init() {
        $this->title = get_string('pluginname', 'block_last_activity_users');
    }

    public function get_content() {
        global $DB, $COURSE, $OUTPUT;

        if ($this->content !== null) {
            return $this->content;
        }

        $content = new stdClass();
        $content->text = '';
       
        $users = $DB->get_records_sql("
            SELECT u.id, u.firstname, u.lastname
            FROM {mdl_user} u
            JOIN {mdl_logstore_standard_log} l ON l.userid = u.id
            WHERE l.courseid = :courseid
            GROUP BY u.id
            ORDER BY MAX(l.timecreated) DESC
            LIMIT 5
        ", ['courseid' => $COURSE->id]);

        if (!empty($users)) {
            foreach ($users as $user) {
                $content->text .= $OUTPUT->user_picture($user, array('size' => 24));
                $content->text .= fullname($user) . '<br>';
            }
        } else {
            $content->text = get_string('nousers', 'block_last_activity_users');
        }

        $this->content = $content;
        return $this->content;
    }
}

?>