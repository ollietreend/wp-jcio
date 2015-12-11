<?php

namespace Scraper\Import\PageVariations;

use Scraper\Utility\TextHelper;
use Symfony\Component\DomCrawler\Crawler;

class ChecklistPageVariations extends BasePageVariations
{
    /**
     * The page template to use.
     * False for default page template.
     *
     * @var string|false
     */
    public $pageTemplate = 'template-checklist';

    /**
     * Page content to import.
     *
     * @return string
     */
    public function getContent() {
        $content = new Crawler($this->entity->content);

        // Remove unwanted elements from content
        $leftColumn = $content->filter('.left-column');
        $leftColumn->nextAll()->each(function($crawler) {
            foreach ($crawler as $node) {
                $node->parentNode->removeChild($node);
            }
        });
        $leftColumn->each(function($crawler) {
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

        $crawler = new Crawler($this->entity->content);

        // Tick fields
        $tickColumn = $crawler->filter('.left-column');
        $tickHeading = $this->getAcfHeadingFromColumn($tickColumn);
        $tickList = $this->getAcfListFromColumn($tickColumn);

        // Cross fields
        $crossColumn = $crawler->filter('.right-column');
        $crossHeading = $this->getAcfHeadingFromColumn($crossColumn);
        $crossList = $this->getAcfListFromColumn($crossColumn);

        // Content after columns
        $content = new Crawler($this->entity->content);
        $afterLastColumn = false;
        $content->filter('body')->children()->each(function($crawler) use (&$afterLastColumn) {
            foreach ($crawler as $node) {
                if ($node->tagName == 'div' && $node->getAttribute('class') == 'right-column') {
                    $node->parentNode->removeChild($node);
                    $afterLastColumn = true;
                }

                if ($node->tagName == 'div' && $node->getAttribute('class') == 'clear-both') {
                    $node->parentNode->removeChild($node);
                }

                if (!$afterLastColumn) {
                    $node->parentNode->removeChild($node);
                }
            }
        });
        $contentAfter = TextHelper::tidyHtml($content->html());

        // Add to fields array
        $fields['field_5655e2d242f66'] = $tickHeading;
        $fields['field_5655e31242f67'] = $tickList;
        $fields['field_5655e35742f69'] = $crossHeading;
        $fields['field_5655e36842f6a'] = $crossList;
        $fields['field_5655e3f5aad51'] = $contentAfter;

        return $fields;
    }

    private function getAcfHeadingFromColumn(Crawler $column) {
        $heading = $column->filter('h2')->text();
        $heading = TextHelper::tidyText($heading);
        return $heading;
    }

    private function getAcfListFromColumn(Crawler $column) {
        $list = [];
        $bulletPoints = $column->filter('ul > li');
        $bulletPoints->each(function($bullet) use (&$list) {
            $text = TextHelper::tidyText($bullet->text());
            $list[] = [
                'text' => $text,
            ];
        });
        return $list;
    }
}
