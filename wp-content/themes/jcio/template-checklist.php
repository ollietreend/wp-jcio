<?php
/**
 * Template name: Checklist page
 */
?>

<?php while (have_posts()) : the_post(); ?>
  <?php get_template_part('templates/page', 'header'); ?>
  <?php get_template_part('templates/content', 'page'); ?>
  CHECKLIST WILL GO HERE.
<?php endwhile; ?>
