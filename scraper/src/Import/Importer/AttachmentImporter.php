<?php

namespace Scraper\Import\Importer;

use Scraper\WordPress\WordPress;
use Scraper\WordPress\Post\Attachment as Attachment;

class AttachmentImporter extends BaseImporter {
    /**
     * @param string $assetPath
     * @param int $associatedPostId
     * @param array $meta (optional) Meta fields be saved with the media item.
     * @return int
     * @throws \Exception
     */
    public static function import($assetPath, $associatedPostId, $meta = []) {
        $existingPost = Attachment::getByMeta([
            'reddot_import' => 1,
            'reddot_url' => $assetPath,
        ]);

        if ($existingPost) {
            if (static::$skipExisting) {
                // Stop processing
                return $existingPost;
            } else {
                // Delete existing post and continue with import
                $existingPost->delete();
            }
        }

        $meta = array_merge($meta, [
            'reddot_import' => true,
            'reddot_url' => $assetPath,
        ]);

        $absoluteAssetPath = static::$baseFilePath . $assetPath;
        $attachment = WordPress::importMedia($absoluteAssetPath, $associatedPostId, null, $meta);

        return $attachment;
    }
}
