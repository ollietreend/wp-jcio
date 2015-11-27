<?php

namespace Scraper\Import;

use Scraper\Source\ContentEntity\DisciplinaryStatementEntity;
use Scraper\WordPress\Post\DisciplinaryStatement;
use Scraper\WordPress\WordPress;

class DisciplinaryStatementImporter extends BaseImporter
{
    /**
     * Import the supplied content entities.
     *
     * @param DisciplinaryStatementEntity $entity
     * @return DisciplinaryStatement
     */
    public static function import(DisciplinaryStatementEntity $entity)
    {
        echo $entity->title . "\n";

        $existingPost = DisciplinaryStatement::getByMeta(static::getReddotMeta($entity));

        if ($existingPost) {
            if (static::$skipExisting) {
                // Stop processing
                return true;
            } else {
                // Delete existing post and continue with import
                $existingPost->delete();
            }
        }

        $save = static::getSaveData($entity);
        $newPost = DisciplinaryStatement::create($save);

        // Import statement file
        $meta = [
            'reddot_import' => 1,
            'reddot_url' => $entity->resource->relativeUrl,
        ];
        $attachment = WordPress::importMedia($entity->filePath, $newPost->WP_Post->ID, $entity->title, $meta);
        $newPost->saveField('field_56586954bb45f', $attachment->WP_Post->ID);

        return $newPost;
    }

    private static function getSaveData(DisciplinaryStatementEntity $entity)
    {
        return [
            'post_title' => $entity->title,
            'post_date' => $entity->date->format('Y-m-d H:i:s'),
            'post_author' => static::$authorId,
            'meta' => static::getReddotMeta($entity),
        ];
    }

    private static function getReddotMeta(DisciplinaryStatementEntity $entity)
    {
        return [
            'reddot_import' => 1,
            'reddot_entity_title' => $entity->title,
        ];
    }
}
