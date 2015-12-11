<?php
/**
 * Created by PhpStorm.
 * User: ollietreend
 * Date: 26/11/2015
 * Time: 14:29
 */

namespace Scraper\Import\PageVariations;


use Scraper\Source\ContentEntity\PageEntity;
use Scraper\Utility\LazyProperties;

class BasePageVariations
{
    use LazyProperties;

    /**
     * Define our lazy properties
     *
     * @var array
     */
    protected $lazyProperties = [
        'content',
        'acfFields',
    ];

    /**
     * Is this the front page?
     *
     * @var bool
     */
    public $isFrontPage = false;

    /**
     * Is this the news/blog index page?
     *
     * @var bool
     */
    public $isNewsPage = false;

    /**
     * The page template to use.
     * False for default page template.
     *
     * @var string|false
     */
    public $pageTemplate = false;

    /**
     * Holds the page entity object.
     */
    public $entity = null;

    /**
     * Class constructor
     *
     * @param PageEntity $entity
     */
    public function __construct(PageEntity $entity) {
        $this->entity = $entity;
    }

    /**
     * Page content to import.
     *
     * @return string
     */
    public function getContent() {
        return $this->entity->content;
    }

    /**
     * ACF fields to import.
     *
     * @return array|false
     */
    public function getAcfFields() {
        return false;
    }
}
