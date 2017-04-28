<?php

namespace Roots\Sage\Plugins;

require_once 'classes/class-tgm-plugin-activation.php';

/**
 * Register the required plugins for this theme.
 */
function tgmpa_register() {
  /**
	 * Array of plugin arrays. Required keys are name and slug.
	 * If the source is NOT from the .org repo, then source is also required.
	 */
  $plugins = array(
    array(
      'name'        => 'Advanced Custom Fields Pro',
      'slug'        => 'advanced-custom-fields-pro',
      'source'      => 'advanced-custom-fields-pro.zip',
      'required'    => true,
    ),

    array(
      'name'        => 'Soil',
      'slug'        => 'soil',
      'source'      => 'https://github.com/roots/soil/archive/3.5.0.zip',
      'required'    => true,
    ),

    array(
      'name'        => 'Breadcrumb NavXT',
      'slug'        => 'breadcrumb-navxt',
      'required'    => true,
    ),

    array(
      'name'        => 'WordPress SEO by Yoast',
      'slug'        => 'wordpress-seo',
      'is_callable' => 'wpseo_init',
      'required'    => false,
    ),

    // Useful for editing "Contact a Tribunal President" page
    array(
      'name'        => 'MCE Table Buttons',
      'slug'        => 'mce-table-buttons',
      'required'    => false,
    ),
  );

  /**
   * Array of configuration settings. Amend each line as needed.
   *
   * TGMPA will start providing localized text strings soon. If you already have translations of our standard
   * strings available, please help us make TGMPA even better by giving us access to these translations or by
   * sending in a pull-request with .po file(s) with the translations.
   *
   * Only uncomment the strings in the config array if you want to customize the strings.
   */
  $config = array(
    'id'           => 'tgmpa_jcio',            // Unique ID for hashing notices for multiple instances of TGMPA.
    'default_path' => get_stylesheet_directory() . '/lib/plugins/', // Default absolute path to bundled plugins.
    'has_notices'  => true,                    // Show admin notices or not.
    'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
    'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
    'is_automatic' => false,                   // Automatically activate plugins after installation or not.
    'message'      => '',                      // Message to output right before the plugins table.
  );

  tgmpa( $plugins, $config );
}

add_action('tgmpa_register', __NAMESPACE__ . '\\tgmpa_register');

/**
 * Hide "cannot update" permissions error.
 * This is a bug with TGM, which shows "Sorry, but you do not have the correct permissions to update"
 * error message to users who do not have permission to update plugins.
 */
function admin_head() {
  if (!current_user_can('update_plugins')) {
    ?><style>#setting-error-tgmpa { display: none; }</style><?php
  }
}
add_action('admin_head', __NAMESPACE__ . '\\admin_head');
