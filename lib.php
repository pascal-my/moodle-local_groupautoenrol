<?php

function local_groupautoenrol_user_enrolled($eventdata) {
  global $CFG, $USER, $DB;
  require_once($CFG->dirroot.'/group/lib.php');

  $groupautoenrol = $DB->get_record('groupautoenrol', array('courseid' => $eventdata->courseid));

  if (isset($groupautoenrol) && !$groupautoenrol && ($groupautoenrol->enable_enrol == "1")) {

    $enrol = $DB->get_record('enrol', array('id' => $eventdata->enrolid), "roleid");

    if ($groupautoenrol->use_groupslist == "1") {
      // If use_groupslist == 1, we need to check
      // a) if the list is not empty
      if ($groupautoenrol->groupslist != "") {
        $groups_temp = explode(",",$groupautoenrol->groupslist);

        // b) if the listed groups still exists (because when a group is deleted, groupautoenrol table is not updated !)
        $all_groups_course = groups_get_all_groups($eventdata->courseid);
        $groups_to_use = array();
        foreach ($groups_temp as $group) {
          if (isset($all_groups_course[$group]))
            $groups_to_use[] = $all_groups_course[$group];
        }
      }
      else { // Empty array is returned
        $groups_to_use = array();
      }
    }
    else { // If use_groupslist == 0, use all groups course
      $groups_to_use = groups_get_all_groups($eventdata->courseid);
    }

    // Checking if there is at least 1 group
    if (count($groups_to_use) > 0) {

      // Checking if user is not already into theses groups
      $already_member = false;
      foreach ($groups_to_use as $group) {
        if ( groups_is_member($group->id, $eventdata->userid )) {
          $already_member = true;
        }
      } 

      if (!$already_member) {

        if ($groupautoenrol->enrol_method == "1") { //  0 = random, 1 = alpha, 2 = balanced
          foreach ($groups_to_use as $group) {
            $groupname = $group->name;
            if (( $groupname[strlen($groupname)-2] <= $USER->lastname[0] ) 
               && ( $groupname[strlen($groupname)-1] >= $USER->lastname[0] )) {
                groups_add_member($group->id, $eventdata->userid);
                break; // exit foreach (is it working ?)
            }
          }
        }
        else {
          // array_rand return key not value !
          $rand_keys = array_rand($groups_to_use);
          $group2add = $groups_to_use[$rand_keys];
          groups_add_member($group2add, $eventdata->userid);
        }

      }
    }
  }
  return true;
}

/**
 * Setup "Automatics groups" link in Courseadmin->users menu
 *
 * @param array     $settings
 * @param object    $context
 * @return void
 */
function local_groupautoenrol_extends_settings_navigation($settings, $context) {
    global $CFG;

    // If we're viewing course and the course is not the front page.
    if ( ($context instanceof context_course || $context instanceof context_module )  && $context->instanceid > 1) {

      if (has_capability("moodle/course:managegroups", $context)) {
        // Add link to manage automatic group enrolment
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

