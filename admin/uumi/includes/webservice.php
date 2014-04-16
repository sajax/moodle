<?php
require_once 'curl.php';
class WebService
{
	private $token;
	private $url;
	private $curl;
	
	/**
	* Constructs a WebService Object which uses cURL in the background to request data from Moodles Webservices
	* Uses JSON and requires Moodle 2.2+
	* 
	* @param string 	$token 	The Moodle WebServices token to authenticate with
	* @param string 	$url	The URL of the Moodle installation without a trailing slash
	* @param boolean 	$cache	Whether to use the cache functionality
	*/
	public function __construct($token, $url, $cache){
		$this->token = $token;
		$this->url = $url;
		$this->curl = new curl(array('cache'=>$cache));
	}
	
	/**
	* Returns the token being used for authentication
	*
	* @return string 	The token being used as a string
	*/
	public function getToken()
	{
		return $this->token;
	}
	
	/**
	* Returns a list of all courses in the Moodle installation from core_course_get_courses
	*
	* @return array		Array of course objects
	*/
	public function getCourses()
	{
		return json_decode($this->get('core_course_get_courses'));
	}
	
	/**
	* Returns a list of all users in the given course matching courseid from core_enrol_get_enrolled_users
	*
	* @param	int			Course ID to retrieve the users from
	* @return 	array		Array of course objects
	*/
	public function getCourseUsers($courseid, $options = array())
	{
		return json_decode($this->post('core_enrol_get_enrolled_users', array('courseid' => $courseid, 'options' => $options)));	
	}
	
	/**
	* Creates the users given in the array via core_user_create_users, any duplicate usernames will cause an error
	*
	* @param	array		Array of users containing sub-arrays of user information
	* @return 	array		Array of created user information
	*/
	public function createUsers($users)
	{
		return $this->post('core_user_create_users', array('users' => $users));
	}
	
	/**
	* Searches the service for all the usernames listed via core_user_get_users_by_field
	*
	* @param	array		Array of usernames to check
	* @return 	array		Array of user objects
	*/
	public function usernamesSearch($usernames)
	{
		return json_decode($this->post('core_user_get_users_by_field', array('field' => 'username', 'values' => $usernames)));
	}
	
	/**
	* Enrols the given combinations of courseid and userid via enrol_manual_enrol_users
	*
	* @param	array		Array of courseid and userid arrays
	* @return 	array		Array of user objects
	*/
	public function enrolUsers($enrolments)
	{
		return json_decode($this->post('enrol_manual_enrol_users', array('enrolments' => $enrolments)));
	}
	
	/**
	* Enrols the given combinations of courseid and userid via enrol_manual_enrol_users
	*
	* @param	array		Array of courseid and userid arrays
	* @return 	array		Array of user objects
	*/
	public function unenrolUser($userid, $courseid)
	{
		return json_decode($this->post('enrol_manual_enrol_users', array('enrolments' => array(array('roleid' => 5, 'userid' => $userid, 'courseid' => $courseid, 'timeend' => (time() - (60 * 5)), 'suspend' => 1)))));
	}
	
	/**
	* Gets all the groups in the courseid via core_group_get_course_groups
	*
	* @param	array		The courseid to return the groups from
	* @return 	array		Array of group information
	*/
	public function getCourseGroups($courseid)
	{
		return json_decode($this->get('core_group_get_course_groups', array('courseid' => $courseid)));
	}
	
	/**
	* Creates the given course group
	*
	* @param	array	$groupname		The name of the group to create
	* @param	array	$courseid		The courseid to create the group in
	* @return 	array	The created group information
	*/
	public function createCourseGroup($groupname, $courseid)
	{
		return json_decode($this->post('core_group_create_groups', array('groups' => array(array('courseid' => $courseid, 'name' => $groupname, 'description' => '')))));
	}
	
	/**
	* Adds all the given memberships to Moodle
	*
	* @param	array		GroupID and UserID memberships
	* @return 	
	*/
	public function addGroupMembers($groupMemberships)
	{
		return json_decode($this->post('core_group_add_group_members', array('members' => $groupMemberships)));
	}
	
	/**
	* Creates a GET request for data
	*
	* @param	string	$functionName	The names of the function to call
	* @param 	array	$params			The GET parameters
	* @return 	json		The JSON response
	*/
	private function get($functionName, $params = '')
	{
		return $this->curl->get($this->url . '/webservice/rest/server.php?wstoken=' . $this->token . '&moodlewsrestformat=json&wsfunction=' . $functionName . $this->buildRequestString($params));
	}
	
	/**
	* Creates a POST request for data
	*
	* @param	string	$functionName	The names of the function to call
	* @param 	array	$params			The POST parameters
	* @return 	json		The JSON response
	*/
	private function post($functionName, $params)
	{
		return $this->curl->post($this->url . '/webservice/rest/server.php?wstoken=' . $this->token . '&moodlewsrestformat=json&wsfunction=' . $functionName, $params);
	}
	
	/**
	* Creates a URL string for a GET request
	*
	* @param 	array	$params			The GET parameters to turn into a string
	* @return 	string	The GET parameter string
	*/
	private function buildRequestString($params)
	{
		$output = '';
		if(!empty($params))
		{
			foreach($params as $key => $value)
			{
				$output .= '&' . $key . '=' . $value;
			}
		}
		return $output;
	}
}
