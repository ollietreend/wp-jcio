<?php
/**
 * Template name: Advisory Committee page
 */
?>

<?php while (have_posts()) : the_post(); ?>
  <?php get_template_part('templates/page', 'header'); ?>
  <?php get_template_part('templates/content', 'page'); ?>

  <?php

  $committees = new WP_Query(array(
    'post_type' => 'advisory_committee',
    'posts_per_page' => -1,
  ));

  function ac_slug($post) {
    return $post->post_name;
  }

  ?>

  <?php if ($committees->have_posts()): ?>
    <form id="page-changer" name="page-changer">
      <label for="committee-nav">Find Advisory Commitee</label>:
      <select name="nav" id="committee-nav">
      <option value="">Go to page...</option>
        <?php

        while ($committees->have_posts()) {
          $committees->the_post();
          the_title('<option value="#' . ac_slug($committees->post) . '">', '</option>');
        }

        $committees->rewind_posts();

        ?>
      </select>
      <input type="submit" value="Go" id="submit" style="display: none;">
    </form>

    <table class="two-column-table" summary="Contact an Advisory Committee">
      <thead>
      <tr>
        <th>
          Advisory Committee
        </th>
        <th>
          Contact address
        </th>
      </tr>
      </thead>
      <tbody>
      <?php while ($committees->have_posts()): $committees->the_post(); ?>
        <tr id="<?php echo ac_slug($committees->post); ?>">
          <td>
            <?php the_title(); ?>
          </td>
          <td>
            <?php the_field('address'); ?>
            <p>
              <a href="#">Back to top</a>
            </p>
          </td>
        </tr>
      <?php endwhile; ?>
      </tbody>
    </table>
  <?php endif; ?>

<?php endwhile; ?>

<?php wp_reset_postdata(); ?>
