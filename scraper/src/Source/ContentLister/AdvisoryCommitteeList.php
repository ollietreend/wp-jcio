<?php

namespace Scraper\Source\ContentLister;

use Scraper\Source\ResourceCollection;
use Scraper\Source\Resource;
use Scraper\Source\ContentEntity\AdvisoryCommitteeEntity;

class AdvisoryCommitteeList
{
    /**
     * Return an array of PageEntity objects to represent content on the scraped site.
     *
     * @param ResourceCollection $resources
     * @return AdvisoryCommitteeEntity[]
     */
    public static function getList(ResourceCollection $resources)
    {
        $pageResources = $resources->filterByType('page');

        foreach ($pageResources as $resource) {
            if (self::includeInList($resource)) {
                return self::extractCommitteesFromPage($resource);
            }
        }

        return [];
    }

    /**
     * Extract AdvisoryCommitteeEntity objects from the supplied resource.
     *
     * @param Resource $resource
     * @return AdvisoryCommitteeEntity[]
     */
    public static function extractCommitteesFromPage(Resource $resource)
    {
        $crawler = $resource->getCrawler();
        $rows = $crawler->filter('.two-column-table > tbody > tr');

        $contentEntities = [];
        foreach ($rows as $row) {
            $contentEntities[] = new AdvisoryCommitteeEntity($row);
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
        return $sniffer->isAdvisoryCommitteePage;
    }
}
