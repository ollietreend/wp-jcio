<?php

namespace Scraper\Source\ContentEntity;

use DateTime;

class NewsPostEntity extends BaseContentEntity
{
    public function __construct($content)
    {
        if (!isset($content['title']) ||
            !isset($content['date']) ||
            !isset($content['content']) ||
            !is_string($content['title']) ||
            !($content['date'] instanceof DateTime) ||
            !is_string($content['content'])
        ) {
            throw new \Exception('Malformed content array supplied.');
        }

        $this->title = $content['title'];
        $this->date = $content['date'];
        $this->content = $content['content'];
    }
}
