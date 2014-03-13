<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Essential theme with the underlying Bootstrap theme.
 *
 * @package    theme
 * @subpackage Essential
 * @author     Julian (@moodleman) Ridden
 * @author     Based on code originally written by G J Bernard, Mary Evans, Bas Brands, Stuart Lamour and David Scotson.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 
 class theme_rsme_standard_v3_core_renderer extends theme_bootstrapbase_core_renderer {
 
 	/*
     * This renders a notification message.
     * Uses bootstrap compatible html.
     */
    public function notification($message, $classes = 'notifyproblem') {
        $message = clean_text($message);
        $type = '';

        if ($classes == 'notifyproblem') {
            $type = 'alert alert-error';
        }
        if ($classes == 'notifysuccess') {
            $type = 'alert alert-success';
        }
        if ($classes == 'notifymessage') {
            $type = 'alert alert-info';
        }
        if ($classes == 'redirectmessage') {
            $type = 'alert alert-block alert-info';
        }
        return "<div class=\"$type\">$message</div>";
    }
	
	public function render_user_card()
	{
		global $USER;
		
		$output = '';
		
		if(isloggedin())
		{
			/* Create Personal User Card */
			
			$userImage = $this->user_picture($USER, array('size' => '80'));
			$lastLogin = $USER->lastlogin;
			
			$lastSeenString = ($lastLogin > 0 ? date('jS F Y', $USER->lastlogin) : get_string('never'));
			
			$welcome = html_writer::tag('p', $USER->firstname . ' ' . $USER->lastname, array('class' => 'welcome'));
			$lastSeen = html_writer::tag('p', get_string('lastaccess') . ': ' . $lastSeenString, array());
			
			$myProfile = html_writer::tag('a', html_writer::empty_tag('img', array('src' => $this->pix_url('icons/profile', 'theme'))) . ' ' . get_string('myprofile'), array('href' => new moodle_url('/user/edit.php?id=' . $USER->id)));
			$logout = html_writer::tag('a', html_writer::empty_tag('img', array('src' => $this->pix_url('icons/logout', 'theme'))) . ' ' . get_string('logout'), array('href' => new moodle_url('/login/logout.php')));
			
			$links = html_writer::tag('div', $myProfile, array('class' => 'card-button'));
			$links .= html_writer::tag('div', $logout, array('class' => 'card-button'));
			
			$output .= html_writer::tag('div', $userImage, array('class' => 'user-image'));
			$output .= html_writer::tag('div', $welcome . $lastSeen . $links, array('class' => 'user-actions'));
		}
		else
		{
			$login = html_writer::tag('a', html_writer::empty_tag('img', array('src' => $this->pix_url('icons/login', 'theme'))) . ' ' . get_string('login'), array('href' => new moodle_url('/login/index.php')));
			
			$password = html_writer::tag('a', html_writer::empty_tag('img', array('src' => $this->pix_url('icons/forgotten', 'theme'))) . ' ' . get_string('passwordforgotten'), array('href' => new moodle_url('/login/forgot_password.php')));
			
			$message = html_writer::tag('p', get_string('loginreset', 'theme_rsme_standard_v3'), array());
			
			$links = html_writer::tag('div', $login, array('class' => 'card-button'));
			$links .= html_writer::tag('div', $password, array('class' => 'card-button'));
			
			$output .= html_writer::tag('div', get_string('loggedout', 'theme_rsme_standard_v3'), array('class' => 'welcome'));
			$output .= html_writer::tag('div', $message . $links, array());
		}
		
		return $output;
	}
	
	public function render_notification_boxes()
	{
		$boxes = $this->render_generic_notification();
		$boxes .= $this->render_first_login_notification();
		$boxes .= $this->render_maintenance_notification();
		$boxes .= $this->render_distant_past_login_notification();
		
		return html_writer::tag('div', $boxes, array('class' => 'row-fluid'));
	}
	
	private function render_generic_notification()
	{
		$boxStyle = 'notification-info';
		$output = '';
		
		$hasGenericAnnouncement = (empty($this->page->theme->settings->genericannouncement)) ? false : $this->page->theme->settings->genericannouncement;
		
		if($hasGenericAnnouncement)
		{
			// Build Button
			$hasGenericAnnouncementLink = (empty($this->page->theme->settings->genericannouncementlink)) ? '' : $this->page->theme->settings->genericannouncementlink;
			
			$buttons = array();
			
			if($hasGenericAnnouncementLink)
			{
				$buttons[] = $this->return_buttons_html(get_string('supplyinfo'), $this->pix_url('icons/more', 'theme'), $hasGenericAnnouncementLink, $boxStyle);
			}
			
			$output = $this->return_notification_box($hasGenericAnnouncement, $buttons, $boxStyle);
		}
		
		return $output;
	}
	
	private function render_first_login_notification()
	{
		global $USER;
		
		$boxStyle = 'notification-success';
		$output = '';
		
		if(isLoggedin())
		{
			
			$hasFirstLoginMessage = (empty($this->page->theme->settings->firstlogin)) ? false : $this->page->theme->settings->firstlogin;
			$hasNotLoggedInBefore = ($USER->lastlogin == 0 && $USER->currentlogin > (time() - (20*60))) ? true : false;
			
			if($hasFirstLoginMessage && $hasNotLoggedInBefore)
			{
				// Build Button
				
				$buttons = array();
				
				$buttons[] = $this->return_buttons_html(get_string('setemail', 'theme_rsme_standard_v3'), $this->pix_url('icons/profile_large', 'theme'), new moodle_url('/user/edit.php?id=' . $USER->id), $boxStyle);
				
				$buttons[] = $this->return_buttons_html(get_string('courses'), $this->pix_url('icons/courses_large', 'theme'), new moodle_url('/my'), $boxStyle);
				
				$output = $this->return_notification_box($hasFirstLoginMessage, $buttons, $boxStyle);
			}
		}
		
		return $output;
	}
	
	private function render_distant_past_login_notification()
	{
		global $USER;
		
		$boxStyle = 'notification-success';
		$output = '';
		
		if(isLoggedin())
		{
			$hasDistantPastLoginMessage = (empty($this->page->theme->settings->distantpastlogin)) ? false : $this->page->theme->settings->distantpastlogin;
			$hasLoggedInBefore = ($USER->lastlogin != 0 && $USER->lastlogin < (time() - (90*24*60*60))) ? true : false;
			
			if($hasDistantPastLoginMessage && $hasLoggedInBefore)
			{
				// Build Button
				$buttons = array();
				
				$buttons[] = $this->return_buttons_html(get_string('checkemail', 'theme_rsme_standard_v3'), $this->pix_url('icons/profile_large', 'theme'), new moodle_url('/user/edit.php?id=' . $USER->id), $boxStyle);
				
				$buttons[] = $this->return_buttons_html(get_string('courses'), $this->pix_url('icons/courses_large', 'theme'), new moodle_url('/my'), $boxStyle);
				
				$output = $this->return_notification_box($hasDistantPastLoginMessage, $buttons, $boxStyle);
			}
		}
		
		return $output;
	}
	
	private function render_maintenance_notification()
	{
		$boxStyle = 'notification-warning';
		$output = '';
		
		$hasMaintenanceAnnouncement = (empty($this->page->theme->settings->maintenanceannouncement)) ? false : $this->page->theme->settings->maintenanceannouncement;
		
		if($hasMaintenanceAnnouncement)
		{
			// Build Button
			$hasMaintenanceAnnouncementLink = (empty($this->page->theme->settings->maintenanceannouncementlink)) ? '' : $this->page->theme->settings->maintenanceannouncementlink;
			
			$buttons = array();
			
			if($hasMaintenanceAnnouncementLink)
			{
				$buttons[] = $this->return_buttons_html(get_string('supplyinfo'), $this->pix_url('icons/more', 'theme'), $hasMaintenanceAnnouncementLink, $boxStyle);
			}
			
			$output = $this->return_notification_box($hasMaintenanceAnnouncement, $buttons, $boxStyle);
		}
		
		return $output;
	}
	
	private function return_buttons_html($text, $icon, $url, $style)
	{
		return html_writer::tag('div', html_writer::tag('a', html_writer::empty_tag('img', array('src' => $icon)) . html_writer::tag('span', $text, array()), array('href' => $url, 'class' => 'notification-link-icon ' . $style)), array('class' => 'span2'));
	}
	
	private function return_notification_box($message, $buttons, $style)
	{		
		$buttonCount = count($buttons);
		$buttonlinks = '';
		foreach($buttons as $button)
		{
			$buttonlinks .= $button;
		}
		
		$spanWidth = 12 - ($buttonCount * 2);
		
		if($spanWidth < 1)
		{
			die('Critical Error: Too Many Buttons');
		}
		
		$content = html_writer::tag('div', $message, array('class' => 'span' . $spanWidth));
		
		return html_writer::tag('div', $content . $buttonlinks, array('class' => 'notification-block clearfix ' . $style));
	}
		
    protected function render_custom_menu(custom_menu $menu) {
    	/*
    	* This code replaces adds the current enrolled
    	* courses to the custommenu.
    	*/
    
    	//$hasdisplaymycourses = (empty($this->page->theme->settings->displaymycourses)) ? false : $this->page->theme->settings->displaymycourses;
        $branch = $menu->add('<i class="icon-home icon-white"></i> ' . 'Home', new moodle_url('/index.php'), 'Home', -1000);
		
		if (isloggedin()) {

			/* Get localised strings */
			$branchlabel = ''.get_string('mycourses', 'theme_rsme_standard_v3');
			$branchtitle = get_string('mycourses', 'theme_rsme_standard_v3');

			/* Get Moodle URL and Sort Order */
            $branchurl   = new moodle_url('/my/index.php');
            $branchsort  = -900;
 
			/* Add Menu Branch */
            $branch = $menu->add($branchlabel, $branchurl, $branchtitle, $branchsort);
 			if ($courses = enrol_get_my_courses(NULL, 'fullname ASC')) {
 				foreach ($courses as $course) {
 					if ($course->visible){
 						$branch->add(format_string($course->fullname), new moodle_url('/course/view.php?id='.$course->id), format_string($course->shortname));
 					}
 				}
 			} else {
 				$branch->add(get_string('noenrolments', 'theme_rsme_standard_v3'),new moodle_url('/'),get_string('noenrolments', 'theme_rsme_standard_v3'));
 			}
            
        }
        
        /*
    	* This code replaces adds the My Dashboard
    	* functionality to the custommenu.
    	*/
        //$hasdisplaymydashboard = (empty($this->page->theme->settings->displaymydashboard)) ? false : $this->page->theme->settings->displaymydashboard;
		
        if (isloggedin()) {
            $branchlabel = ''.get_string('mydashboard', 'theme_rsme_standard_v3');
            $branchurl   = new moodle_url('/my/index.php');
            $branchtitle = get_string('mydashboard', 'theme_rsme_standard_v3');
            $branchsort  = -800;
 
            $branch = $menu->add($branchlabel, $branchurl, $branchtitle, $branchsort);
 			$branch->add(get_string('profile'),new moodle_url('/user/profile.php'),'' . get_string('profile'));
 			$branch->add(get_string('pluginname', 'block_calendar_month'),new moodle_url('/calendar/view.php'),get_string('pluginname', 'block_calendar_month'));
 			$branch->add(get_string('pluginname', 'block_messages'),new moodle_url('/message/index.php'),get_string('pluginname', 'block_messages'));
 			$branch->add(get_string('privatefiles', 'block_private_files'),new moodle_url('/user/files.php'),get_string('privatefiles', 'block_private_files'));
 			$branch->add(get_string('logout'),new moodle_url('/login/logout.php'),get_string('logout'));    
        }
 
        return parent::render_custom_menu($menu);
    }
	
	/*
     * This renders the navbar.
     * Uses bootstrap compatible html. This overrides the base renderer to return null when there are no items to show.
     */
    public function navbar() {
        $items = $this->page->navbar->get_items();
		if(!empty($items))
		{
			$breadcrumbs = array();
			foreach ($items as $item) {
				$item->hideicon = true;
				$breadcrumbs[] = $this->render($item);
			}
			$divider = '<span class="divider">/</span>';
			$list_items = '<li>'.join(" $divider</li><li>", $breadcrumbs).'</li>';
			$title = '<span class="accesshide">'.get_string('pagepath').'</span>';
			return $title . "<ul class=\"breadcrumb\">$list_items</ul>";
		}
		else
		{
			return false;
		}
    }
	
	public function edit_button(moodle_url $url) {
        $url->param('sesskey', sesskey());    
        if ($this->page->user_is_editing()) {
            $url->param('edit', 'off');
            $btn = 'btn-danger';
            $title = get_string('turneditingoff');
            $icon = 'icon-off';
        } else {
            $url->param('edit', 'on');
            $btn = 'btn-success';
            $title = get_string('turneditingon');
            $icon = 'icon-edit';
        }
        return html_writer::tag('a', html_writer::start_tag('i', array('class' => $icon.' icon-white')).
               html_writer::end_tag('i') . html_writer::tag('span', $title,array()), array('href' => $url, 'class' => 'btn '.$btn, 'title' => $title));
    }
	
	public function stats_tracking()
	{
		global $USER, $COURSE;
		
		$code = '';
		$trackingEnabled = (empty($this->page->theme->settings->enablevisitorstats)) ? false : $this->page->theme->settings->enablevisitorstats;
		$trackingURL = (empty($this->page->theme->settings->visitorstatsurl)) ? false : $this->page->theme->settings->visitorstatsurl;
		$siteID = (empty($this->page->theme->settings->visitorstatsid)) ? false : $this->page->theme->settings->visitorstatsid;
		
		$trackingSetupComplete = ($trackingEnabled && $trackingURL && $siteID) ? true : false;
		
		
		if($trackingSetupComplete)
		{
			$code .= '<!-- Piwik -->';
			
			$code .= '<script type="text/javascript"> 
  var _paq = _paq || [];';
  
	if(isset($USER->username)) {
		$code .= "_paq.push(['setCustomVariable','1','Username','" . $USER->username . "','visit']);"; 
	} else {
		$code .= "_paq.push(['setCustomVariable','1','Username','Not logged in','visit']);";
	}

	if(isset($COURSE->id)) {
		$code .= "_paq.push(['setCustomVariable','2','Course-ID','" . $COURSE->id . "','page']);";
		$code .= "_paq.push(['setCustomVariable','3','Course-Name','" . $COURSE->fullname . "','page']);";
	}
  
  $code .= '
  _paq.push([\'trackPageView\']);
  _paq.push([\'enableLinkTracking\']);
  (function() {
    var u=(("https:" == document.location.protocol) ? "https" : "http") 
+ "://' . $trackingURL .'//";
    _paq.push([\'setTrackerUrl\', u+\'piwik.php\']);
    _paq.push([\'setSiteId\', ' . $siteID . ']);
    var d=document, g=d.createElement(\'script\'), 
s=d.getElementsByTagName(\'script\')[0]; g.type=\'text/javascript\';
    g.defer=true; g.async=true; g.src=u+\'piwik.js\'; 
s.parentNode.insertBefore(g,s);
  })();
</script>';
			
			// noscript tracking
			$code .= '<noscript><p><img src="http://' . $trackingURL . '/piwik.php?idsite=' . $siteID . '" style="border:0" alt="" /></p></noscript>';
			$code .= '<!-- End Piwik Code -->';
		}
		
		return $code;
	}
}