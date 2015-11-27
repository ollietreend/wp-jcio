<?php
/**
 * Template name: Advisory Committee page
 */
?>

<?php while (have_posts()) : the_post(); ?>
  <?php get_template_part('templates/page', 'header'); ?>
  <?php get_template_part('templates/content', 'page'); ?>
  ADVISORY COMMITTEE LIST WILL GO HERE.
<?php endwhile; ?>
