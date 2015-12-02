<?php

/**
 * Custom Post Type for Disciplinary Statements
 */

namespace Roots\Sage\Content\CPT;

class DisciplinaryStatement {
  public $postType = 'disciplinary_stmnt';

  /**
   * Class constructor
   */
  public function __construct() {
    add_action('init', array($this, 'registerPostType'));
  }

  /**
   * Register the custom post type
   */
  function registerPostType() {
    $labels = array(
      'name' => 'Disciplinary Statements',
      'singular_name' => 'Disciplinary Statement',
      'add_new' => 'Add New',
      'all_items' => 'All Statements',
      'add_new_item' => 'Add New Statement',
      'edit_item' => 'Edit Statement',
      'new_item' => 'New Statement',
      'view_item' => 'View Statement',
      'search_items' => 'Search Statements',
      'not_found' =>  'No Statements found',
      'not_found_in_trash' => 'No Statements found in trash',
    );

    $args = array(
      'labels' => $labels,
      'public' => false,
      'exclude_from_search' => true,
      'publicly_queryable' => false,
      'show_ui' => true,
      'show_in_nav_menus' => true,
      'show_in_menu' => true,
      'show_in_admin_bar' => true,
      'menu_icon' => 'dashicons-id-alt',
      'hierarchical' => false,
      'supports' => array(
        'title',
      ),
      'has_archive' => false,
      'rewrite' => false,
      'query_var' => true,
      'can_export' => true,
    );

    register_post_type($this->postType, $args);
  }
}

$DisciplinaryStatement = new DisciplinaryStatement();
