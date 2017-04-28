<?php get_template_part('templates/page', 'header'); ?>

<p>You may have followed a broken link from another site or found an error somewhere on our site.</p>
<p>Try one of the links below to find the content you're looking for.</p>

<?php

if (has_nav_menu('primary_navigation')) {
  wp_nav_menu([
    'theme_location' => 'primary_navigation',
    'container' => false,
  ]);
}

?>
