<?php
// This file is part of".
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//.
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <http://www.gnu.org/licenses/>.
    
defined('MOODLE_INTERNAL') || die;

require_once("$CFG->libdir/formslib.php");

function auto_group_enrol_form(MoodleQuickForm $mform, $page, $course) {
    global $CFG, $USER, $DB;

    $mform->addElement('header', 'enrol', 'Paramètres');

    $data = array();

    $allgroupscourse = groups_get_all_groups($course->id);
    // Group(s) must be created first.
    if (count($allgroupscourse) == 0) {
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
    
        $fields = array();
        foreach ($allgroupscourse as $group) {
            $fields[$group->id] = $group->name;
        }
        $select = $mform->addElement('select', 'groupslist', get_string('auto_group_form_groupslist', 'local_groupautoenrol'), $fields);
        $select->setMultiple(true);
        $mform->disabledIf('groupslist', 'enable_enrol');
        $mform->disabledIf('groupslist', 'use_groupslist');
        if ($instance != false) {
            $mform->setDefault('groupslist', explode(",", $instance->groupslist));
        }
    }
}

