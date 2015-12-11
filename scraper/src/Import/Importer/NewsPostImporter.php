<?php

namespace Scraper\Import\Importer;

use Scraper\Source\ContentEntity\NewsPostEntity;
use Scraper\WordPress\Post\Post;

class NewsPostImporter extends BaseImporter
{
    /**
     * Import the supplied content entities.
     *
     * @param NewsPostEntity $entity
     * @return Post
     */
    public static function import(NewsPostEntity $entity)
    {
        echo $entity->title . "\n";

        $existingPost = Post::getByMeta(static::getReddotMeta($entity));

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
        $newPost = Post::create($save);
        return $newPost;
    }

    private static function getSaveData(NewsPostEntity $entity)
    {
        return [
            'post_title' => $entity->title,
            'post_date' => $entity->date->format('Y-m-d H:i:s'),
            'post_content' => $entity->content,
            'post_status' => 'publish',
            'post_type' => Post::$postType,
            'post_author' => static::$authorId,
            'meta' => static::getReddotMeta($entity),
        ];
    }

    private static function getReddotMeta(NewsPostEntity $entity)
    {
        return [
            'reddot_import' => 1,
            'reddot_entity_title' => $entity->title,
        ];
    }
}
