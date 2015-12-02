<?php

namespace Scraper\WordPress\Post;

class Attachment extends Base {
    /**
     * The WordPress post type which this class represents.
     *
     * @var string
     */
    public static $postType = 'attachment';

    /**
     * Get a new WP_Query object.
     *
     * @param $args
     * @return \WP_Query
     */
    protected static function getWpQuery($args) {
        // We need to overwrite post_status from attachment as it does not default to 'publish'
        // See: https://codex.wordpress.org/Class_Reference/WP_Query#Type_Parameters
        $args['post_status'] = 'inherit';
        return parent::getWpQuery($args);
    }

    /**
     * Get the post URL.
     * For attachments, we'll return the path to the file rather than the attachment page.
     *
     * @return string
     */
    public function getUrl() {
        return \wp_get_attachment_url($this->WP_Post->ID);
    }
}
