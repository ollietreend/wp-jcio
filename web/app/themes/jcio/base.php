<?php

use Roots\Sage\Setup;
use Roots\Sage\Wrapper;

?>
<!doctype html>
<html class="no-js" <?php language_attributes(); ?>>
<head>
    <?php get_template_part('head'); ?>
</head>
<body <?php body_class(); ?>>
<?php
do_action('get_header');
get_header();
?>
<div id="contentwrapper">
    <?php get_template_part('templates/main-menu'); ?>
    <a id="skipnav" name="skipnav"></a>
    <div id="content">
        <?php include Wrapper\template_path(); ?>
    </div>
</div>
<?php
do_action('get_footer');
get_footer();
wp_footer();
?>
</body>
</html>
