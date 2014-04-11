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

$strlogin     = get_string('login');

$PAGE->navbar->add($strlogin, get_login_url());
$PAGE->navbar->add('Login Assistant');

$PAGE->set_title("$site->fullname: Login Assistant");
$PAGE->set_heading("$site->fullname");

require_once('login_assistant_form.php');

$step = optional_param('step', 'step1', PARAM_TEXT);

// if you are logged in then you shouldn't be here!
if (isloggedin() and !isguestuser()) {
    redirect($CFG->wwwroot.'/index.php', get_string('loginalready'), 5);
}

echo $OUTPUT->header();
echo $OUTPUT->heading('Login Assistant');

// Build SSD Info Page, This is very lazy
$ssd_info = '<h3>Service Support Desk</h3>' . $OUTPUT->box('Please contact the Service Support Desk for further assistance:', 'alert alert-info');
$ssd_info .= '<dl class="dl-horizontal"><dt>Civ:</dt><dd>01634 82 99 44</dd><dt>Mil:</dt><dd>961 44 99 44</dd><dt>Email</dt><dd>rsme.ssd@hts.army.mod.uk</dd></dl>';

$return_to_login = $OUTPUT->continue_button(new moodle_url("$CFG->httpswwwroot/login/index.php"));

switch ($step) {
  case "step1":
    $frm = new login_assistant_form_step1();
    echo $OUTPUT->box('<p>If you have received your <strong>RSME or Loading Letter</strong> but have been unable to login using the password provided, you will be able to reset your password using the following process.</p><p>You will need the Serial Number, Password and Course Start Date provided on the RSME/Loading letter you have received. Please ensure you enter the serial and password exactly as they appear in the letter.</p>');
    echo $frm->display();
    break;
  case "step2":
    $password = required_param('letterpassword', PARAM_TEXT);
    $serial_course = required_param('serial-course', PARAM_TEXT);
    $serial_year = required_param('serial-year', PARAM_TEXT);
    $serial_class = required_param('serial-class', PARAM_TEXT);
    $servicenumber = required_param('servicenumber', PARAM_TEXT);
    $surname = required_param('surname', PARAM_TEXT);
    
    $course = $DB->get_record('course', array('idnumber'=>$serial_course));
    $user = $DB->get_record('user', array('auth'=>'manual', 'username'=>$servicenumber, 'mnethostid'=>$CFG->mnet_localhost_id));

    if (!$course or !$user or $password != $CFG->login_assistant_password)
    {
      echo $OUTPUT->box('We were unable to find a match for either the Serial Number or your Service Number. Please contact the Service Support Desk for further assistance.', 'alert alert-error');
      echo $ssd_info;
      echo $return_to_login;
    }
    else
    {
      $course_context = context_course::instance($course->id);
      $is_enrolled = is_enrolled($course_context, $user);
      
      
      // Check we found a user, check their lastnames match in lowercase and prevent any site admins from being reset this way
      if ($user && strtolower($user->lastname) == strtolower($surname) && !is_siteadmin($user) && !$user->deleted && $is_enrolled) {
        $new_password = generate_password();
        $hashedpassword = hash_internal_user_password($new_password);
        // Lets update the user
        $DB->set_field('user', 'password', $hashedpassword, array('id'=>$user->id));
        set_user_preference('auth_forcepasswordchange', 1, $user);
        
        echo $OUTPUT->box('Please now try and login using the password provided below. Copy and paste the password provided below, be sure to include any punctuation as well.', 'alert alert-success');
        echo '<div style="width:25%; font-size:1.7em; background:lightyellow; border:1px solid black; padding:1em; text-align:center; margin:1em auto;">' . $new_password . '</div>';
        unset($new_password);
        
        add_to_log(SITEID, 'login', 'password claim', '/user/view.php?id=' . $user->id, $user->username);
        
        echo $return_to_login;
      }
      else
      {
        echo $OUTPUT->box('Sorry, we were unable to reset your password. For further assistance please contact the Service Support Desk.', 'alert alert-error');
        echo $ssd_info;
        echo $return_to_login;
      }
    }
    break;
}

echo $OUTPUT->footer();