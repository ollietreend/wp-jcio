<?php

namespace Scraper\WordPress\Post;

class Base {
    /**
     * The WordPress post type which this class represents.
     *
     * @var string
     */
    public static $postType = null;

    /**
     * Holds the associated WP_Post object.
     *
     * @var \WP_Post
     */
    public $WP_Post = null;

    /**
     * Class constructor
     *
     * @param \WP_Post $WP_Post
     */
    public function __construct(\WP_Post $WP_Post) {
        $this->WP_Post = $WP_Post;
    }

    /**
     * Delete the post.
     *
     * @return bool
     */
    public function delete() {
        $delete = wp_delete_post($this->WP_Post->ID, true);
        return ( $delete !== false );
    }

    /**
     * Get media files which are attached to this post.
     *
     * @return Attachment[]
     */
    public function getAttachedMedia() {
        $args = array(
            'post_parent' => $this->WP_Post->ID,
            'post_type' => 'attachment',
            'posts_per_page' => -1,
        );
        $children = get_children($args);

        $return = [];
        foreach ($children as $child) {
            $return[] = new Attachment($child);
        }
        return $return;
    }

    /**
     * Delete all media files attached to this post.
     */
    public function deleteAttachedMedia() {
        $attachedMedia = $this->getAttachedMedia();
        foreach ($attachedMedia as $media) {
            $media->delete();
        }
    }

    /**
     * Update the post with the supplied data.
     *
     * @param $save
     * @return bool
     * @throws \Exception
     */
    public function save($save) {
        if (isset($save['meta'])) {
            $meta = $save['meta'];
            unset($save['meta']);
        } else {
            $meta = [];
        }

        $newWpPost = clone $this->WP_Post;
        foreach ($save as $key => $value) {
            $newWpPost->$key = $value;
        }
        $savedId = wp_insert_post($newWpPost, true);

        if (!is_int($savedId)) {
            $message = 'Unable to save. Failed with error(s): ';
            $message .= join(', ', $savedId->get_error_messages());
            throw new \Exception($message);
        }

        foreach ($meta as $metaName => $metaValue) {
            update_post_meta($savedId, $metaName, $metaValue);
        }

        $this->updateWpPostObject();
        return true;
    }

    /**
     * Get the post URL.
     *
     * @return string
     */
    public function getUrl() {
        return apply_filters('the_permalink', get_permalink($this->WP_Post));
    }

    /**
     * Get ACF custom field for the current post.
     *
     * @param string $key
     * @return mixed|null|void
     */
    public function getField($key) {
        return get_field($key, $this->WP_Post->ID);
    }

    /**
     * Save ACF custom field for the current post.
     *
     * @param string $key
     * @param mixed $value
     * @return bool|int
     */
    public function saveField($key, $value) {
        return update_field($key, $value, $this->WP_Post->ID);
    }

    /**
     * Update the instance's WP_Post object with the latest version from the WordPress database.
     */
    private function updateWpPostObject() {
        $newObject = static::getById($this->WP_Post->ID);
        $this->WP_Post = $newObject->WP_Post;
        unset($newObject);
    }

    /**
     * Create a new post using the supplied data.
     * Post will be inserted into WordPress.
     *
     * @param $save
     * @return static
     * @throws \Exception
     */
    public static function create($save) {
        if (isset($save['meta'])) {
            $meta = $save['meta'];
            unset($save['meta']);
        } else {
            $meta = [];
        }

        $save = array_merge([
            'post_type' => static::$postType,
            'post_status' => 'publish',
        ], $save);

        $savedId = wp_insert_post($save, true);

        if (!is_int($savedId)) {
            $message = 'Unable to save. Failed with error(s): ';
            $message .= join(', ', $savedId->get_error_messages());
            throw new \Exception($message);
        }

        foreach ($meta as $metaName => $metaValue) {
            update_post_meta($savedId, $metaName, $metaValue);
        }

        return static::getById($savedId);
    }

    /**
     * Find post with specified meta and return instantiated object.
     *
     * @param array $meta Key/value array of meta fields.
     * @return static
     */
    public static function getByMeta($meta) {
        $args = [
            'meta_query' => static::getMetaQueryFromKeyValueArgs($meta),
        ];
        $query = static::getWpQuery($args);

        if ($query->have_posts()) {
            return new static($query->posts[0]);
        } else {
            return false;
        }
    }

    /**
     * Find all posts with specified meta and return an array of instantiated objects.
     *
     * @param array $meta Key/value array of meta fields.
     * @return static[]
     */
    public static function getAllByMeta($meta) {
        $args = [
            'meta_query' => static::getMetaQueryFromKeyValueArgs($meta),
        ];
        $query = static::getWpQuery($args);

        if ($query->have_posts()) {
            $return = [];
            foreach ($query->posts as $post) {
                $return[] = new static($post);
            }
            return $return;
        } else {
            return false;
        }
    }

    /**
     * Get post with specified ID.
     *
     * @param int $id WordPress post ID
     * @return static
     */
    public static function getById($id) {
        $args = [
            'p' => $id,
        ];
        $query = static::getWpQuery($args);

        if ($query->have_posts()) {
            return new static($query->posts[0]);
        } else {
            return false;
        }
    }

    /**
     * Produce an array in the format required for WP_Query 'meta_query' argument,
     * from a key-value array of meta fields.
     *
     * @param $args
     * @return array
     */
    protected static function getMetaQueryFromKeyValueArgs($args) {
        $metaQuery = [];

        foreach ($args as $metaName => $metaValue) {
            $metaQuery[] = [
                'key' => $metaName,
                'value' => $metaValue,
                'compare' => '=',
            ];
        }

        return $metaQuery;
    }

    /**
     * Get a new WP_Query object.
     *
     * @param $args
     * @return \WP_Query
     */
    protected static function getWpQuery($args) {
        $args['posts_per_page'] = 100;
        $args['post_type'] = static::$postType;
        return new \WP_Query($args);
    }
}
