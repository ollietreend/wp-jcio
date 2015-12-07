<?php
/**
 * Template Name: Disciplinary Statements Child Page
 */
?>
<?php while (have_posts()) : the_post(); ?>
  <?php get_template_part('templates/page', 'header'); ?>
  <?php get_template_part('templates/tabs', 'disciplinary-statements'); ?>
  <h2><?php the_title(); ?></h2>
  <?php get_template_part('templates/content', 'page'); ?>
<?php endwhile; ?>
