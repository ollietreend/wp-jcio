<?php

namespace Scraper\Source;

use Goutte\Client;
use Scraper\Utility\ObjectCache;

class Resource
{
    use ObjectCache;

    private $relativeUrl = null;
    private $absoluteUrl = null;
    private $meta = null;

    public function __construct($meta)
    {
        $this->meta = $meta;
        $this->absoluteUrl = $meta['absolute_url'];
        $this->relativeUrl = $this->getRelativeUrl();
    }

    public function getType()
    {
        $isInDocumentsDirectory = preg_match('/^(\.+\/)?documents\//i', $this->relativeUrl);
        $isADocumentFile = preg_match('/\.(pdf|docx?|xlsx?|pptx?)$/i', $this->relativeUrl);
        $isAHtmlFile = preg_match('/\.html?$/i', $this->relativeUrl);

        if ($isInDocumentsDirectory || $isADocumentFile) {
            return 'document';
        } else if ($isAHtmlFile) {
            return 'page';
        } else {
            trigger_error('Unrecognised resource type for URL: ' . $this->relativeUrl, E_USER_WARNING);
            return false;
        }
    }

    /**
     * Get the PageSniffer object for this resource.
     *
     * @return PageSniffer
     */
    public function getPageSniffer()
    {
        if (!$this->getType() == 'page') {
            return false;
        }

        $sniffer = $this->getObject('ResourceSniffer', function () {
            return new PageSniffer($this);
        });

        return $sniffer;
    }

    /**
     * Get a relative URL from the absolute URL.
     *
     * @return string
     */
    private function getRelativeUrl()
    {
        $absolute = $this->meta['absolute_url'];
        $baseUrl = $_ENV['IMPORT_URL'];

        // Substring absolute URL to get relative URL
        $relative = substr($absolute, strlen($baseUrl));

        // Remove directory up-levelling
        $relative = preg_replace('/\.\.\//', '', $relative);

        return $relative;
    }

    /**
     * Return the filesystem path to the file.
     *
     * @return string
     */
    public function getFilePath()
    {
        return $_ENV['IMPORT_PATH'] . $this->relativeUrl;
    }

    public function getMimeType()
    {
        return $this->getObject('mimeType', function () {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $this->getFilePath());
            finfo_close($finfo);
            return $mime;
        });
    }

    public function getCrawler()
    {
        if ($this->getType() !== 'page') {
            trigger_error(
                'Unable to get crawler for resource with type: ' . $this->getType(),
                E_USER_WARNING);
            return false;
        }

        $crawler = $this->getObject('crawler', function () {
            $goutte = new Client();
            return $goutte->request('GET', $this->absoluteUrl);
        });

        return $crawler;
    }

    /**
     * Getter override to allow for immutable but publicly accessible object properties.
     *
     * @param $property
     * @return mixed
     */
    public function __get($property)
    {
        $immutableProperties = [
            'relativeUrl',
            'absoluteUrl',
            'meta',
        ];

        if (property_exists($this, $property) && in_array($property, $immutableProperties)) {
            return $this->{$property};
        }

        $trace = debug_backtrace();
        trigger_error(
            'Undefined or inaccessible property via __get(): ' . $property .
            ' in ' . $trace[0]['file'] .
            ' on line ' . $trace[0]['line'],
            E_USER_NOTICE);
        return null;
    }
}
