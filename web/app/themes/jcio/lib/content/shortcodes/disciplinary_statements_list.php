<?php

/**
 * Shortcode for displaying the most recent disciplinary statements
 */

namespace Roots\Sage\Content\Shortcodes;

use Roots\Sage\Content\CPT\DisciplinaryStatement;
use WP_Query;

class DisciplinaryStatementsList {
  public function __construct() {
    add_shortcode('disciplinary_statements_list', array($this, 'shortcode'));
  }

  public function shortcode($attrs, $content = null) {
    if (!is_array($attrs)) {
      $attrs = array();
    }
    $defaultAttrs = array(
      'show' => 2,
    );
    $attrs = array_merge($defaultAttrs, $attrs);

    $statements = new WP_Query(array(
      'post_type' => 'disciplinary_stmnt',
      'posts_per_page' => $attrs['show'],
      'orderby' => 'post_date',
      'order' => 'DESC',
    ));

    if (!$statements->have_posts()) {
      return '';
    }

    $html = '<ul>';
    while ($statements->have_posts()) {
      $statements->the_post();
      $title = html_entity_decode(get_the_title());
      $title = \Roots\Sage\Extras\trimToLength($title, 50, array('exact' => false));
      $title = htmlentities($title);
      $document = get_field('document');

      $html .= '<li>';
      $html .= '<a href="' . esc_url($document['url']) . '" target="_blank" onclick="pageTracker._trackPageview(\'' . esc_url($document['url']) . '\');">';
      $html .= $title;
      $html .= '</a>';
      $html .= '</li>';
    }

    $html .= '</ul>';
    return $html;
  }
}

new DisciplinaryStatementsList();
