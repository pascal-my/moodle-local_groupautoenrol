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
 * Setup "Automatics groups" link in Courseadmin->users menu
 * 
 * Test TravisCI
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
            array('id' => $context->instanceid)
            );
            $root = $settings->find('courseadmin', navigation_node::TYPE_COURSE);
            $usermenu = $root->get('users');
            $usermenu->add(get_string('menu_auto_groups', 'local_groupautoenrol'), $url);
        }
    }
}
