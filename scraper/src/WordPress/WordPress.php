<?php

namespace Scraper\WordPress;

use Scraper\WordPress\Post\Attachment;

class WordPress {
    /**
     * Import a file into the WordPress media library.
     * Return the inserted media post ID.
     *
     * @param string $filePath Path of file to be imported.
     * @param int $associatedPostId The post ID the media is associated with
     * @param null $title Title for the sideloaded file (optional)
     * @param array $meta Meta fields to associate with the attachment post (optional)
     * @return Attachment Attachment post object
     * @throws \Exception
     */
    public static function importMedia($filePath, $associatedPostId, $title = null, $meta = []) {
        if (!file_exists($filePath)) {
            throw new \Exception('Unable to find file for import: ' . $filePath);
        }

        // Create temporary file
        $prefix = substr(md5(__FILE__), 0, 6);
        $tmpFilePath = tempnam(sys_get_temp_dir(), $prefix);

        // Duplicate file to be imported to the temporary file path
        copy($filePath, $tmpFilePath);
        clearstatcache(true, $tmpFilePath); // PHP bug: https://bugs.php.net/bug.php?id=65701

        // Import the file into WordPress
        $fileArray = [
            'name' => basename($filePath),
            'tmp_name' => $tmpFilePath,
        ];

        $success = media_handle_sideload($fileArray, $associatedPostId, $title);

        if (!is_int($success)) {
            throw new \Exception('Error importing file: ' . $filePath);
        }

        // Remove temporary file if it still exists (WordPress would usually move this)
        if (file_exists($tmpFilePath)) {
            unlink($tmpFilePath);
        }

        $attachment = Attachment::getById($success);

        // Add meta fields to attachment post
        if (!empty($meta)) {
            $attachment->save([
                'meta' => $meta,
            ]);
        }

        return $attachment;
    }
}
