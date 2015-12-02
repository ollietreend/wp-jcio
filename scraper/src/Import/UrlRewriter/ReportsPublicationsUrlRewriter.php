<?php

namespace Scraper\Import\UrlRewriter;

use Goutte\Client;
use Scraper\Import\Importer\AttachmentImporter;

class ReportsPublicationsUrlRewriter extends BaseUrlRewriter
{
    public function rewrite() {
        $this->rewritePdfLinks();
    }

    public function rewritePdfLinks() {
        $dom = $this->getDOM();
        $links = $dom->getElementsByTagName('a');

        foreach ($links as $link) {
            $url = $link->getAttribute('href');
            $isHtmLink = preg_match('/\.html?$/i', $url);

            if ($this->shouldRewriteUrl($url) && $isHtmLink) {
                // These links have an intermediate html page, so we need to scrape that to find the actual PDF link
                $goutte = new Client();
                $fullUrl = $_ENV['IMPORT_URL'] . $url;
                $url = $goutte->request('GET', $fullUrl)->filter('a')->first()->attr('href');
                // Remove directory up-levelling
                $url = preg_replace('/\.\.\//', '', $url);

                $attachment = AttachmentImporter::import($url, $this->wppost->WP_Post->ID);
                $link->setAttribute('href', $attachment->getUrl());
            }
        }
        $this->saveDOM();
    }
}
