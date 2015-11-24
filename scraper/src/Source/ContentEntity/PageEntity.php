<?php

namespace Scraper\Source\ContentEntity;

use Scraper\Source\Resource;
use Scraper\Utility\ObjectCache;

class PageEntity
{
    use ObjectCache;

    public $resource = null;

    public function __construct(Resource $resource)
    {
        $this->resource = $resource;
    }

    public function getTitle()
    {
        $crawler = $this->resource->getCrawler();
        $h1 = $crawler->filter('h1');
        $title = $h1->text();
        $title = utf8_decode($title);
        $title = trim($title);
        return $title;
    }

    public function getContent()
    {
        $crawler = $this->resource->getCrawler();

        $elContent = $crawler->filter('#content');

        eval(\Psy\sh());
    }

    /**
     * Getter to provide content properties.
     * Properties are handled by associated getProperty methods, and the output is cached to speed up subsequent calls.
     *
     * @param $property
     * @return mixed
     */
    public function __get($property)
    {
        $contentProperties = [
            'title',
            'content',
        ];

        $getMethod = 'get' . ucfirst($property);

        if (method_exists($this, $getMethod) && in_array($property, $contentProperties)) {
            return $this->getObject($property, array($this, $getMethod));
        }

        $trace = debug_backtrace();
        trigger_error(
            'Undefined or inaccessible property via __get(): ' . $property .
            ' in ' . $trace[0]['file'] .
            ' on line ' . $trace[0]['line'],
            E_USER_NOTICE);
        return null;
    }
}
