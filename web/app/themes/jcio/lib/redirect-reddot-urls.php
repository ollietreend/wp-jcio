<?php

/**
 * Redirect requests for old RedDot URLs to their
 * equivalent page within WordPress.
 * This is possible because we've stored the old RedDot URL
 * where possible with each post.
 */

namespace Roots\Sage\RedirectReddotUrls;

use WP_Query;

class RedDotURLs {
  /**
   * Hardcoded mapping of old URL => new URL
   *
   * @var array
   */
  public $mapUrls = array(
    '975.htm' => 'disciplinary-statements/2015',
    '816.htm' => 'disciplinary-statements/2014',
    'disciplinary-statements-2013.htm' => 'disciplinary-statements/2013',
    'disciplinary-statements-2012.htm' => 'disciplinary-statements/2012',
    '667.htm' => 'disciplinary-statements/2011',
  );

  public function __construct() {
    add_action('template_redirect', array($this, 'action_template_redirect'));
  }

  public function action_template_redirect() {
    global $wp;
    ini_set('html_errors', 1);

    if (is_404()) {
      $matchingUrl = $this->mapToNewUrl($wp->request);
      if ($matchingUrl) {
        wp_redirect($matchingUrl, 301);
        exit;
      }
    }
  }

  public function mapToNewUrl($request) {
    $mapped = false;

    if (isset($this->mapUrls[$request])) {
      // If the requested URL exists in $this->mapUrls, use that
      $mapped = trailingslashit(home_url($this->mapUrls[$request]));
    } else {
      // Otherwise look for posts with a matching 'reddot_url' meta field
      $query = new WP_Query(array(
        'meta_key' => 'reddot_url',
        'meta_value' => $request,
        'post_type' => 'any',
      ));

      if ($query->post_count > 0) {
        $mapped = get_the_permalink($query->post);
      }
    }

    return $mapped;
  }
}

new RedDotURLs();
