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
 * Get attachment ID from its URL
 *
 * @param string $url
 * @return bool|int The Attachment ID or FALSE if not found
 */
function get_attachment_id_from_url($url) {
	global $wpdb;

	// First: try to find an exact match for the attachment GUID
	$query = $wpdb->prepare("SELECT ID FROM {$wpdb->posts} WHERE guid = %s LIMIT 1", $url);
	$id = $wpdb->get_var($query);
	if (!is_null($id)) {
		return (int) $id;
	}

	// Fallback: try and do a fuzzier (but slower) LIKE match
	// Drop everything before /uploads/ in the image src so we can match against different hostnames
	$url_part = substr($url, strpos($url, '/uploads/'));
	$like = '%' . $wpdb->esc_like($url_part);
	$query = $wpdb->prepare("SELECT ID FROM {$wpdb->posts} WHERE guid LIKE %s LIMIT 1", $like);
	$id = $wpdb->get_var($query);
	if (!is_null($id)) {
		return (int) $id;
	}

	// Else: attachment not found, return false
	return false;
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
