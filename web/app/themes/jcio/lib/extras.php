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
  if (!is_admin() && is_search()) {
    // Change search query
    $query->is_search = false;
    $query->query_vars['s'] = false;
    $query->query['s'] = false;

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
/**
 * Truncates text starting from the end.
 *
 * Cuts a string to the length of $length and replaces the first characters
 * with the ellipsis if the text is longer than length.
 *
 * ### Options:
 *
 * - `ellipsis` Will be used as Beginning and prepended to the trimmed string
 * - `exact` If false, $text will not be cut mid-word
 *
 * @param string $text String to truncate.
 * @param int $length Length of returned string, including ellipsis.
 * @param array $options An array of options.
 * @return string Trimmed string.
 */
function trimToLength($text, $length = 100, array $options = [])
{
  $default = [
    'ellipsis' => '…',
    'exact' => true,
  ];
  $options += $default;

  if (mb_strlen($text) <= $length) {
    return $text;
  }

  $truncate = mb_substr($text, 0, $length - mb_strlen($options['ellipsis']));
  if (!$options['exact']) {
    $spacepos = mb_strrpos($truncate, ' ');
    $truncate = $spacepos === false ? '' : trim(mb_substr($truncate, 0, $spacepos));
  }

  return $truncate . $options['ellipsis'];
}

/**
 * For plugin: Google Analytics Dashboard for WP
 * Move Google Analytics tracking snippet to the footer
 * if jQuery/JS has been moved to the footer.
 * This resolves a dependency that the snippet has on jQuery.
 */
function move_google_analytics_to_footer() {
    $jqueryInFooter = ( get_theme_support('soil-js-to-footer') || get_theme_support('soil-jquery-cdn') );
    $gadwpExists = function_exists('GADWP');

    if (!is_admin() && $jqueryInFooter && $gadwpExists) {
        remove_action('wp_head', array(GADWP()->tracking, 'tracking_code'), 99);
        add_action('wp_footer', array(GADWP()->tracking, 'tracking_code'), 99);
    }
}
add_action('init', __NAMESPACE__ . '\\move_google_analytics_to_footer');

