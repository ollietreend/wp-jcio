<?php

namespace Scraper\Utility;

trait LazyProperties
{
    use ObjectCache;

    /**
     * Array of properties which this should be lazily evaluated.
     *
     * Also accepts boolean values:
     *  - false = nothing is lazy (this trait will do nothing)
     *  - true = every property is lazy
     *
     * @var array|bool $lazyProperties
     */

    /**
     * Getter to provide properties.
     * Properties are handled by associated getProperty methods, and the output is cached to speed up subsequent calls.
     * Example: $obj->name will run $obj->getName() on first call, and then subsequently refer to the cache.
     *
     * @param $property
     * @return mixed
     */
    public function __get($property)
    {
        if (!isset($this->lazyProperties)) {
            $this->lazyProperties = false;
        }

        if ($this->lazyProperties === false) {
            $trace = debug_backtrace();
            trigger_error(
                'Lazy properties are disabled: ' . $property .
                ' in ' . $trace[0]['file'] .
                ' on line ' . $trace[0]['line'],
                E_USER_NOTICE);
            return null;
        }

        if ($this->lazyProperties === true || in_array($property, $this->lazyProperties)) {
            $getMethod = 'get' . ucfirst($property);

            if (method_exists($this, $getMethod)) {
                return $this->getObject($property, array($this, $getMethod));
            }
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
