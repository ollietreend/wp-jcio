<?php

namespace Roots\Sage\Content;

/**
 * Hide ACF from the admin menu.
 */
function should_acf_show_admin() {
  return ( defined('WP_ENV') && WP_ENV == 'development' );
}
add_filter('acf/settings/show_admin', __NAMESPACE__ . '\\should_acf_show_admin');

/**
 * Include content definitions from content directory.
 */
$includePath = dirname(__FILE__) . '/content/';
foreach (array('cpt', 'shortcodes') as $type) {
  $dir = scandir($includePath . $type);
  foreach ($dir as $file) {
    if (in_array($file, array('.', '..'))) {
      continue;
    }
    require_once $includePath . $type . '/' . $file;
  }
}
