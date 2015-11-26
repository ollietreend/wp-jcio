<?php

namespace Scraper\Import;

class BaseImporter {
    /**
     * User ID to use as author of imported posts.
     *
     * @var int
     */
    public static $authorId = null;

    /**
     * Whether to skip importing posts which have already been imported.
     * If false, existing post will be deleted and a new post created.
     *
     * @var bool
     */
    public static $skipExisting = true;

    /**
     * Filesystem path to the base of the import content directory.
     *
     * @var string
     */
    public static $baseFilePath = null;

    /**
     * Import an array of content entities.
     *
     * @param array $entities
     */
    public static function importMany($entities) {
        foreach ($entities as $entity) {
            static::import($entity);
        }
    }
}
