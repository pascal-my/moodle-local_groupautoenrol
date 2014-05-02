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

/*    
// Option to finalise : if we want to let teacher choose which role must be concerned, we have to filter here the role list (show only role that teacher can manage)
      $roles = $DB->get_records('role', null, 'sortorder ASC');
      $roles_choices = array();
      foreach ($roles as $role) {
          if ( empty($role->name)) {
              $roles_choices[$role->id] = $role->shortname;
          }
          else {
              $roles_choices[$role->id] = $role->name;
          }
      }
      $select = $mform->addElement('select', 'rolelist', get_string('auto_group_form_rolelist', 'local_groupautoenrol'), $roles_choices, 'size="6"');
      $select->setMultiple(true);
      $mform->disabledIf('rolelist', 'enable_enrol');
      if ($instance != false) {
        $mform->setDefault('rolelist', explode(",",$instance->rolelist));
      }
      else {
        $mform->setDefault('rolelist', "5");
      }
*/
// Old option : ne l'inscrire que s'il n'est pas déjà inscrit dans un cours 
/*      $mform->addElement('checkbox', 'no_check', get_string('auto_group_form_no_check', 'local_groupautoenrol'));
      if ($instance != false) {
        $no_check = $instance->no_check;
      } 
      else {
        $no_check = 0;
      } 
      $mform->setDefault('no_check', $no_check);
      $mform->disabledIf('no_check', 'enable_enrol');*/

// Option not ready : use pattern to select groups (alternative to groups list)
/*      $mform->addElement('text', 'pattern', get_string('auto_group_form_pattern', 'local_groupautoenrol'), 'maxlength="100" size="25" ');
      if ($instance != false) {
        $pattern = $instance->pattern;
        $mform->setDefault('pattern', $pattern);
      } 
      $mform->disabledIf('pattern', 'enable_enrol');*/

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

      $METHODS_TYPES = array("0" => get_string('auto_group_form_rand_method', 'local_groupautoenrol'), "1" => get_string('auto_group_form_alpha_method', 'local_groupautoenrol'), "2" => get_string('auto_group_form_fill_method', 'local_groupautoenrol'));
      $mform->addElement('select', 'enrol_method', get_string('auto_group_form_enrol_method', 'local_groupautoenrol'), $METHODS_TYPES);
      if ($instance != false) {
        $enrol_method = $instance->enrol_method;
      } 
      else {
        $enrol_method = 0;
      } 
      $mform->setDefault('enrol_method', $enrol_method);
      $mform->disabledIf('enrol_method', 'enable_enrol');

      $FIELDS = array("0" => "Nom","1" => "Prénom","2" => "Numéro d'identification","3" => "Adresse de courriel","4" => "Ville","5" => "Pays","6" => "Status principal", "7" => "Département");
      $mform->addElement('select', 'profile_field', get_string('auto_group_form_alpha_field', 'local_groupautoenrol'), $FIELDS);

      if ($instance != false) {
        $profile_field = $instance->profile_field;
      } 
      else {
        $profile_field = 0;
      } 
      $mform->setDefault('profile_field', $profile_field);
      $mform->disabledIf('profile_field', 'enable_enrol');
      $mform->disabledIf('profile_field', 'enrol_method', 'neq', 1);

      $mform->addElement('text', 'balises', get_string('auto_group_form_balises', 'local_groupautoenrol'), array('maxlength'=>'10', 'size'=>'10'));
      $mform->setType('balises', PARAM_TEXT);
      if ($instance != false) {
        $balises = $instance->balises;
      } 
      else {
        $balises = "[-@@-]";
      } 
      $mform->setDefault('balises', $balises);
      $mform->disabledIf('balises', 'enable_enrol');
      $mform->disabledIf('balises', 'enrol_method', 'neq', 1);
    }
}

