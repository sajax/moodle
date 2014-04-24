<?php
require_once 'config.php';
require_once 'includes/webservice.php';
require_once 'includes/array_column.php';
require_once 'includes/functions.php';
require_once 'includes/excelreader.php';
require_once 'includes/log.php';

$ws = new WebService($token, $url, false);
$log = new Log();
$errors = array();

/**
* Get Course ID and Validate
*/
$log_form_data = $log->add('Validating Form and File Upload');
if(isset($_POST['courseid']) && is_valid_id_number($_POST['courseid']))
{
	$courseid = $_POST['courseid'];
	$log_form_data->add_child('Course ID has passed validation.');
}
else
{
	$errors[] = create_error_array(2, 'The course could not be identified and no further action could be taken.');
	$log_form_data->add_child('Error found with Course ID. Execution will be halted.');
}

/**
* Get Nominal Roll Files and Validate
*/
if(isset($_FILES['nominalroll']) && $_FILES['nominalroll']['error'] != UPLOAD_ERR_NO_FILE)
{
	$nominalroll = $_FILES['nominalroll'];
	$log_form_data->add_child('Nominal Roll file has been received.');
}
else
{
	$errors[] = create_error_array(2, 'The Nominal Roll files was missing or was corrupt, please go back and try uploading again.');
	$log_form_data->add_child('Error found with nominal roll, the file is missing or corrupt. Execution will be halted.');
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Importing...</title>

	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="css/master.css" />
</head>
<body>

	<div id="container">
		<?php
			/**
			* All inputs have been gathered, check for errors and begin processing
			*/
			if(!check_for_and_display_errors($errors))
			{
				/**
				* Check for valid nominal roll
				*/
				$log_nominal_roll = $log->add('Loading Nominal Roll...');
				$nr_data = readExcelFile($nominalroll['tmp_name']);
				
				if($nr_data instanceof Exception)
				{
					$log_nominal_roll->add_child('Loading nominal roll failed: ' . $nr_data->getMessage());
					$errors[] = create_error_array(2, 'The nominal roll could not be read by the Excel Reader. The file was possibly corrupted.');
				}
				else
				{
					
					$log_nominal_roll->add_child('Found data for course name: <span class="label label-green">' . $nr_data['coursename'] . '</span>');
					$log_nominal_roll->add_child('Found Course Code: <span class="label label-green">' . $nr_data['coursecode'] . '</span>');
					$log_nominal_roll->add_child('Found Course Group Number: <span class="label label-green">' . $nr_data['coursegroup'] . '</span>');
					$log_nominal_roll_user_count = $log_nominal_roll->add_child('Found ' . count($nr_data['users']) . ' Student Rows');
					
					$log_nominal_roll_user_count->add_child('Verifying found user count with nominal roll total cell');
						
					if(count($nr_data['users']) == $nr_data['coursesize'])
					{
						$log_nominal_roll_user_count->add_child('Found ' . count($nr_data['users']) . ' Students, Expecting ' . $nr_data['coursesize'] . ' <span class="label label-green">Pass</span>');
					}
					else
					{
						$log_nominal_roll_user_count->add_child('Found ' . count($nr_data['users']) . ' Students, Expecting ' . $nr_data['coursesize'] . ' <span class="label label-red">Fail</span>');
						$errors[] = create_error_array(2, 'Expected ' . $nr_data['coursesize'] . ' Students, Found ' . count($nr_data['users']) . '. Please check the Nominal Roll is correct and try again.');
					}
					
					$log_nominal_roll_course_capacity = $log_nominal_roll->add_child('Course capacity is <span class="label label-green">' . $nr_data['coursecapacity'] . '</span>');
					
					if(intval(count($nr_data['users'])) > intval($nr_data['coursecapacity']))
					{
						$log_nominal_roll_course_capacity->add_child('Student count of ' . count($nr_data['users']) .' exceeds the course capacity of ' . $nr_data['coursecapacity'] . '');
						$errors[] = create_error_array(2, 'The number of student entries found exceeds the course capacity. Please check the nominal roll and try again.');
					}
					else
					{
						$log_nominal_roll_course_capacity->add_child('Student count of ' . count($nr_data['users']) .' is under the course capacity of ' . $nr_data['coursecapacity'] . '');
					}
					
					if(!check_for_and_display_errors($errors))
					{
						/**
						* Start Extracting Data
						*/
						$log->add('Extracting Service Numbers from Nominal Roll');
						$nr_usernames = array_column($nr_data['users'], 'serviceno');
            $nr_usernames = array_map('strtolower', $nr_usernames); // Set usernames to lowercase for search
						
						$log_ws_username_search = $log->add('Searching for existing user accounts with matching usernames / Service Numbers');
						$ws_matching_users = $ws->usernamesSearch($nr_usernames);
						$log_ws_username_search->add_child('Checking search results'); 
						
						if(isset($ws_matching_users->exception))
						{
							$log_ws_username_search_exception = $log_ws_username_search->add_child('Web Service request failed');
							$log_ws_username_search_exception->add_child('<span class="label label-red">Exception:</span> ' . $ws_matching_users->exception);
							$log_ws_username_search_exception->add_child('<span class="label label-red">Error Code:</span> ' . $ws_matching_users->errorcode);
							$log_ws_username_search_exception->add_child('<span class="label label-red">Message:</span> ' . $ws_matching_users->message);
							$log_ws_username_search_exception->add_child('<span class="label label-red">Debug Info:</span> ' . $ws_matching_users->debuginfo);
							
							$errors[] = create_error_array(2, 'The web service encountered an exception. This must be fixed by the administrator.');
						}
						if(is_null($ws_matching_users))
						{
							$log_ws_username_search->add_child('The web service did not respond or returned NULL.');
							$errors[] = create_error_array(2, 'The web service did not respond in time. Please try again and report the issue if it reoccurs.');
						}
						
						/**
						* Show some log output about the search results if it's an array
						*/
						if(is_array($ws_matching_users))
						{
							$log_ws_username_search->add_child('Found ' . count($ws_matching_users) . ' / ' . count($nr_usernames) . ' student(s) with pre-existing accounts');
						}
						
						if(!check_for_and_display_errors($errors))
						{
							$log_ws_extract_missing_users = $log->add('Extracting students without existing accounts');
							if(count($nr_data['users']) != count($ws_matching_users))
							{
								// Get all the user details
								$ws_new_user_accounts = $nr_data['users'];
								
								foreach($ws_new_user_accounts as $key => $user)
								{
									$match = 0;
									$i = 0;
									
									while($i < count($ws_matching_users) && $match == 0)
									{
										if($ws_matching_users[$i]->username == strtolower($user['serviceno']))
										{
											$match = 1;
										}
										
										$i++;
									}
									
									if($match == 1)
									{
										// If a match is found remove this one from the array. Leave only those that need creating.
										unset($ws_new_user_accounts[$key]);
									}
								}
								
								$log_ws_extract_missing_users->add_child('Extracted ' . count($ws_new_user_accounts) . ' students to create');
								
								
								
								if(empty($ws_new_user_accounts))
								{
									$errors[] = create_error_array(1, 'Expected more than 1 user to create but found zero. Resuming to enrol existing accounts.');
								}
								
								$log_ws_create_users = $log->add('Creating New User Accounts');
								$ws_created_user_accounts = json_decode($ws->createUsers(createUserWebServicePostData($ws_new_user_accounts)));
								//$ws_created_user_accounts = array();
								if(isset($ws_created_user_accounts->exception))
								{
									$log_ws_create_users_exception = $log_ws_create_users->add_child('Web Service request failed');
									$log_ws_create_users_exception->add_child('<span class="label label-red">Exception:</span> ' . $ws_created_user_accounts->exception);
									$log_ws_create_users_exception->add_child('<span class="label label-red">Error Code:</span> ' . $ws_created_user_accounts->errorcode);
									$log_ws_create_users_exception->add_child('<span class="label label-red">Message:</span> ' . $ws_created_user_accounts->message);
									$log_ws_create_users_exception->add_child('<span class="label label-red">Debug Info:</span> ' . $ws_created_user_accounts->debuginfo);
									
									$errors[] = create_error_array(2, 'The web service encountered an exception. This must be fixed by the administrator.');
								}
								if(is_null($ws_created_user_accounts))
								{
									$log_ws_create_users->add_child('The web service did not respond or returned NULL.');
									$errors[] = create_error_array(2, 'The web service did not respond in time. Please try again and report the issue if it reoccurs.');
								}
								
								/**
								* Show some log output if it's an array
								*/
								if(is_array($ws_created_user_accounts))
								{
									$log_ws_create_users->add_child('Created ' . count($ws_created_user_accounts) . ' user accounts');
								}
							}
							else
							{
								$log->add('Skipping creating accounts as all students have existing accounts');
							}
						
							if(!check_for_and_display_errors($errors))
							{
								$log_combined_user_ids = $log->add('Combining new and existing user accounts details');
								
								$userids = array();
								
								if(!empty($ws_matching_users))
								{
									$log_combined_user_ids->add_child('Merging Existing Users');
									foreach($ws_matching_users as $v)
									{	
										$userids[] = $v->id;
										
									}
								}
								
								if(!empty($ws_created_user_accounts))
								{
									$log_combined_user_ids->add_child('Merging New Users');
									foreach($ws_created_user_accounts as $v)
									{
										$userids[] = $v->id;
									}
								}
								
								$log_combined_user_ids->add_child(count($userids) . ' students to enrol');
								
								$log_ws_enrolments = $log->add('Preparing course enrolments');
								$enrolments = array();
								
								foreach($userids as $userid)
								{
									$enrolment['roleid'] = 5; // STUDENT ID = 5
									$enrolment['userid'] = $userid;
									$enrolment['courseid'] = $courseid;
									
									$enrolments[] = $enrolment;
								}
								
								$log_ws_enrolments->add_child('Prepared ' . count($enrolments) . ' enrolments');
								
								$log_ws_enrolments->add_child('Creating course enrolments');
								$ws_enrol_users = $ws->enrolUsers($enrolments);
								
								if(isset($ws_enrol_users->exception))
								{
									$log_ws_enrolments_exception = $log_ws_enrolments->add_child('Web Service request failed');
									$log_ws_enrolments_exception->add_child('<span class="label label-red">Exception:</span> ' . $ws_enrol_users->exception);
									$log_ws_enrolments_exception->add_child('<span class="label label-red">Error Code:</span> ' . $ws_enrol_users->errorcode);
									$log_ws_enrolments_exception->add_child('<span class="label label-red">Message:</span> ' . $ws_enrol_users->message);
									$log_ws_enrolments_exception->add_child('<span class="label label-red">Debug Info:</span> ' . $ws_enrol_users->debuginfo);
									
									$errors[] = create_error_array(2, 'The web service encountered an exception. This must be fixed by the administrator.');
								}
								
								/**if(is_null($ws_enrol_users))
								{
									$log_ws_enrolments->add_child('The web service did not respond or returned NULL.');
									$errors[] = create_error_array(2, 'The web service did not respond in time. Please try again and report the issue if it reoccurs.');
								}*/

								/**
								* 
								*/
								if(empty($ws_enrol_users))
								{
									$log_ws_enrolments->add_child('Course enrolments created');
								}
								
								$log_ws_course_groups = $log->add('Retrieving course groups');
								$ws_exisiting_course_groups = $ws->getCourseGroups($courseid);
								
								if(isset($ws_exisiting_course_groups->exception))
								{
									$log_ws_course_groups_exception = $log_ws_course_groups->add_child('Web Service request failed');
									$log_ws_course_groups_exception->add_child('<span class="label label-red">Exception:</span> ' . $ws_exisiting_course_groups->exception);
									$log_ws_course_groups_exception->add_child('<span class="label label-red">Error Code:</span> ' . $ws_exisiting_course_groups->errorcode);
									$log_ws_course_groups_exception->add_child('<span class="label label-red">Message:</span> ' . $ws_exisiting_course_groups->message);
									$log_ws_course_groups_exception->add_child('<span class="label label-red">Debug Info:</span> ' . $ws_exisiting_course_groups->debuginfo);
									
									$errors[] = create_error_array(2, 'The web service encountered an exception. This must be fixed by the administrator.');
								}
								
								if(is_null($ws_exisiting_course_groups))
								{
									$log_ws_course_groups->add_child('The web service did not respond or returned NULL.');
									$errors[] = create_error_array(2, 'The web service did not respond in time. Please try again and report the issue if it reoccurs.');
								}
								

								if(!check_for_and_display_errors($errors))
								{
									$log_create_or_match_group = $log->add('Searching for existing <span class="label label-green">' . $nr_data['coursegroup'] . '</span> group');
									$course_group = findMatchingGroup($nr_data['coursegroup'], $ws_exisiting_course_groups);
									
									if(is_null($course_group))
									{
										$log_creating_group = $log_create_or_match_group->add_child('No existing group found, creating new group.');
										$ws_course_group = $ws->createCourseGroup($nr_data['coursegroup'], $courseid);
										
										if(isset($ws_course_group->exception))
										{
											$log_creating_group_exception = $log_creating_group->add_child('Web Service request failed');
											$log_creating_group_exception->add_child('<span class="label label-red">Exception:</span> ' . $ws_course_group->exception);
											$log_creating_group_exception->add_child('<span class="label label-red">Error Code:</span> ' . $ws_course_group->errorcode);
											$log_creating_group_exception->add_child('<span class="label label-red">Message:</span> ' . $ws_course_group->message);
											$log_creating_group_exception->add_child('<span class="label label-red">Debug Info:</span> ' . $ws_course_group->debuginfo);
											
											$errors[] = create_error_array(2, 'The web service encountered an exception. This must be fixed by the administrator.');
										}
										
										if(is_null($ws_course_group))
										{
											$log_creating_group->add_child('The web service did not respond or returned NULL.');
											$errors[] = create_error_array(2, 'The web service did not respond in time. Please try again and report the issue if it reoccurs.');
										}
										
										if(is_array($ws_course_group))
										{
											$course_group = $ws_course_group[0];
											$log_creating_group->add_child('Group ' . $course_group->name . ' added');
										}
									}
									else
									{
										$log_create_or_match_group->add_child('Found existing group <span class="label label-green">' . $course_group->name . '</span>');
									}
									
									if(!check_for_and_display_errors($errors))
									{
										$log_group_membership = $log->add('Preparing group memberships');
										
										$group_memberships = array();
										foreach($userids as $userid)
										{
											$group_memberships[] = array('userid' => $userid, 'groupid' => $course_group->id);
										}
										
										$log_group_membership->add_child(count($group_memberships). ' group memberships to add');
										
										$ws_group_memberships = $ws->addGroupMembers($group_memberships);
										
										if(isset($ws_group_memberships->exception))
										{
											$log_group_membership_exception = $log_group_membership->add_child('Web Service request failed');
											$log_group_membership_exception->add_child('<span class="label label-red">Exception:</span> ' . $ws_group_memberships->exception);
											$log_group_membership_exception->add_child('<span class="label label-red">Error Code:</span> ' . $ws_group_memberships->errorcode);
											$log_group_membership_exception->add_child('<span class="label label-red">Message:</span> ' . $ws_group_memberships->message);
											$log_group_membership_exception->add_child('<span class="label label-red">Debug Info:</span> ' . $ws_group_memberships->debuginfo);
											
											$errors[] = create_error_array(2, 'The web service encountered an exception. This must be fixed by the administrator.');
										}
										
										if(empty($ws_group_memberships))
										{
											$log_group_membership->add_child('Group memberships created');
										}
										
										if(!check_for_and_display_errors($errors))
										{
											$log->add('Import Complete. Creating continue button <span class="label label-green">Done</span>');
											$continue_button = '<a href="view.php?id=' . $courseid . '&groupid=' . $course_group->id . '" class="btn btn-big btn-green">Continue &raquo;</a>';
										}
									}
								}
							}
						}
					}
					
				}
				
			}
		?>
		<div id="controls" class="units-row">
			<div class="unit-20 text-left">
				<a href="index.php" class="btn btn-big">&laquo; Course List</a>
			</div>
			<div class="unit-80 text-right">
				<?php if(isset($continue_button)) { echo $continue_button; } else { ?>
				<a href="view.php?id=<?php echo $courseid; ?>" class="btn btn-big btn-blue">&laquo; Go Back</a>
				<?php } ?>
			</div>
		</div>
		<div class="message message-success">
			<?php echo $log->get_all_entries(); ?>
		</div>
		<div id="controls" class="units-row">
			<div class="unit-20 text-left">
				<a href="index.php" class="btn btn-big">&laquo; Course List</a>
			</div>
			<div class="unit-80 text-right">
				<?php if(isset($continue_button)) { echo $continue_button; } ?>
			</div>
		</div>
	</div>

</body>
</html>