<?php

namespace Scraper\Import\PageVariations;

use Scraper\Utility\TextHelper;
use Symfony\Component\DomCrawler\Crawler;

class LinkListPageVariations extends BasePageVariations
{
    /**
     * Page content to import.
     *
     * @return string
     */
    public function getContent() {
        $content = new Crawler($this->entity->content);

        // Reformat link-list markup
        // Wrap ul with shortcode
        $content->filter('ul.link-list')->each(function($ul) {
            /* @var Crawler $ul */
            /* @var \DOMElement $domelement */
            foreach ($ul as $domelement) {
                $domelement->removeAttribute('class');

                $shortcodeOpen = $domelement->ownerDocument->createElement('p', '[file_list]');
                $shortcodeClose = $domelement->ownerDocument->createElement('p', '[/file_list]');

                $domelement->parentNode->insertBefore($shortcodeOpen, $domelement);
                $domelement->parentNode->insertBefore($shortcodeClose, $domelement->nextSibling);
            }

            $lis = $ul->filter('li');

            foreach ($lis as $li) {
                $li->removeAttribute('class');

                foreach ($li->childNodes as $node) {
                    if (get_class($node) == 'DOMText') {
                        $node->nodeValue = preg_replace('/\s*\[[A-Z]+ [0-9\.a-zA-Z]+\]/', '', $node->nodeValue);
                    }
                    if (get_class($node) == 'DOMElement' && $node->tagName == 'a') {
                        $node->removeAttribute('target');
                        $node->removeAttribute('onclick');
                        $node->removeAttribute('title');
                    }
                }
            }
        });

        // Filter and clean HTML
        $html = $content->html();
        $html = TextHelper::tidyHtml($html);

        return $html;
    }
}
