<?php

namespace Scraper\Source;

use Scraper\Utility\LazyProperties;

class PageSniffer
{
    use LazyProperties;

    /**
     * Array of properties which this should be lazily evaluated.
     *
     * Also accepts boolean values:
     *  - false = nothing is lazy (this trait will do nothing)
     *  - true = every property is lazy
     *
     * @var array|bool
     */
    protected $lazyProperties = [
        'pageHasHeader',
        'isNewsPage',
        'isUnwantedPage',
        'isDisciplinaryStatementsPage',
    ];

    /**
     * Holds the resource object which we're sniffing.
     *
     * @var Resource
     */
    public $resource = null;

    /**
     * Class constructor
     *
     * @param \Scraper\Source\Resource $resource
     */
    public function __construct(Resource $resource)
    {
        $this->resource = $resource;
    }

    public function getPageHasHeader()
    {
        $crawler = $this->resource->getCrawler();
        $header = $crawler->filter('#header');
        return (count($header) > 0);
    }

    public function getIsNewsPage()
    {
        return ($this->resource->relativeUrl == 'latest-news.htm');
    }

    public function getIsUnwantedPage()
    {
        $excludeUrls = [
            'sitemap.htm', // Sitemap is empty
        ];
        return (in_array($this->resource->relativeUrl, $excludeUrls));
    }

    public function getIsDisciplinaryStatementsPage()
    {
        return (
            stripos($this->resource->meta['title'], 'Disciplinary statements') !== false &&
            stripos($this->resource->meta['title'], 'Publication Policy') === false
        );
    }
}
