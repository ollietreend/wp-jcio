<?php

/**
 * Trait to store objects in a cache.
 * This can be used to avoid repeatedly instantiating objects by keeping them in a simple cache.
 */

namespace Scraper\Utility;

trait ObjectCache
{
    /**
     * Holds the cached objects.
     *
     * @var array
     */
    private $objectCache = [];

    /**
     * Adds an object to the cache.
     *
     * @param $key
     * @param $value
     */
    public function setObject($key, $value)
    {
        $this->objectCache[$key] = $value;
    }

    /**
     * Gets an object from the cache.
     * If object does not yet exist but a callable was passed, execute it to instantiate the desired object.
     *
     * @param string $key
     * @param callable $init
     * @return bool
     */
    public function getObject($key, $init = null)
    {
        if (isset($this->objectCache[$key])) {
            return $this->objectCache[$key];
        } else if (is_callable($init)) {
            $object = $init();
            $this->setObject($key, $object);
            return $object;
        } else {
            return false;
        }
    }

    /**
     * Unset an object from the cache.
     *
     * @param $key
     */
    public function unsetObject($key)
    {
        if (isset($this->objectCache[$key])) {
            unset($this->objectCache[$key]);
        }
    }
}
