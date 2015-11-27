<?php

namespace Scraper\Source\ContentEntity;

use Scraper\Source\Resource;
use Scraper\Utility\LazyProperties;
use Scraper\Utility\TextHelper;
use Symfony\Component\DomCrawler\Crawler;

class PageEntity
{
    use LazyProperties;

    private $lazyProperties = [
        'title',
        'content',
    ];

    public $resource = null;

    public function __construct(Resource $resource)
    {
        $this->resource = $resource;
    }

    public function getTitle()
    {
        $crawler = $this->resource->getCrawler();
        $title = $crawler->filter('h1')->text();
        $title = TextHelper::tidyText($title);
        return $title;
    }

    public function getContent()
    {
        $crawler = $this->resource->getCrawler();
        $filter = $crawler->filter('#content');

        // Clone elements into new Crawler instance
        // This allows us to modify the DOM without modifying the DOM of the original crawler object.
        $content = new Crawler();
        foreach ($filter as $matchedElement) {
            $content->add(clone $matchedElement);
        }

        // Remove unwanted elements from content
        $content->filter('h1, #navBoxes')->each(function($crawler) {
            foreach ($crawler as $node) {
                $node->parentNode->removeChild($node);
            }
        });

        // Filter and clean HTML
        $html = $content->html();
        $html = utf8_decode($html);
        $html = TextHelper::tidyHtml($html);

        return $html;
    }
}
