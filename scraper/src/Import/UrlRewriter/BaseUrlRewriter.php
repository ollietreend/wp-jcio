<?php

namespace Scraper\Import\UrlRewriter;

use Scraper\Utility\ObjectCache;
use Scraper\WordPress\Post\Base;
use DOMDocument;

class BaseUrlRewriter
{
    use ObjectCache;

    public $entity = null;

    /**
     * @var Base
     */
    public $wppost = null;

    public function __construct($entity, Base $wppost) {
        $this->entity = $entity;
        $this->wppost = $wppost;
    }

    public function rewrite() {
        // Stub method
    }

    /**
     * @return DOMDocument
     */
    public function getDOM() {
        return $this->getObject('dom', function() {
            $dom = new DOMDocument();
            $dom->loadHTML($this->wppost->WP_Post->post_content);
            return $dom;
        });
    }

    public function saveDOM() {
        $dom = $this->getDOM();
        $bodyTag = $dom->getElementsByTagName('body')->item(0);
        $html = $dom->saveHTML($bodyTag);
        $html = preg_replace('/\s?<\/?body>\s?/', '', $html);
        $this->wppost->save([
            'post_content' => $html,
        ]);
    }

    public function shouldRewriteUrl($url) {
        // If the URL looks like it's already been rewritten, don't rewrite again.
        // There's definite room for improvement here – but this works for now.
        if (
            preg_match('/\/wp-content\/uploads\//i', $url) ||
            preg_match('/\/$/', $url)
        ) {
            return false;
        }

        // Check if URL begins with a protocol, "www." or "//" (protocol-relative).
        if (preg_match('/^([a-z][a-z0-9+\.-]*:|www\.|\/\/)/i', $url)) {
            return false;
        }

        // If none of the above, then we want to rewrite it.
        return true;
    }
}
