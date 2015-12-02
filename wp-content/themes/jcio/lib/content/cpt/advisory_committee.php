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
    add_filter('pre_get_posts', array($this, 'adminOrderAlphabetically'));
    add_action('admin_init', array($this, 'adminRemoveDateFilter'));
    add_action('admin_head', array($this, 'adminHeadStyles'));
    add_filter(sprintf('manage_%s_posts_columns', $this->postType) , array($this, 'manageColumns'));
    add_action(sprintf('manage_%s_posts_custom_column', $this->postType) , array($this, 'customColumns'), 10, 2);
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

  /**
   * Order posts alphabetically in the admin area.
   *
   * @param \WP_Query $wpquery
   */
  public function adminOrderAlphabetically(\WP_Query $wpquery) {
    if (is_admin() && $wpquery->query['post_type'] == $this->postType) {
      $wpquery->set('orderby', 'title');
      $wpquery->set('order', 'ASC');
    }
  }

  /**
   * Remove the date filter options.
   */
  function adminRemoveDateFilter() {
    global $typenow;
    if (isset($typenow) && $typenow == $this->postType) {
      add_filter('months_dropdown_results', '__return_empty_array');
    }
  }

  /**
   * Admin CSS styles
   *  - Hide filter button
   *  - Hide view switcher (list view / excerpt view)
   */
  function adminHeadStyles() {
    global $typenow;
    if ($typenow == $this->postType) {
      ?>
      <style type="text/css">
        input.button[name="filter_action"],
        .view-switch {
          display: none;
        }
      </style>
      <?php
    }
  }

  /**
   * Add custom columns to admin table
   *
   * @param array $columns
   * @return array
   */
  function manageColumns($columns) {
    unset($columns['date']);
    $columns['address'] = 'Address';
    return $columns;
  }

  /**
   * Output content for custom column cell
   *
   * @param $column
   * @param $post_id
   */
  function customColumns($column, $post_id) {
    switch ($column) {
      case 'address':
        the_field('address', $post_id);
        break;
    }
  }
}

$AdvisoryCommittee = new AdvisoryCommittee();
