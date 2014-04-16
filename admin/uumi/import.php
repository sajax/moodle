<?php
require_once 'config.php';
require_once 'includes/webservice.php';
require_once 'includes/array_column.php';
$ws = new WebService($token, $url, false);


if(!$_POST)
{
	die('Invalid Request');
}
else
{
	$nominalroll = $_FILES['nominalroll'];
	$courseid = trim($_POST['courseid']);
	
	if($nominalroll['error'] == UPLOAD_ERR_NO_FILE)
	{
		die('Please provide an Excel nominal roll');
	}
	
	if(!is_numeric($courseid) || $courseid < 1)
	{
		die('Invalid Request - Course ID');
	}
	
	require_once 'includes/excelreader.php';
	
	$data = readExcelFile($nominalroll['tmp_name']);
	echo '<br><br><br>';

	
	require_once 'includes/functions.php';
	
	$usernames = array_column($data['users'], 'serviceno');
		
	$exisitingUsers = $ws->usernamesSearch($usernames);
	
	//print_r($exisitingUsers);
	echo '<br><br><br>';
	//print_r($data['users']);

	
	$createdUsers = array(); // Create Array For New User Accounts
	
	// Don't search for matches if we can be sure they all exist anyway
	if(count($data['users']) != count($exisitingUsers))
	{
		$usersToCreate = $data['users'];
		foreach($usersToCreate as $key => $user)
		{
			$match = 0;
			$i = 0;
			while($i < count($exisitingUsers) && $match == 0)
			{
				if($exisitingUsers[$i]->username == $user['serviceno'])
				{
					$match = 1;
				}
				
				$i++;
			}
			
			if($match == 1)
			{
				unset($usersToCreate[$key]);
			}
			//$match = array_walk($exisitingUsers, 'matchUser', $user['serviceno']);
		}
		
		$createdUsersArray = createMoodleUsersArray($usersToCreate);
		$createdUsers = json_decode($ws->createUsers($createdUsersArray));
		print_r($createdUsers); echo '<b>CREATED</b>';
	}
	
	// Prepare Combined List of New and Existing User Accounts IDs
	$userids = array();
	foreach($exisitingUsers as $v)
	{	
		$userids[] = $v->id;
		
	}
	
	foreach($createdUsers as $v)
	{
		$userids[] = $v->id;
	}
	
	$enrolments = array();
	
	foreach($userids as $userid)
	{
		$enrolment['roleid'] = 5; // STUDENT ID = 5
		$enrolment['userid'] = $userid;
		$enrolment['courseid'] = $courseid;
		
		$enrolments[] = $enrolment;
	}
	
	$enrolusers = $ws->enrolUsers($enrolments);
	
	if($enrolusers != null)
	{
		//die('Error Has Occurred');
	}
	
	$courseGroups = $ws->getCourseGroups($courseid);
	
	
	
	// Create Group if ours does not exist
	$courseGroup = matchingGroup($data['coursegroup'], $courseGroups);

	// If NULL, Group Does Not Exist... Create Group
	if(is_null($courseGroup))
	{
		$courseGroup = $ws->createCourseGroup($data['coursegroup'], $courseid);
		$courseGroup = $courseGroup[0];
	}
	
	//print_r($courseGroup);
	
	// Put all the users in the group
	$groupMemberships = array();
	foreach($userids as $userid)
	{
		$groupMemberships[] = array('userid' => $userid, 'groupid' => $courseGroup->id);
	}
	print_r($ws->addGroupMembers($groupMemberships));
	
	echo '<a href="view.php?id=' . $courseid . '">Continue &raquo;</a>';
	//$newUsers = createMoodleUsersArray($data['users']);
	//echo '<br><br><br>';
	//print_r($ws->createUsers($newUsers));
}

function matchingGroup($group, $groups)
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

function matchingsUsers($users)
{	
	$usernames = array();
	foreach($users as $user)
	{
		$usernames[] = array('key' => 'username', 'value' => $user['serviceno']);
	}
	
	return $usernames;
}
?>