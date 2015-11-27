<?php

namespace Scraper\Import;

use Scraper\Import\PageVariations\AdvisoryCommitteePageVariations;
use Scraper\Import\PageVariations\BasePageVariations;
use Scraper\Import\PageVariations\ChecklistPageVariations;
use Scraper\Import\PageVariations\HomePageVariations;
use Scraper\Import\PageVariations\NewsPageVariations;
use Scraper\Source\ContentEntity\PageEntity;
use Scraper\WordPress\Post\Page;

class PageImporter extends BaseImporter
{
    /**
     * Import the supplied content entities.
     *
     * @param PageEntity $entity
     * @return Page
     */
    public static function import(PageEntity $entity)
    {
        echo "Importing page: " . $entity->title . "\n";

        $existingPost = Page::getByMeta(static::getReddotMeta($entity));

        if ($existingPost) {
            if (static::$skipExisting) {
                // Stop processing
                return true;
            } else {
                // Delete existing post and continue with import
                $existingPost->delete();
            }
        }

        $variations = static::getPageVariations($entity);
        $save = static::getSaveData($entity, $variations);
        $newPost = Page::create($save);

        if ($variations->acfFields) {
            foreach ($variations->acfFields as $key => $value) {
                $newPost->saveField($key, $value);
            }
        }

        if ($variations->isFrontPage) {
            update_option('page_on_front', $newPost->WP_Post->ID);
            update_option('show_on_front', 'page');
        }

        if ($variations->isNewsPage) {
            update_option('page_for_posts', $newPost->WP_Post->ID);
        }

        return $newPost;
    }

    private static function getSaveData(PageEntity $entity, BasePageVariations $variations = null)
    {
        $meta = static::getReddotMeta($entity);

        if ($variations->pageTemplate) {
            $meta['_wp_page_template'] = $variations->pageTemplate . '.php';
        }

        return [
            'post_title' => $entity->title,
            'post_content' => $variations->content,
            'post_status' => 'publish',
            'post_type' => Page::$postType,
            'post_author' => static::$authorId,
            'meta' => $meta,
        ];
    }

    private static function getReddotMeta(PageEntity $entity)
    {
        return [
            'reddot_import' => 1,
            'reddot_url' => $entity->resource->relativeUrl,
        ];
    }

    private static function getPageVariations(PageEntity $entity)
    {
        $thePage = $entity->resource->getPageSniffer();

        if ($thePage->isHomepage) {
            return new HomePageVariations($entity);
        } else if ($thePage->isNewsPage) {
            return new NewsPageVariations($entity);
        } else if ($thePage->isAdvisoryCommitteePage) {
            return new AdvisoryCommitteePageVariations($entity);
        } else if ($thePage->hasAChecklist) {
            return new ChecklistPageVariations($entity);
        } else {
            return new BasePageVariations($entity);
        }
    }
}
