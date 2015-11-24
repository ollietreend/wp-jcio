<?php

namespace Scraper\Source\ContentLister;

use Scraper\Source\ResourceCollection;
use Scraper\Source\Resource;
use Scraper\Source\ContentEntity\PageEntity;

class PageList
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
        $contentEntities = [];

        foreach ($pageResources as $resource) {
            if (self::includeInList($resource)) {
                $contentEntities[] = new PageEntity($resource);
            }
        }

        return $contentEntities;
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
        return (
            !$sniffer->isNewsPage &&
            !$sniffer->isUnwantedPage &&
            !$sniffer->isDisciplinaryStatementsPage &&
            $sniffer->pageHasHeader
        );
    }
}
