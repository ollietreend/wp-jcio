<?php

/**
 * Shortcode for displaying link list
 */

namespace Roots\Sage\Content\Shortcodes;

use DOMDocument;
use DOMElement;

class FileList {
  public $uses = 0;

  public function __construct() {
    add_shortcode('file_list', array($this, 'shortcode'));
  }

  public function shortcode($attrs, $content = null) {
    // Remove "</p>" from beginning and "<p>" from end
    $content = preg_replace(array('/^<\/p>\s*/i', '/\s*<p>$/i'), '', $content);

    // Instantiate a DOMDocument object
    $doc = new DOMDocument();
    $doc->loadHTML('<?xml encoding="UTF-8">' . $content);

    // Add class "link-list" to ul elements
    $uls = $doc->getElementsByTagName('ul');
    foreach ($uls as $ul) {
      $ulHelper = new DOMHelper($ul);
      if (!$ulHelper->hasClass('link-list')) {
        $ulHelper->addClass('link-list');
      }
    }

    // Add formatting for links to file attachments
    $lis = $doc->getElementsByTagName('li');
    foreach ($lis as $li) {
      /* @var DOMElement $li */
      /* @var DOMElement $link */
      $liHelper = new DOMHelper($li);
      $link = $li->getElementsByTagName('a')->item(0);

      $href = $link->getAttribute('href');
      $attachmentID = \Roots\Sage\Extras\get_attachment_id_from_url($href);
      if (is_null($attachmentID)) {
        continue;
      }

      $attachmentPath = get_attached_file($attachmentID);
      $extension = pathinfo($attachmentPath, PATHINFO_EXTENSION);
      $liHelper->addClass($extension);

      $metaString = \Roots\Sage\Extras\attachment_meta_info($attachmentID);
      $text = $doc->createTextNode(' ' . $metaString);
      $li->insertBefore($text, $link->nextSibling);

      $link->setAttribute('target', '_blank');
      $link->setAttribute('title', 'Opens in a new window');
    }

    // Extract HTML from DOMDocument object
    $content = $doc->saveHTML($doc->getElementsByTagName('body')->item(0));
    $content = preg_replace(array('/^<body>\s*/i', '/\s*<\/body>$/i'), '', $content);

    return $content;
  }
}

class DOMHelper {
  /**
   * @var DOMElement
   */
  protected $element = null;

  public function __construct(DOMElement $element) {
    $this->element = $element;
  }

  public function hasClass($needle) {
    $classes = $this->getClasses();
    return in_array($needle, $classes);
  }

  public function addClass($class) {
    $classes = $this->getClasses();
    $classes[] = $class;
    $this->setClasses($classes);
    return $this;
  }

  public function removeClass($class) {
    $classes = $this->getClasses();
    $classes = array_filter($classes, function ($c) use ($class) {
      return $c !== $class;
    });
    $this->setClasses($classes);
    return $this;
  }

  protected function getClasses() {
    $class = $this->element->getAttribute('class');
    $classes = explode(' ', $class);
    $classes = $this->normalizeClassesArray($classes);
    return $classes;
  }

  protected function setClasses($classes) {
    $classes = $this->normalizeClassesArray($classes);
    $class = implode(' ', $classes);
    $this->element->setAttribute('class', $class);
  }

  protected function normalizeClassesArray($classes) {
    $classes = array_map('trim', $classes);
    $classes = array_filter($classes);
    $classes = array_unique($classes);
    return $classes;
  }
}

new FileList();
