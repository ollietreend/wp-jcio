<?php
/**
 * Template name: Checklist page
 */
?>

<?php while (have_posts()) : the_post(); ?>
  <?php get_template_part('templates/page', 'header'); ?>
  <?php get_template_part('templates/content', 'page'); ?>

  <div class="left-column">
    <div class="column-head-tick"></div>
    <h2><?php the_field('tick_heading'); ?></h2>
    <?php if (have_rows('tick_list')): ?>
      <ul>
        <?php while(have_rows('tick_list')): the_row(); ?>
          <li><?php the_sub_field('text'); ?></li>
        <?php endwhile; ?>
      </ul>
    <?php endif; ?>
  </div>

  <div class="right-column">
    <div class="column-head-cross"></div>
    <h2><?php the_field('cross_heading'); ?></h2>
    <?php if (have_rows('cross_list')): ?>
      <ul>
        <?php while(have_rows('cross_list')): the_row(); ?>
          <li><?php the_sub_field('text'); ?></li>
        <?php endwhile; ?>
      </ul>
    <?php endif; ?>
  </div>

  <?php

  $contentAfter = get_field('content_after');
  if (!empty($contentAfter)) {
    echo '<div class="clear-both"></div>';
    the_field('content_after');
  }

  ?>

<?php endwhile; ?>
