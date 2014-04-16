<?php
require_once 'config.php';
require_once 'includes/webservice.php';
$cache = false;

if(isset($_GET['cache']))
{
	if($_GET['cache'] == '1')
	{
		$cache = true;
	}
}

$ws = new WebService($token, $url, $cache);

$id = $_GET['id'];
if(isset($_GET['groupid']))
{
	$gid = $_GET['groupid'];
	
	if(!is_numeric($gid))
	{
		die('Invalid GID');
	}
}
else
{
	$gid = 0;
}
if(!is_numeric($id))
{
	die('Invalid ID');
}

if(!isset($_GET['task']))
{
	$task_action = '';
}
else
{
	$task_action = $_GET['task'];
}

/**
* Unassign Requested User
*/
if($task_action == 'remove' && !empty($_POST))
{
	$remove_user_id = $_POST['remove_user_id'];
	if(is_numeric($remove_user_id) && !empty($remove_user_id))
	{
		$remove_user = $ws->unenrolUser($remove_user_id, $id);
		
		if(!isset($remove_user->exception))
		{
			$remove_user_message = 'The users access to the course has been disabled and is queued to be removed from the course shortly.';
		}
		else
		{
			$remove_user_message = 'There was an error when trying to remove the user. Please try again.';
		}
	}
}
	
$enrolled_users = $ws->getCourseUsers($id, array(array('name' => 'groupid', 'value' => $gid), array('name' => 'onlyactive', 'value' => 1)));

$course_name_search = (isset($enrolled_users[0]->enrolledcourses) ? $enrolled_users[0]->enrolledcourses : array()); 

$course_name = findCurrentCourseName($id, $course_name_search);
?>
<!DOCTYPE html>
<html>
<head>
	<title>Title</title>

	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="css/master.css" />

</head>
<body>

	<div id="container">
		<div id="controls" class="units-row">
			<div class="unit-20 text-left">
				<a href="index.php" class="btn btn-big">&laquo; Back</a>
			</div>
			<div class="unit-40 text-left">
				<form action="view.php" method="GET">
				<label>
					Group
					<select id="groupid" name="groupid" class="width-60">
						<option value="0">All</option>
						<?php 
							$groups = $ws->getCourseGroups($id);
							foreach($groups as $group)
							{
								if($gid == $group->id)
								{
									echo '<option value="' . $group->id . '" selected>' . $group->name . '</option>';
								}
								else
								{
									echo '<option value="' . $group->id . '">' . $group->name . '</option>';
								}
							}
						?>
					</select>
					<input type="hidden" name="id" value="<?php echo $id; ?>">
				</label>
				<button class="btn btn-small btn-blue">Change</button>
				</form>
			</div>
			<div class="unit-40 text-right">
				<!--<a href="index.php" class="btn btn-big">Add User</a>-->
				<a id="excelimport" class="btn btn-big btn-green">Excel Import</a>
			</div>
		</div>
		<?php if(isset($remove_user_message)) { ?>
		<div class="message message-error">
		<p><?php echo $remove_user_message; ?></p>
		</div>
		<?php } ?>
		<div id="excelimportform" class="message message-success">
			<form method="post" enctype="multipart/form-data" action="import2.php" class="forms forms-inline">
				<p class="forms-inline">
				<label for="file">
					Select Excel Nominal Roll
					<input type="file" id="nominalroll" name="nominalroll">
				</label>
				<input type="hidden" id="courseid" name="courseid" value="<?php echo $id; ?>">
				<input type="submit" class="btn btn-green" value="Upload">
				<a id="cancelupload" class="btn btn-small btn-red pull-right">Cancel</a>
				</p>
			</form>
		</div>
		<h2><?php echo $course_name; ?></h2>
		<div id="table">
			<table class="width-100 table-striped">
				<thead>
					<tr>
						<th>Username</th>
						<th>Rank</th>
						<th>Name</th>
						<th>Last Access</th>
						<th>Groups</th>
						<th>Options</th>
					</tr>
				</thead>
				<tbody>
					<?php
					
					 foreach($enrolled_users as $key => $value)
					 {
						if(checkStudentRole($value->roles))
						{
							echo '<tr>';
							echo '<td>' . strtoupper($value->username) . '</td>';
							echo '<td>' . getRank($value) . '</td>';
							echo '<td>' . $value->fullname . '</td>';
							echo '<td>' . printLastAccess($value->lastaccess) . '</td>';
							echo '<td>' . printGroupString($value) . '</td>';
							echo '<td>';
							echo '<form action="view.php?task=remove&id=' . $id . '&groupid=' . $gid . '" method="POST" onsubmit="return confirm(\'Are you sure you want to remove ' . addslashes($value->fullname) . '?\');">';
							echo '<input type="hidden" value="' . $value->id . '" name="remove_user_id">';
							echo '<button title="Remove ' . $value->fullname . '" class="btn btn-small btn-orange">Remove</button>';
							echo '</form>';
							echo '</td>';
							echo '</tr>';
						}
					 }
					?>
				</tbody>
			</table>
		</div>
	</div>
</body>
</html>
<?php

function getRank($user)
{
	if(property_exists($user, 'customfields'))
	{
		return $user->customfields[0]->value;
	}
	else
	{
		return '';
	}
}

function getGroup($user)
{
	if(!empty($user->groups))
	{
		return $user->groups[0]->name;
	}
	else
	{
		return '';
	}
}

function printGroupString($user)
{
	if(!empty($user->groups))
	{
		$str = '';
		foreach($user->groups as $group)
		{
			if($str != '')
			{
				$str .= ', ';
			}
			$str .= $group->name;
		}
		return $str;
	}
	else
	{
		return '';
	}
}

function checkStudentRole($roles)
{
	$isAStudent = false;
	foreach($roles as $role)
	{
		if($role->roleid == 5)
		{
			$isAStudent = true;
			break;
		}
	}

	return $isAStudent;
}

function printLastAccess($timestamp)
{
	if($timestamp == 0)
	{
		return 'Never';
	}
	else
	{
		return date("jS M y", $timestamp) . ' (' . floor((time() - $timestamp) / ((60 * 60) * 24)) . ' Days Ago)';
	}
}

function findCurrentCourseName($courseid, $courses)
{
	foreach($courses as $course)
	{
		if($course->id == $courseid)
		{
			return $course->fullname;
			break;
		}
	}
	
	return 'Unable to find course name';
}
?>