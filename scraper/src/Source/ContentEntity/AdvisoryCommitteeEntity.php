<?php

namespace Scraper\Source\ContentEntity;

use DOMElement;
use Scraper\Utility\LazyProperties;
use Scraper\Utility\TextHelper;
use Symfony\Component\DomCrawler\Crawler;

class AdvisoryCommitteeEntity
{
    use LazyProperties;

    private $lazyProperties = [
        'title',
        'address',
    ];

    /**
     * @var DOMElement
     */
    private $row = null;

    /**
     * Class constructor
     *
     * @param $row
     * @throws \Exception
     */
    public function __construct(DOMElement $row)
    {
        $this->row = $row;
    }

    /**
     * Committee name
     *
     * @return string
     */
    public function getTitle() {
        $crawler = $this->getRowCrawler();
        $td = $crawler->filter('td')->first();
        $text = $td->text();
        $text = TextHelper::tidyText($text);
        return $text;
    }

    /**
     * Contact address
     *
     * @return string
     */
    public function getAddress() {
        $crawler = $this->getRowCrawler();
        $filter = $crawler->filter('td')->last();

        // Clone elements into new Crawler instance
        // This allows us to modify the DOM without modifying the DOM of the original crawler object.
        $content = new Crawler();
        foreach ($filter as $matchedElement) {
            $content->add(clone $matchedElement);
        }

        // Remove unwanted elements from content
        $content->filter('p')->each(function($crawler) {
            foreach ($crawler as $node) {
                $node->parentNode->removeChild($node);
            }
        });

        $html = $content->html();
        $text = strip_tags($html);
        $text = TextHelper::tidyText($text);
        $text = preg_replace('/\n\s+/', "\n", $text);

        return $text;
    }

    /**
     * Get crawler containing the row node
     *
     * @return bool
     */
    private function getRowCrawler() {
        return $this->getObject('rowCrawler', function() {
            $crawler = new Crawler();
            $crawler->add($this->row);
            return $crawler;
        });
    }
}
