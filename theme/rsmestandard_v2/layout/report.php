<?php

$hasheading = ($PAGE->heading);
$hasheading = (empty($PAGE->layout_options['noheader']));
$hasnavbar = (empty($PAGE->layout_options['nonavbar']) && $PAGE->has_navbar());
$hasfooter = (empty($PAGE->layout_options['nofooter']));
$hassidepre = (empty($PAGE->layout_options['noblocks']) && $PAGE->blocks->region_has_content('side-pre', $OUTPUT));
$hassidepost = (empty($PAGE->layout_options['noblocks']) && $PAGE->blocks->region_has_content('side-post', $OUTPUT));
$haslogininfo = (empty($PAGE->layout_options['nologininfo']));

$showsidepre = ($hassidepre && !$PAGE->blocks->region_completely_docked('side-pre', $OUTPUT));
$showsidepost = ($hassidepost && !$PAGE->blocks->region_completely_docked('side-post', $OUTPUT));

$custommenu = $OUTPUT->custom_menu();
$hascustommenu = (empty($PAGE->layout_options['nocustommenu']) && !empty($custommenu));

$bodyclasses = array();
if ($showsidepre && !$showsidepost) {
    $bodyclasses[] = 'side-pre-only';
} else if ($showsidepost && !$showsidepre) {
    $bodyclasses[] = 'side-post-only';
} else if (!$showsidepost && !$showsidepre) {
    $bodyclasses[] = 'content-only';
}
if ($hascustommenu) {
    $bodyclasses[] = 'has_custom_menu';
}

echo $OUTPUT->doctype() ?>
<html <?php echo $OUTPUT->htmlattributes() ?>>
<head>
    <title><?php echo $PAGE->title ?></title>
    <link rel="shortcut icon" href="<?php echo $OUTPUT->pix_url('favicon', 'theme')?>" />
    <?php echo $OUTPUT->standard_head_html() ?>
</head>
<body id="<?php p($PAGE->bodyid) ?>" class="<?php p($PAGE->bodyclasses.' '.join(' ', $bodyclasses)) ?>">
<?php echo $OUTPUT->standard_top_of_body_html() ?>
<div id="page">
	<div id="inner_page">
		<?php if ($hasheading) { ?>
		<div id="page-header">
		<div id="page-header-banner">
			<?php
				if (!empty($PAGE->layout_options['langmenu'])) {
					echo $OUTPUT->lang_menu();
				}
			?>
		</div>
		<?php } ?>
		<?php if ($hascustommenu) { ?>
		<div id="custommenu"><?php echo $custommenu; ?></div>
		<?php } ?>

		<?php if ($hasnavbar) { ?>
            <div class="navbar clearfix">
                <div class="breadcrumb"><?php echo $OUTPUT->navbar(); ?></div>
                <div class="navbutton"> <?php echo $PAGE->button; ?></div>
            </div>
		</div>
        <?php } echo '<!-- HEADING MENU -->' . $PAGE->headingmenu; ?>



	<!-- END OF HEADER -->
		
		
		
		<div id="page-content">
		
				<?php
					if ($haslogininfo) {
						echo $OUTPUT->login_info();
					}
				?>
			<!--<div class="welcome_message">
				<p>Welcome to the RSME ELE. Since this appears to be your first time here. Please take a moment to check your <a href="http://localhost/moodle/user/edit.php?id=19&course=1">profile details</a> and update them if they are incorrect.</p>
				<p>Then please visit your <a href="http://localhost/moodle/my">ELE Home Page</a> to view the courses you are enrolled in and collect your Joining Instructions.</p>
			</div>-->
			<div id="region-main-box">
				<div id="region-post-box">

					<div id="region-main-wrap">
						<div id="region-main">
							<div class="region-content">
								<?php echo $OUTPUT->main_content() ?>
							</div>
						</div>
					</div>

					<?php if ($hassidepre) { ?>
					<div id="region-pre" class="block-region">
						<div class="region-content">
							<?php echo $OUTPUT->blocks_for_region('side-pre') ?>
						</div>
					</div>
					<?php } ?>

					<?php if ($hassidepost) { ?>
					<div id="region-post" class="block-region">
						<div class="region-content">
							<?php echo $OUTPUT->blocks_for_region('side-post') ?>
						</div>
					</div>
					<?php } ?>
				</div>
			</div>
		</div>

	<!-- START OF FOOTER -->
		<?php if ($hasfooter) { ?>
		<div id="page-footer">
			<div class="siteinfo">
				<div class="pagetitle"><?php echo $SITE->fullname; if ($hasheading && $SITE->fullname != $PAGE->heading) { echo ' - ' . $PAGE->heading; } ?></div>
				<div class="helplink"><?php echo page_doc_link(get_string('moodledocslink')) ?></div>
			</div>
			<div class="clearfix"></div>
			<?php
			echo $OUTPUT->login_info();
			echo $OUTPUT->home_link();
			echo $OUTPUT->standard_footer_html();
			?>
		</div>
		<?php } ?>
		<div class="clearfix"></div>
	</div>
</div>
<?php echo $OUTPUT->standard_end_of_body_html() ?>
</body>
</html>