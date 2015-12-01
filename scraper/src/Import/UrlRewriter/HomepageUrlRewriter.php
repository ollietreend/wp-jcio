<?php

namespace Scraper\Import\UrlRewriter;

use Scraper\Import\Importer\AttachmentImporter;
use Scraper\WordPress\Post\Page;

class HomepageUrlRewriter extends BaseUrlRewriter
{
    public function rewrite() {
        $this->rewriteImages();
        $this->rewriteAcfContentBoxes();
    }

    public function rewriteImages() {
        $dom = $this->getDOM();
        $images = $dom->getElementsByTagName('img');
        foreach ($images as $img) {
            $url = $img->getAttribute('src');
            if ($this->shouldRewriteUrl($url)) {
                $attachment = AttachmentImporter::import($url, $this->wppost->WP_Post->ID);
                $img->setAttribute('src', $attachment->getUrl());
            }
        }
        $this->saveDOM();
    }

    public function rewriteAcfContentBoxes() {
        $boxes = $this->wppost->getField('field_564dd79b9cdbd');
        foreach ($boxes as $k => $box) {
            if (empty($box['link_external']) || $box['link_type'] !== 'external') {
                continue;
            }

            $url = $box['link_external'];
            if (!$this->shouldRewriteUrl($url)) {
                continue;
            }

            if ($url == '975.htm') {
                // We don't want to rewrite this URL
                continue;
            }

            $targetPage = Page::getByMeta([
                'reddot_import' => true,
                'reddot_url' => $url,
            ]);

            if (!$targetPage) {
                trigger_error('Unable to rewrite content box link with URL: ' . $url, E_USER_WARNING);
                continue;
            }

            $box['link_type'] = 'internal';
            $box['link_external'] = '';
            $box['link_internal'] = $targetPage->WP_Post->ID;

            $boxes[$k] = $box;
        }
        $this->wppost->saveField('field_564dd79b9cdbd', $boxes);
    }
}
