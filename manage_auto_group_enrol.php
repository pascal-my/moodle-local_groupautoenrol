<?php

/**
 * @file Params page for auto group enrollment as defined by Comete
 *
 * @category local
 * @package  local_groupautoenrol
 * @author   Comete <comete@u-paris10.fr>
 */

require_once '../../config.php';
require_once './manage_auto_group_enrol_form.php';

$id = required_param('id', PARAM_INT);

$url = new moodle_url("$CFG->wwwroot/local/groupautoenrol/manage_auto_group_enrol.php", array('id' => $id) );
$PAGE->set_url($url);

// TODO we need to gracefully shutdown if course not found
$course   = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);
$context  = context_course::instance($course->id);

require_login($course);

$coursecontext  = context_course::instance($course->id);
require_capability('moodle/course:update', $coursecontext);

$PAGE->set_context($context);
$PAGE->set_pagelayout('admin');

$PAGE->set_heading($course->fullname);

$form = new manage_auto_group_enrol_form($url, array('course' => $course, 'page' => $PAGE, 'context' => $context));

if ($form->is_cancelled()) {
    redirect( new moodle_url("$CFG->wwwroot/course/view.php", array('id' => $course->id) ) );
}
else if ( $data = $form->get_data() ) {

  // Checkbox cleaning : if checkbox are unchecked, the value is empty or NULL, this is not compatible with "tinyint" in database.
  if (!isset($data->enable_enrol) || ($data->enable_enrol == NULL) || ($data->enable_enrol == ""))
    $data->enable_enrol = 0;
  if (!isset($data->use_groupslist) || ($data->use_groupslist == NULL) || ($data->use_groupslist == ""))
    $data->use_groupslist = 0;

  $groupautoenrol = new stdClass();
  $groupautoenrol->courseid = $course->id;
  $groupautoenrol->enable_enrol = $data->enable_enrol;
  $groupautoenrol->use_groupslist = $data->use_groupslist;
  if (isset($data->groupslist)) // Could be not set
    $groupautoenrol->groupslist = implode(",", $data->groupslist);

  $record = $DB->get_record('groupautoenrol', array('courseid' => $course->id), 'id');
  if (!$record) {
      $DB->insert_record('groupautoenrol', $groupautoenrol, false);
  }
  else {
      $groupautoenrol->id = $record->id;
      $DB->update_record('groupautoenrol', $groupautoenrol);
  }
  redirect( new moodle_url("$CFG->wwwroot/local/groupautoenrol/manage_auto_group_enrol.php", array('id' => $course->id) ));
}


echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('auto_group_form_page_title', 'local_groupautoenrol'));

$form->display();

echo $OUTPUT->footer();

