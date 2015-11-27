<?php

namespace Scraper\Import;

use Scraper\Source\ContentEntity\AdvisoryCommitteeEntity;
use Scraper\WordPress\Post\AdvisoryCommittee;

class AdvisoryCommitteeImporter extends BaseImporter
{
    /**
     * Import the supplied content entities.
     *
     * @param AdvisoryCommitteeEntity $entity
     * @return AdvisoryCommittee
     */
    public static function import(AdvisoryCommitteeEntity $entity)
    {
        echo "Importing advisory committee: " . $entity->title . "\n";

        $existingPost = AdvisoryCommittee::getByMeta(static::getReddotMeta($entity));

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
        $newPost = AdvisoryCommittee::create($save);

        // Save address custom field
        $newPost->saveField('field_565868740dbc4', $entity->address);

        return $newPost;
    }

    private static function getSaveData(AdvisoryCommitteeEntity $entity)
    {
        return [
            'post_title' => $entity->title,
            'post_status' => 'publish',
            'post_type' => AdvisoryCommittee::$postType,
            'post_author' => static::$authorId,
            'meta' => static::getReddotMeta($entity),
        ];
    }

    private static function getReddotMeta(AdvisoryCommitteeEntity $entity)
    {
        return [
            'reddot_import' => 1,
            'reddot_entity_title' => $entity->title,
        ];
    }
}
