<div id="footerwrapper">
  <div id="footer">
    <?php

    if (has_nav_menu('footer_navigation')) {
      wp_nav_menu([
        'theme_location' => 'footer_navigation',
        'menu_id' => 'navBoxes',
        'container' => false,
      ]);
    }

    ?>
  </div>
</div>
