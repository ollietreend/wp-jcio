<article <?php post_class(); ?>>
  <h2 class="post-heading">
    <?php the_time('j F Y'); ?> – <?php the_title(); ?>
  </h2>
  <div class="post-content">
    <?php the_content(); ?>
  </div>
</article>
