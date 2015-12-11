<?php

namespace Roots\Sage\Extras;

use Roots\Sage\Setup;

/**
 * Add <body> classes
 */
function body_class($classes) {
  // Add page slug if it doesn't exist
  if (is_single() || is_page() && !is_front_page()) {
    if (!in_array(basename(get_permalink()), $classes)) {
      $classes[] = basename(get_permalink());
    }
  }

  // Add class if sidebar is active
  if (Setup\display_sidebar()) {
    $classes[] = 'sidebar-primary';
  }

  return $classes;
}
add_filter('body_class', __NAMESPACE__ . '\\body_class');

/**
 * Clean up the_excerpt()
 */
function excerpt_more() {
  return ' &hellip; <a href="' . get_permalink() . '">' . __('Continued', 'sage') . '</a>';
}
add_filter('excerpt_more', __NAMESPACE__ . '\\excerpt_more');

/**
 * Default settings for Breadcumb NavXT plugin
 *
 * @param $settings
 * @return mixed
 */
function bcn_settings_init($settings) {
  $settings['Hhome_template'] = '<span typeof="v:Breadcrumb"><a rel="v:url" property="v:title" title="Go to %title%." href="%link%" class="%type%">Home</a></span>';
  $settings['Hhome_template_no_anchor'] = '<span typeof="v:Breadcrumb"><span property="v:title">Home</span></span>';
  $settings['hseparator'] = ' › ';
  $settings['Spost_post_taxonomy_type'] = false;

  return $settings;
}
add_filter('bcn_settings_init', __NAMESPACE__ . '\\bcn_settings_init');

/**
 * Disable search pages.
 *
 * @param $query
 * @param bool|true $error
 */
function disable_search($query, $error = true) {
  if (is_search()) {
    // Change search query
    $query->is_search = false;
    $query->query_vars[s] = false;
    $query->query[s] = false;

    if ($error == true) {
      $query->is_404 = true;
    }
  }
}
add_action('parse_query', __NAMESPACE__ . '\\disable_search');
add_filter('get_search_form', function() { return ''; });

/**
 * Get attachment ID given its URL
 *
 * @param $url
 * @return mixed
 */
function get_attachment_id_from_url($url) {
  global $wpdb;
  $base = home_url();
  if (substr($url, 0, strlen($base)) !== $base) {
    // Make relative URL absolute
    $url = $base . $url;
  }
  $attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid='%s';", $url ));
  return $attachment[0];
}

/**
 * Return a string of meta info for the specified attachment.
 * Example: "[PDF, 1 MB]"
 *
 * @param int $attachmentID
 * @return string
 */
function attachment_meta_info($attachmentID) {
  $path = get_attached_file($attachmentID);
  $extension = pathinfo($path, PATHINFO_EXTENSION);
  $size = filesize($path);
  return sprintf('[%s, %s]', strtoupper($extension), size_format($size));
}
