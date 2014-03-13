<?php
/**
 * RSME Moodle Bootstrap Theme
 *
 * @package   theme_rsme_standard_v3
 * @copyright 2013 Babcock International
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    // Custom CSS file.
    $name = 'theme_rsme_standard_v3/customcss';
    $title = get_string('customcss', 'theme_rsme_standard_v3');
    $description = get_string('customcssdesc', 'theme_rsme_standard_v3');
    $default = '';
    $setting = new admin_setting_configtextarea($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);
	
    // First Login Message
    $name = 'theme_rsme_standard_v3/firstlogin';
    $title = get_string('firstlogin', 'theme_rsme_standard_v3');
    $description = get_string('firstlogindesc', 'theme_rsme_standard_v3');
    $default = '';
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    //$setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);
	
    // Distant Past Login Message
    $name = 'theme_rsme_standard_v3/distantpastlogin';
    $title = get_string('distantpastlogin', 'theme_rsme_standard_v3');
    $description = get_string('distantpastlogindesc', 'theme_rsme_standard_v3');
    $default = '';
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    //$setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);
	
    // Generic Announcement
    $name = 'theme_rsme_standard_v3/genericannouncement';
    $title = get_string('genericannouncement', 'theme_rsme_standard_v3');
    $description = get_string('genericannouncementdesc', 'theme_rsme_standard_v3');
    $default = '';
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    //$setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);
	
    // Generic Announcement Link
    $name = 'theme_rsme_standard_v3/genericannouncementlink';
    $title = get_string('genericannouncementlink', 'theme_rsme_standard_v3');
    $description = get_string('genericannouncementlinkdesc', 'theme_rsme_standard_v3');
    $default = '';
	$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_URL);
    //$setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);
	
    // Maintenance Announcement
    $name = 'theme_rsme_standard_v3/maintenanceannouncement';
    $title = get_string('maintenanceannouncement', 'theme_rsme_standard_v3');
    $description = get_string('maintenanceannouncementdesc', 'theme_rsme_standard_v3');
    $default = '';
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    //$setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);
	
    // Generic Announcement Link
    $name = 'theme_rsme_standard_v3/maintenanceannouncementlink';
    $title = get_string('maintenanceannouncementlink', 'theme_rsme_standard_v3');
    $description = get_string('maintenanceannouncementlinkdesc', 'theme_rsme_standard_v3');
    $default = '';
	$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_URL);
    //$setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);
	
	
    // Enable Piwik Visitor Tracking
    $name = 'theme_rsme_standard_v3/enablevisitorstats';
    $title = get_string('enablevisitorstats', 'theme_rsme_standard_v3');
    $description = get_string('enablevisitorstatsdesc', 'theme_rsme_standard_v3');
    $default = 0;
	$setting = new admin_setting_configcheckbox($name, $title, $description, $default);
    //$setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);
	
    // Piwik Stats URL
    $name = 'theme_rsme_standard_v3/visitorstatsurl';
    $title = get_string('visitorstatsurl', 'theme_rsme_standard_v3');
    $description = get_string('visitorstatsurldesc', 'theme_rsme_standard_v3');
    $default = '';
	$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
    //$setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Piwik Stats Site ID
    $name = 'theme_rsme_standard_v3/visitorstatsid';
    $title = get_string('visitorstatsid', 'theme_rsme_standard_v3');
    $description = get_string('visitorstatsiddesc', 'theme_rsme_standard_v3');
    $default = '';
	$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_INT);
    //$setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);
}
