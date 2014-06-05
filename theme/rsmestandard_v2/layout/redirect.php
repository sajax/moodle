<?php


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
			<div id="region-main-box">
				<div id="region-post-box">

					<div id="region-main-wrap">
						<div id="region-main">
							<div class="region-content">
								<?php echo $OUTPUT->main_content() ?>
							</div>
						</div>
					</div>
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