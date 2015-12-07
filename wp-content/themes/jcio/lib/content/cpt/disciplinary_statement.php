<?php

/**
 * Custom Post Type for Disciplinary Statements
 */

namespace Roots\Sage\Content\CPT;

use bcn_breadcrumb_trail;

class DisciplinaryStatement {
  public $postType = 'disciplinary_stmnt';

  /**
   * Class constructor
   */
  public function __construct() {
    add_action('init', array($this, 'registerPostType'));
    add_action('init', array($this, 'addRewriteRules'));
    add_action('template_redirect', array($this, 'redirectToCurrentYear'));
    add_action('bcn_after_fill', array($this, 'addYearBreadcrumbs'));
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

  public function addRewriteRules() {
    add_rewrite_rule(
      '^disciplinary-statements/([0-9]{4})/?',
      'index.php?pagename=disciplinary-statements&disciplinary_stmnt_year=$matches[1]',
      'top'
    );

    add_rewrite_tag('%disciplinary_stmnt_year%', '([0-9]{4})');
  }

  /**
   * Redirect to the most recent / current year if the Disciplinary Statements
   * page is loaded without a year specified in the URL parameters.
   */
  public function redirectToCurrentYear() {
    $year = get_query_var('disciplinary_stmnt_year');
    if (is_page_template('template-disciplinary-statements.php') && empty($year)) {
      $years = self::getArchiveYears();
      $url = self::getYearPageUrl($years[0]);
      wp_redirect($url);
      exit;
    }
  }

  /**
   * Add the active year to the breadcrumb trail.
   *
   * @param bcn_breadcrumb_trail $trail
   */
  public function addYearBreadcrumbs(bcn_breadcrumb_trail $trail) {
    if (is_page_template('template-disciplinary-statements.php')) {
      $pageCrumb = $trail->breadcrumbs[0];
      $yearCrumb = clone $pageCrumb;
      $pageCrumb->set_url(get_the_permalink());
      $theYear = get_query_var('disciplinary_stmnt_year');
      $yearCrumb->set_title($theYear);
      array_unshift($trail->breadcrumbs, $yearCrumb);
    }
  }

  /**
   * Retrieve an array of available archive years for disciplinary statements.
   *
   * @return array
   */
  public static function getArchiveYears() {
    global $wpdb;

    $sql = sprintf(
      "SELECT YEAR(post_date) AS year
       FROM %s
       WHERE post_type = 'disciplinary_stmnt'
       AND post_status = 'publish'
       GROUP BY YEAR(post_date)
       ORDER BY post_date DESC",
      $wpdb->posts
    );
    $years = $wpdb->get_results($sql);
    $years = array_map(function ($year) {
      return $year->year;
    }, $years);

    return $years;
  }

  public static function getYearPageUrl($year) {
    $baseUrl = get_the_permalink(self::getBasePage());
    return sprintf('%s%d/', $baseUrl, $year);
  }

  public static function getBasePage() {
    return get_page_by_path('disciplinary-statements');
  }
}

$DisciplinaryStatement = new DisciplinaryStatement();
