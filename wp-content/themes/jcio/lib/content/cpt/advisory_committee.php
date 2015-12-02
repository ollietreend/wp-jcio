<?php

/**
 * Custom Post Type for Advisory Committees
 */

namespace Roots\Sage\Content\CPT;

class AdvisoryCommittee {
  public $postType = 'advisory_committee';

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
      'name' => 'Advisory Committees',
      'singular_name' => 'Advisory Committee',
      'add_new' => 'Add New',
      'all_items' => 'All Committees',
      'add_new_item' => 'Add New Committee',
      'edit_item' => 'Edit Committee',
      'new_item' => 'New Committee',
      'view_item' => 'View Committee',
      'search_items' => 'Search Committees',
      'not_found' =>  'No Committees found',
      'not_found_in_trash' => 'No Committees found in trash',
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
      'menu_icon' => 'dashicons-groups',
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

$AdvisoryCommittee = new AdvisoryCommittee();
