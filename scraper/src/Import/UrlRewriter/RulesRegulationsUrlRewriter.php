<?php

namespace Scraper\Import\UrlRewriter;

use Scraper\Import\Importer\AttachmentImporter;

class RulesRegulationsUrlRewriter extends BaseUrlRewriter
{
    public function rewrite() {
        $this->rewritePdfLinks();
    }

    public function rewritePdfLinks() {
        $dom = $this->getDOM();
        $links = $dom->getElementsByTagName('a');

        foreach ($links as $link) {
            $url = $link->getAttribute('href');
            $isPdfLink = preg_match('/\.pdf$/i', $url);
            if ($this->shouldRewriteUrl($url) && $isPdfLink) {
                $attachment = AttachmentImporter::import($url, $this->wppost->WP_Post->ID);
                $link->setAttribute('href', $attachment->getUrl());
            }
        }
        $this->saveDOM();
    }
}
