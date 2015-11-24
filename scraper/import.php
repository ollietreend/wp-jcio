<?php

/**
 * Import script
 */

require 'vendor/autoload.php';

// Setup environment variables
$dotenv = new josegonzalez\Dotenv\Loader('.env');
$dotenv->parse()->toEnv();

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', true);

// Set the default timezone (required by \Symfony\Component\BrowserKit\Cookie)
date_default_timezone_set('Europe/London');

// Set page time limit
set_time_limit(0);

// Includes
//require '../wp-load.php';
//require '../wp-admin/includes/image.php';
//require '../wp-admin/includes/file.php';
//require '../wp-admin/includes/media.php';

// Configure filesystem cache
FileSystemCache::$cacheDir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'cache';

$resources = \Scraper\Source\Spider::createCollectionFromUrl($_ENV['IMPORT_URL']);

//$pages = \Scraper\Source\ContentLister\PageList::getList($resources);
//$news = \Scraper\Source\ContentLister\NewsPostList::getList($resources);
$statements = \Scraper\Source\ContentLister\DisciplinaryStatementList::getList($resources);

/*foreach ($resources as $resource) {
    if (!file_exists($resource->getFilePath())) {
        echo $resource->getFilePath() . "\n";
    }
}*/

//$doPages = array_map(function($page) {
//    return $page->resource->relativeUrl;
//}, $pages);

foreach ($statements as $statement) {
    echo $statement->date->format('Y-m-d H:i:s') . "\n";
}

eval(\Psy\sh());
