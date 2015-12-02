<?php
/**
 * Template Name: Disciplinary Statements
 */
?>
<?php while (have_posts()) : the_post(); ?>
  <?php get_template_part('templates/page', 'header'); ?>
  <?php get_template_part('templates/tabs', 'disciplinary-statements'); ?>
  <?php get_template_part('templates/content', 'page'); ?>
<?php endwhile; ?>
