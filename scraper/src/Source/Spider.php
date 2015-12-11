<?php

namespace Scraper\Source;

use Scraper\Utility\Crawler;
use FileSystemCache;
use FileSystemCacheKey;

class Spider
{
    public static function createCollectionFromUrl($url)
    {
        $links = self::spiderUrl($url);
        $collection = new ResourceCollection();

        foreach ($links as $linkUrl => $meta) {
            if (self::isValidResource($linkUrl, $meta)) {
                $collection->append(new Resource($meta));
            }
        }

        return $collection;
    }

    public static function isValidResource($relativeUrl, $meta)
    {
        $isMailto   = preg_match('/^mailto\:/i', $relativeUrl);
        $isExternal = $meta['external_link'];
        $isEmpty    = empty($relativeUrl);
        $isBaseUrl  = isset($meta['links_text'][0]) && $meta['links_text'][0] == 'BASE_URL';

        return !($isMailto || $isExternal || $isEmpty || $isBaseUrl);
    }

    private static function spiderUrl($url)
    {
        $key = new FileSystemCacheKey('links_' . $url, null);
        $links = FileSystemCache::retrieve($key);
        if (!$links) {
            $links = self::getLinks($url);
            FileSystemCache::store($key, $links);
        }
        return $links;
    }

    private static function getLinks($url)
    {
        $crawler = new Crawler($url, 1000);
        $crawler->traverse();
        return $crawler->getLinks();
    }
}
