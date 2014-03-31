<?php
/**
 * RSME Login Assistant
 *
 * @package    standalone
 * @copyright  2014 Babcock International
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 
require('../config.php');
require_once('lib.php');


$context = context_system::instance();
$PAGE->set_url("$CFG->httpswwwroot/login/login_assistant.php");
$PAGE->set_context($context);
$PAGE->set_pagelayout('login');

// Define variables used in page
$site = get_site();

$PAGE->navbar->add('Login Assistant');

$PAGE->set_title("$site->fullname: Login Assistant");
$PAGE->set_heading("$site->fullname");

require_once('login_assistant_form.php');

$step = optional_param('step', 'step1', PARAM_TEXT);

echo $OUTPUT->header();
echo $OUTPUT->heading('Login Assistant');


switch ($step) {
  case "step1":
    $frm = new login_assistant_form_step1();
    echo $OUTPUT->box('To start please enter below the password provided on the letter/email you have received from us or the password you have been given that does not work.');
    echo $frm->display();
    break;
  case "step2":
    $password = required_param('letterpassword', PARAM_TEXT);
    if ($password == 'password')
    {
      echo $OUTPUT->box('Please confirm the start/assembly date of your course');
      $frm = new login_assistant_form_step2();
      echo $frm->display();
    }
    else
    {
      echo $OUTPUT->box('Sorry, we cannot continue with the password reset. Please contact the Service Support Desk on the information below for further assistance.');
    }
    break;
  case "step3":
    $start_date = required_param_array('course_start_date', PARAM_INT);
    $start_date_stamp = mktime(12, 0, 0, $start_date['month'], $start_date['day'], $start_date['year']);
    $cutoff_date = time() + (6 * 7 * 24 * 60 * 60); // 6 weeks, 7 days, 24 hours, 60 minutes, 60 seconds
    $leeway = (7 * 24 * 60 * 60); // 1 week
    
    $cutoff_remainder = $cutoff_date - $start_date_stamp;
    
    $leeway = $leeway + $cutoff_remainder;
    
    // If cutoff_remainder is negative, still time remaining for loading. Add leeway and check it's still negative
    if ($cutoff_remainder < 0 && $cutoff_remainder + $leeway < 0)
    {
      echo $OUTPUT->box('Your course start date is too far in the future. Your course is unlikely to ready or your account may not exist yet. User accounts and course are finalised 5 to 6 weeks before the course start date. Please try again later.');
    }
    
    if($cutoff_remainder > 0 || $cutoff_remainder + $leeway > 0)
    {
      echo $OUTPUT->box('Finally, please enter your Service Number and Surname into the boxes below. If your course and account are ready your password will be reset for you.');
      $frm = new login_assistant_form_step3();
      echo $frm->display();
    }
    break;
  case "step4":
    $servicenumber = required_param('servicenumber', PARAM_TEXT);
    $surname = required_param('surname', PARAM_TEXT);
    
    $USER = $DB->get_record('user', array('username'=>$servicenumber));

    // Found user and users lastname matches the given surname
    if ($USER && strtolower($USER->lastname) == strtolower($surname))
    {
      $userauth = get_auth_plugin($USER->auth);
      
      if (!$userauth->can_change_password()) {
        print_error('nopasswordchange', 'auth');
      }
      
      if (!$userauth->user_update_password($USER, $data->newpassword1)) {
        print_error('errorpasswordupdate', 'auth');
      }
      echo 'Found';
    }
    else
    {
      $OUTPUT->box('We could not find details matching the information you supplied. Please contact the Service Support Desk for further assistance.', 'alert alert-danger');
    }
    
    break;
}

echo $OUTPUT->footer();