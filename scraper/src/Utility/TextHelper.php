<?php

namespace Scraper\Utility;

use tidy;

class TextHelper
{
    static public function tidyText($input)
    {
        // Strip tags
        $output = strip_tags($input);

        // Replace &nbsp; with space
        // Why? Because preg_replace's \s token doesn't match non-breaking space characters.
        $output = htmlentities($output);
        $output = str_replace('&nbsp;', ' ', $output);
        $output = html_entity_decode($output);

        // Replace multiple spaces with single spaces
        $output = preg_replace('/\s+/', ' ', $output);

        // Trip the string for whitespace
        $output = trim($output);

        return $output;
    }

    /**
     * Clean HTML by running it through the PHP Tidy class.
     *
     * @see http://php.net/manual/en/book.tidy.php
     * @param $html
     * @return string
     */
    public static function tidyHtml($html)
    {
        $config = [
            'bare' => true,
            'enclose-text' => true,
            'hide-comments' => true,
            'logical-emphasis' => true,
            'show-body-only' => true,
            'wrap' => 0,
            'drop-empty-paras' => true,
        ];

        $tidy = new tidy();
        $tidy->parseString($html, $config);
        $tidy->cleanRepair();

        return (string)$tidy;
    }
}
