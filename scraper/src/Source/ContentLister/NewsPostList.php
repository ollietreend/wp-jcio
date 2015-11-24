<?php

namespace Scraper\Source\ContentLister;

use Scraper\Source\ResourceCollection;
use Scraper\Source\Resource;
use Scraper\Source\ContentEntity\NewsPostEntity;
use Scraper\Utility\TextHelper;
use DateTime;

class NewsPostList
{
    /**
     * Return an array of PageEntity objects to represent content on the scraped site.
     *
     * @param ResourceCollection $resources
     * @return array
     */
    public static function getList(ResourceCollection $resources)
    {
        $pageResources = $resources->filterByType('page');

        foreach ($pageResources as $resource) {
            if (self::includeInList($resource)) {
                return self::extractNewsPostsFromPage($resource);
            }
        }

        return [];
    }

    public static function extractNewsPostsFromPage(Resource $resource)
    {
        $crawler = $resource->getCrawler();
        $dlHtml = $crawler->filter('dl')->html();
        $stories = preg_split('/<strong>.*\n?.*Update/i', $dlHtml);

        // Remove empty stories
        $stories = array_filter($stories, function ($story) {
            $textContent = trim(strip_tags($story));
            return !empty($textContent);
        });

        $contentEntities = [];
        foreach ($stories as $story) {
            $storyContent = self::parseNewsPost($story);
            $contentEntities[] = new NewsPostEntity($storyContent);
        }

        return $contentEntities;
    }

    /**
     * Parse HTML snippet to extract a news post.
     *
     * @param $html
     * @return array Keys: "title", "date", "content"
     */
    protected static function parseNewsPost($html)
    {
        preg_match('/^.*?(\d+).*?(July|August|September)\s(\d{4})[\s:-]+(.*?)</i', $html, $matches);
        $matches = array_map('trim', $matches);

        $date = join(' ', [$matches[1], $matches[2], $matches[3]]);
        $date = new DateTime($date);

        $title = TextHelper::tidyText($matches[4]);

        $content = preg_replace('/^.*?<\/strong>/i', '', $html);
        $content = preg_replace('/<(\/?)(dt|dd).*?>/i', '<$1p>', $content);
        $content = TextHelper::tidyHtml($content);
        $content = preg_replace('/<p>(<br\s?\/?>\s?)+/i', '<p>', $content);
        $content = preg_replace('/(<br\s?\/?>\s?)+<\/p>/i', '</p>', $content);
        $content = TextHelper::tidyHtml($content);

        return compact('title', 'date', 'content');
    }

    /**
     * Determine if the supplied resource should be included in the page list.
     *
     * @param \Scraper\Source\Resource $resource
     * @return bool
     */
    public static function includeInList(Resource $resource)
    {
        $sniffer = $resource->getPageSniffer();
        return $sniffer->isNewsPage;
    }
}
