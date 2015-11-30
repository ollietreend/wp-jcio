<?php while (have_posts()) : the_post(); ?>
  <?php get_template_part('templates/page', 'header'); ?>
  <?php get_template_part('templates/content', 'page'); ?>

  <?php if (have_rows('content_boxes')): ?>
    <ul id="navBoxes">
      <?php while (have_rows('content_boxes')): the_row(); ?>
        <?php $linkType = get_sub_field('link_type'); ?>
        <li>
          <h2>
            <?php

            switch ($linkType) {
              // No link
              case 'none':
                the_sub_field('heading');
                break;

              // Internal link
              case 'internal':
                // @TODO
                break;

              // External link
              case 'external':
                echo '<a href="' . esc_attr(get_sub_field('link_external')) . '">';
                the_sub_field('heading');
                echo '</a>';
                break;
            }

            ?>
          </h2>

          <?php the_sub_field('content'); ?>

          <?php if ($linkType !== 'none'): ?>
            <div class="more">
              <?php

              switch ($linkType) {
                // Internal link
                case 'internal':
                  // @TODO
                  break;

                // External link
                case 'external':
                  echo '<a href="' . esc_attr(get_sub_field('link_external')) . '">Find out more &gt;</a>';
                  break;
              }

              ?>
            </div>
          <?php endif; ?>
        </li>
      <?php endwhile; ?>
    </ul>
  <?php endif; ?>

<?php endwhile; ?>
