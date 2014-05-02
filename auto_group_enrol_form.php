<?php
defined('MOODLE_INTERNAL') || die;

require_once("$CFG->libdir/formslib.php");

function auto_group_enrol_form(MoodleQuickForm $mform, $page, $course) {
    global $CFG, $USER, $DB;

    $mform->addElement('header','enrol', 'Paramètres');

    $data = array();

    $all_groups_course = groups_get_all_groups($course->id);
    // Group(s) must be created first
    if (count($all_groups_course) == 0) {
      $mform->addElement('static', 'no_group_found', '', get_string('auto_group_enrol_form_no_group_found', 'local_groupautoenrol', (string)$course->id));
    }
    else {

      $instance = false;
      if ( isset($course->id) ) {
        $instance = $DB->get_record('groupautoenrol', array('courseid' => $course->id));
      }

      $mform->addElement('checkbox', 'enable_enrol', get_string('auto_group_form_enable_enrol', 'local_groupautoenrol'));
      if ($instance != false) {
        $enable_enrol = $instance->enable_enrol;
      } 
      else {
        $enable_enrol = 0;
      } 
      $mform->setDefault('enable_enrol', $enable_enrol);

      $mform->addElement('checkbox', 'use_groupslist', get_string('auto_group_form_usegroupslist', 'local_groupautoenrol'));
      if ($instance != false) {
        $use_groupslist = $instance->use_groupslist;
      } 
      else {
        $use_groupslist = 0;
      } 
      $mform->setDefault('use_groupslist', $use_groupslist);
      $mform->disabledIf('use_groupslist', 'enable_enrol');

      $FIELDS = array();
      foreach ($all_groups_course as $group) {
        $FIELDS[$group->id] = $group->name;
      }
      $select = $mform->addElement('select', 'groupslist', get_string('auto_group_form_groupslist', 'local_groupautoenrol'), $FIELDS);
      $select->setMultiple(true);
      $mform->disabledIf('groupslist', 'enable_enrol');
      $mform->disabledIf('groupslist', 'use_groupslist');
      if ($instance != false) {
        $mform->setDefault('groupslist', explode(",",$instance->groupslist));
      }

    }
}

