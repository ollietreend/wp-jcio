<?php

namespace Scraper\Import;

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
    public static function import($entity)
    {
        echo "Importing news post: " . $entity->title . "\n";

        $existingPost = Post::getByMeta([
            'reddot_import' => 1,
            'reddot_news_post_title' => $entity->title,
        ]);

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
            'meta' => [
                'reddot_import' => 1,
                'reddot_news_post_title' => $entity->title,
            ],
        ];
    }
}
