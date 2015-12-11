<?php

namespace Scraper\Source;

use ArrayObject;

class ResourceCollection extends ArrayObject
{
    public function filterByType($type) {
        $filteredCollection = new self;

        foreach ($this as $resource) {
            if ($resource->getType() == $type) {
                $filteredCollection->append($resource);
            }
        }

        return $filteredCollection;
    }

    public function findByRelativeUrl($url) {
        foreach ($this as $resource) {
            if ($resource->relativeUrl == $url) {
                return $resource;
            }
        }

        return false;
    }
}
