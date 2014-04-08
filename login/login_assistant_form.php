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
      
      // Year options, +/- current year
      $year_options = array(date('y') - 1 => date('y') - 1, date('y') => date('y'), date('y') + 1 => date('y') + 1);
      
      $serial = array();
      $serial[] =& $mform->createElement('static', 'serial-pam', '', '03');
      $serial[] =& $mform->createElement('text', 'serial-course', '');
      $serial[] =& $mform->createElement('select', 'serial-year', '', $year_options);
      $serial[] =& $mform->createElement('text', 'serial-class', '');
      $serial[] =& $mform->createElement('static', 'serial-phase', '', '*');
      $mform->addGroup($serial, 'course-serial', 'Serial Number', array(' / '), false);
      
      // Set Types
      $mform->setType('serial-course', PARAM_TEXT);
      $mform->setType('serial-year', PARAM_TEXT);
      $mform->setType('serial-class', PARAM_TEXT);
      
      // Add Rules
      $mform->addRule('course-serial', 'Please complete the full serial', 'required', null, 'client');
      
      $mform->addElement('static', 'serial-help', '', 'The serial in your letter will look similar to <strong>03 /6508/14/001/3</strong>');
      
      $mform->addElement('password', 'letterpassword', 'Letter Password');
      $mform->setType('letterpassword', PARAM_TEXT);
      $mform->addRule('letterpassword', 'Please provide the password on your RSME/Loading Letter.', 'required', null, 'client');
      
      $mform->addElement('text', 'servicenumber', 'Service Number');
      $mform->setType('servicenumber', PARAM_TEXT);
      $mform->addRule('servicenumber', 'Please enter your service number', 'required', null, 'client');
      $mform->addRule('servicenumber', 'Please enter your service number', 'alphanumeric', null, 'client');
      
      $mform->addElement('text', 'surname', 'Surname');
      $mform->setType('surname', PARAM_TEXT);
      $mform->addRule('surname', 'Please enter your full surname, do not include first or middle names', 'required', null, 'client');
      $mform->addRule('surname', 'Please enter your full surname', 'lettersonly', null, 'client');
      
      $mform->addElement('hidden', 'step', 'step2');
      $mform->setType('step', PARAM_ACTION);
      
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
      $mform->addRule('course_start_date', 'Please provide the password on your course acknowledgement letter.', 'required', null, 'client');
      
      $mform->addElement('hidden', 'step', 'step3');
      $mform->setType('step', PARAM_ACTION);
      
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
      $mform->setType('servicenumber', PARAM_TEXT);
      $mform->addRule('servicenumber', 'Please enter your service number', 'required', null, 'client');
      $mform->addRule('servicenumber', 'Please enter your service number', 'alphanumeric', null, 'client');
      
      $mform->addElement('text', 'surname', 'Surname');
      $mform->setType('surname', PARAM_TEXT);
      $mform->addRule('surname', 'Please enter your full surname, do not include first or middle names', 'required', null, 'client');
      $mform->addRule('surname', 'Please enter your full surname', 'lettersonly', null, 'client');
      
      $mform->addElement('hidden', 'step', 'step4');
      $mform->setType('step', PARAM_ACTION);
      
      $submitlabel = 'Next';
      $mform->addElement('submit', 'nextbutton', $submitlabel);
  }
}