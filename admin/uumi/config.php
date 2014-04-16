<?php
$token = 'ee8c2dbd6926f26cade9a9d307a8665a';

$url = 'http://rsme-insite.co.uk/mymoodle';

// Moodle user Authentication
require_once("../../config.php");
require_once($CFG->libdir.'/adminlib.php');
require_login();
require_capability('moodle/site:config',
get_context_instance(CONTEXT_SYSTEM, SITEID));

?>