<?php
// This file is part of Moodle - http://moodle.org/
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
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <http://www.gnu.org/licenses/>.
/**
 * Main functions
 *
 * @package    local_groupautoenrol
 * @copyright  2016 Pascal Maury - Université Paris Ouest - service COMETE
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 
/**
 * Action when user is enrolled
 *
 * @package         local_groupautoenrol
 * @param object    $eventdata
 * @return bool     true if all ok 
 */
function local_groupautoenrol_user_enrolled($eventdata) {
    global $CFG, $USER, $DB;
    require_once($CFG->dirroot.'/group/lib.php');
    $groupautoenrol = $DB->get_record('groupautoenrol', array('courseid' => $eventdata->courseid));

    if (isset($groupautoenrol) && ($groupautoenrol->enable_enrol == "1")) {

        $enrol = $DB->get_record('enrol', array('id' => $eventdata->enrolid), "roleid");
        if ($groupautoenrol->use_groupslist == "1") {
            // If use_groupslist == 1, we need to check.
            // a) if the list is not empty.
            if ($groupautoenrol->groupslist != "") {
                $groupstemp = explode(",", $groupautoenrol->groupslist);
    
                // b) if the listed groups still exists (because when a group is deleted, groupautoenrol table is not updated !).
                $allgroupscourse = groups_get_all_groups($eventdata->courseid);
                $groupstouse = array();
                foreach ($groupstemp as $group) {
                    if (isset($allgroupscourse[$group])) {
                        $groupstouse[] = $allgroupscourse[$group];
                    }
                }
            } else { // Empty array is returned.
                $groupstouse = array();
            }
        } else { // If use_groupslist == 0, use all groups course.
            $groupstouse = groups_get_all_groups($eventdata->courseid);
        }
    
        // Checking if there is at least 1 group
        if (count($groupstouse) > 0) {
            // Checking if user is not already into theses groups.
            $alreadymember = false;
            foreach ($groupstouse as $group) {
                if ( groups_is_member($group->id, $eventdata->userid )) {
                $alreadymember = true;
                }
            } 
    
            if (!$alreadymember) {
                // array_rand return key not value !
                $randkeys = array_rand($groupstouse);
                $group2add = $groupstouse[$randkeys];
                groups_add_member($group2add, $eventdata->userid);
            }
        }
    }
    return true;
}

/**
 * Setup "Automatics groups" link in Courseadmin->users menu
 *
 * @package         local_groupautoenrol
 * @param array     $settings
 * @param object    $context
 * @return void
 */
function local_groupautoenrol_extend_settings_navigation($settings, $context) {
    global $CFG;
    // If we're viewing course and the course is not the front page.
    if ( ($context instanceof context_course || $context instanceof context_module )  && $context->instanceid > 1) {

        if (has_capability("moodle/course:managegroups", $context)) {
            // Add link to manage automatic group enrolment.
            $url = new moodle_url(
            '/local/groupautoenrol/manage_auto_group_enrol.php',
            array('id'=> $context->instanceid)
            );
            $root = $settings->find('courseadmin', navigation_node::TYPE_COURSE);
            $usermenu = $root->get('users');
            $usermenu->add(get_string('menu_auto_groups', 'local_groupautoenrol'), $url);
        }
    }
}
