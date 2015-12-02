<?php

namespace Scraper\WordPress\Post;

class DisciplinaryStatement extends Base {
    /**
     * The WordPress post type which this class represents.
     *
     * @var string
     */
    public static $postType = 'disciplinary_stmnt';

    /**
     * Delete the post.
     *
     * @return bool
     */
    public function delete() {
        $this->deleteAttachedMedia();
        return parent::delete();
    }
}
