<?php

namespace Scraper\Import\PageVariations;

use Scraper\Utility\TextHelper;
use Symfony\Component\DomCrawler\Crawler;

class HomePageVariations extends BasePageVariations
{
    /**
     * Is this the front page?
     *
     * @var bool
     */
    public $isFrontPage = true;

    /**
     * Page content to import.
     *
     * @return string
     */
    public function getContent() {
        $content = new Crawler($this->entity->content);

        // Remove unwanted elements from content
        $content->filter('#navBoxes')->each(function($crawler) {
            foreach ($crawler as $node) {
                $node->parentNode->removeChild($node);
            }
        });

        // Filter and clean HTML
        $html = $content->html();
        $html = TextHelper::tidyHtml($html);

        return $html;
    }

    /**
     * ACF fields to import.
     *
     * @return array|false
     */
    public function getAcfFields() {
        $fields = [];
        $fields['field_564dd79b9cdbd'] = []; // Field key for 'content_boxes'

        $crawler = $this->entity->resource->getCrawler();
        $boxes = $crawler->filter('#content #navBoxes > li');

        $boxes->each(function(Crawler $box) use (&$fields) {
            $boxFields = [];

            $boxFields['heading'] = TextHelper::tidyText($box->filter('h2')->text());

            $link = $box->filter('h2 > a');
            if (count($link) > 0) {
                $boxFields['link_type'] = 'external';
                $boxFields['link_external'] = $link->attr('href');
            } else {
                $boxFields['link_type'] = 'none';
            }

            $content = $box->filter('p')->html();
            $content = utf8_decode($content);
            $content = TextHelper::tidyHtml($content);
            $boxFields['content'] = $content;

            $fields['field_564dd79b9cdbd'][] = $boxFields;
        });

        return $fields;
    }
}
