<?php
defined('MOODLE_INTERNAL') || die;

require_once("$CFG->libdir/formslib.php");
require_once('./auto_group_enrol_form.php');

class manage_auto_group_enrol_form extends moodleform {

    function definition() {
        global $USER, $CFG, $DB;

        $mform          = $this->_form;
        $page           = $this->_customdata['page'];
        $course         = $this->_customdata['course'];
        $context        = $this->_customdata['context'];

        auto_group_enrol_form($mform, $page, $course, $context);

        $this->add_action_buttons();
    }
}
