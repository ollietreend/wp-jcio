<?php

namespace Scraper\Import\PageVariations;

use Scraper\Utility\TextHelper;
use Symfony\Component\DomCrawler\Crawler;

class DisciplinaryStatementsChildPageVariations extends BasePageVariations
{
    /**
     * The page template to use.
     * False for default page template.
     *
     * @var string|false
     */
    public $pageTemplate = 'template-disciplinary-statements-child-page';

    /**
     * Page content to import.
     *
     * @return string
     */
    public function getContent() {
        $content = new Crawler($this->entity->content);

        // Remove unwanted elements from content
        $content->filter('#tabs, #tabs + h2')->each(function($crawler) {
            foreach ($crawler as $node) {
                $node->parentNode->removeChild($node);
            }
        });

        // Filter and clean HTML
        $html = $content->html();
        $html = TextHelper::tidyHtml($html);

        return $html;
    }
}
