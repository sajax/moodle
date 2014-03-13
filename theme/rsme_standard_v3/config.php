<?php
/**
 * RSME Moodle Bootstrap Theme
 *
 * @package   theme_rsme_standard_v3
 * @copyright 2013 Babcock International
 */

$THEME->name = 'rsme_standard_v3';

/////////////////////////////////
// The only thing you need to change in this file when copying it to
// create a new theme is the name above. You also need to change the name
// in version.php and lang/en/theme_rsme_standard_v3.php as well.
//////////////////////////////////
//
$THEME->doctype = 'html5';
$THEME->parents = array('bootstrapbase');
$THEME->sheets = array('custom','style');
$THEME->supportscssoptimisation = false;
$THEME->yuicssmodules = array();

$THEME->editor_sheets = array('editor');

$THEME->plugins_exclude_sheets = array(
    'block' => array(
        'html',
    ),
);

$THEME->rendererfactory = 'theme_overridden_renderer_factory';
$THEME->csspostprocess = 'theme_rsme_standard_v3_process_css';

$THEME->blockrtlmanipulations = array(
    'side-pre' => 'side-post',
    'side-post' => 'side-pre'
);