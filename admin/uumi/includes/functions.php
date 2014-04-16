<?php
/**
* Takes an array of user objects and builds a web service array
*
* @param string	$users		Array of User Objects
* @return array	Array in format expected for Moodle Web Services
*/
function createUserWebServicePostData($users)
{
	$accounts = array();
	foreach ($users as $user)
	{
		$account = new stdClass();
		$account->idnumber = $user['serviceno'];
		$account->username = strtolower($user['serviceno']);
		$account->firstname = $user['forename'];
		$account->lastname = $user['surname'];
		$account->password = 'changeme';
		$account->email = date(time()) . $user['serviceno'] . '@rsme-insite.co.uk';
		
		$account->customfields = array(
			array('type' => 'title', 'value' => $user['rank']));
		
		$accounts[] = $account;
		
	}
	
	return $accounts;
}

/**
* 
*
* @param string	$group
* @param array  $groups		
* @return Object	
*/
function findMatchingGroup($group, $groups)
{
	$groupObj = null;
	$i = 0;
	while($groupObj == null && $i < count($groups))
	{
		if($groups[$i]->name == $group)
		{
			$groupObj = $groups[$i];
		}
		$i++;
	}
	return $groupObj;
}

/**
* Validate input as a valid ID Number
*
* @param string	$id		The ID String to validate as an ID Number
* @return boolean	Returns True if valid
*/
function is_valid_id_number($id)
{
	if(is_numeric($id) && $id > 0)
	{
		return true;
	}
	else
	{
		return false;
	}
}

/**
* Creates a simple error array
*
* @param int	$severity	0 = Warning, 1 = Minor, 2 = Major. Any Level 2 will stop processing any further
* @param string	$message	The message to show to the user
* @return array	The created array
*/
function create_error_array($severity, $message)
{
	return array(
		'severity' => $severity,
		'message' => $message
		);
}

/**
* Checks the array for any error arrays. Prints a message if errors are found and changes the display depending upon severity.
*
* @param array	$errors		Arrays of Errors
* @return	boolean	Returns true if execution should end
*/
function check_for_and_display_errors($errors)
{
	$end_execution = false;
	$messages = array();
	
	if(count($errors) > 0)
	{
		foreach($errors as $error)
		{
			$messages[] = $error['message'];
			if($error['severity'] > 1)
			{
				$end_execution = true;
			}
		}

		if($end_execution)
		{
			echo '<div class="message message-error">';
			echo '<p>Sorry, There was an issue with your request that could not be resolved. Please check the messages below or report the issue to the administrator.</p>';
		}
		else
		{
			echo '<div class="message">';
			echo '<p>There was an issue with your request but it has been resolved automatically. Please check below for details.</p>';
		}
		
		echo '<ul>';
		foreach($messages as $message)
		{
			echo '<li>' . $message . '</li>';
		}
		echo '</ul>';
		
		echo '</div>';
		
		return $end_execution;
	}
	return $end_execution;
}


?>