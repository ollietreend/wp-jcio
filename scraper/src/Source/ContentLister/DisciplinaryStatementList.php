<?php

namespace Scraper\Source\ContentLister;

use Scraper\Source\ResourceCollection;
use Scraper\Source\Resource;
use Scraper\Source\ContentEntity\DisciplinaryStatementEntity;
use Scraper\Utility\TextHelper;

class DisciplinaryStatementList
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
                $contentEntities = array_merge($contentEntities, self::extractStatementsFromPage($resource, $resources));
            }
        }

        return $contentEntities;
    }

    public static function extractStatementsFromPage(Resource $resource, ResourceCollection $resources) {
        $crawler = $resource->getCrawler();
        $contentEntities = [];

        $h2 = $crawler->filter('h2');
        $year = $h2->first()->text();
        $year = TextHelper::tidyText($year);
        $year = intval($year);

        $links = $crawler->filter('h2 + ul > li > a');
        foreach ($links as $link) {
            $href = $link->getAttribute('href');
            $statementResource = self::getStatementResourceFromUrl($href, $resources);
            $title = TextHelper::tidyText($link->nodeValue);

            if ($statementResource) {
                $contentEntities[] = new DisciplinaryStatementEntity($title, $year, $statementResource);
            } else {
                trigger_error('Unable to find a PDF resource for disciplinary statement: "' . $title . '" with URL: ' . $href, E_USER_NOTICE);
            }
        }

        return $contentEntities;
    }

    private static function getStatementResourceFromUrl($href, ResourceCollection $resources) {
        $resource = $resources->findByRelativeUrl($href);
        if (!$resource) {
            return false;
        }

        if ($resource->getMimeType() == 'text/html') {
            // Extract the first link from the page, and use that as the resource.
            $href = $resource->getCrawler()->filter('a')->first()->attr('href');
            $resource = $resources->findByRelativeUrl($href);
        }

        $allowedMimeTypes = [
            'application/pdf',
            'application/msword',
        ];
        if (in_array($resource->getMimeType(), $allowedMimeTypes)) {
            return $resource;
        } else {
            return false;
        }
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
        return $sniffer->isDisciplinaryStatementsPage;
    }
}
