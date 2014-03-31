<?php
defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');

class login_assistant_form_step1 extends moodleform {
  /**
   * Define the forgot password form.
   */
  function definition() {
      $mform    = $this->_form;
      $mform->setDisableShortforms(true);

      $mform->addElement('password', 'letterpassword', 'Password');
      $mform->setType('letterpassword', PARAM_RAW);
      
      $mform->addElement('hidden', 'step', 'step2');
      $mform->setType('step', PARAM_RAW);
      
      $submitlabel = 'Next';
      $mform->addElement('submit', 'nextbutton', $submitlabel);
  }
}

class login_assistant_form_step2 extends moodleform {
  /**
   * Define the forgot password form.
   */
  function definition() {
      $mform    = $this->_form;
      $mform->setDisableShortforms(true);
      
      $mform->addElement('date_selector', 'course_start_date', 'Course Start Date');
      
      $mform->addElement('hidden', 'step', 'step3');
      $mform->setType('step', PARAM_RAW);
      
      $submitlabel = 'Next';
      $mform->addElement('submit', 'nextbutton', $submitlabel);
  }
}

class login_assistant_form_step3 extends moodleform {
  /**
   * Define the forgot password form.
   */
  function definition() {
      $mform    = $this->_form;
      $mform->setDisableShortforms(true);
      
      $mform->addElement('text', 'servicenumber', 'Service Number');
      $mform->setType('servicenumber', PARAM_RAW);
      
      $mform->addElement('text', 'surname', 'Surname');
      $mform->setType('surname', PARAM_TEXT);
      
      $mform->addElement('hidden', 'step', 'step4');
      $mform->setType('step', PARAM_RAW);
      
      $submitlabel = 'Next';
      $mform->addElement('submit', 'nextbutton', $submitlabel);
  }
}