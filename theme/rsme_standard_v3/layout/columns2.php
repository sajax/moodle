<?php
// Get the HTML for the settings bits.
/* Removed Until Purpose is discovered 
$html = theme_clean_get_html_for_settings($OUTPUT, $PAGE);*/

$left = (!right_to_left());  // To know if to add 'pull-right' and 'desktop-first-column' classes in the layout for LTR.
echo $OUTPUT->doctype() ?>
<html <?php echo $OUTPUT->htmlattributes(); ?>>
<head>
    <title><?php echo $OUTPUT->page_title(); ?></title>
    <link rel="shortcut icon" href="<?php echo $OUTPUT->favicon(); ?>" />
    <?php echo $OUTPUT->standard_head_html() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body <?php echo $OUTPUT->body_attributes('two-column'); ?>>

<?php echo $OUTPUT->standard_top_of_body_html() ?>

<header role="banner" class="navbar">
	<div class="container-fluid header-banner">
		<a href="/"><img src="<?php echo $OUTPUT->pix_url('logos/holdfast_logo', 'theme'); ?>" alt="" /></a>
		<a href="/"><img src="<?php echo $OUTPUT->pix_url('logos/eng_logo', 'theme'); ?>" alt="" /></a>
		<div class="user-card pull-right well">
			<?php echo $OUTPUT->render_user_card(); ?>
		</div>
	</div>
	
    <nav role="navigation" class="navbar-inner">
        <div class="container-fluid">
			<!-- Small Screen Menu -->
            <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>
			<!-- Small Screen Menu -->
            <div class="nav-collapse collapse">
                <?php echo $OUTPUT->custom_menu(); ?>
            </div>
        </div>
    </nav>
</header>

<div id="page" class="container-fluid">
	<?php echo $OUTPUT->render_notification_boxes(); ?>
    <header id="page-header" class="clearfix">
        <div id="page-navbar" class="clearfix">
            <div class="breadcrumb-nav"><?php echo $OUTPUT->navbar(); ?></div>
            <nav class="breadcrumb-button"><?php echo $OUTPUT->page_heading_button(); ?></nav>
        </div>
        <div id="course-header">
            <?php echo $OUTPUT->course_header(); ?>
        </div>
    </header>

    <div id="page-content" class="row-fluid">
        <section id="region-main" class="span9<?php if ($left) { echo ' pull-right'; } ?>">
            <?php
            echo $OUTPUT->course_content_header();
            echo $OUTPUT->main_content();
            echo $OUTPUT->course_content_footer();
            ?>
        </section>
        <?php
        $classextra = '';
        if ($left) {
            $classextra = ' desktop-first-column';
        }
        echo $OUTPUT->blocks('side-pre', 'span3'.$classextra);
        ?>
    </div>
</div>

<div id="course-footer">
	<?php echo $OUTPUT->course_footer(); ?>
</div>

<footer id="page-footer">
	<?php
	echo $OUTPUT->login_info();
	echo $OUTPUT->home_link();
	echo '<!-- START STANDARD FOOTER -->';
	echo $OUTPUT->standard_footer_html();
	?>
	
	<div id="banner-footer">
		<span><?php echo $SITE->fullname; if($PAGE->heading && $SITE->fullname != $PAGE->heading) { echo ' - ' . $PAGE->heading; } ?></span>
		<p class="helplink pull-right"><?php echo $OUTPUT->page_doc_link(); ?></p>
	</div>
	
	
</footer>
<!-- END OF BODY -->
<?php echo $OUTPUT->standard_end_of_body_html() ?>
<!-- END OF BODY -->
<?php echo $OUTPUT->stats_tracking(); ?>
</body>
</html>
