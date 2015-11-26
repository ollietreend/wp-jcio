<?php
/**
 * Import script
 */

// Ensure that this script is being called via WP-CLI
if (!defined('WP_CLI') || !WP_CLI) {
    echo "Please do not run this script directly. Use import.sh instead.\n";
    die(1);
}

// Include composer autoloader
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

use Scraper\Source\Spider;
use Scraper\Source\ContentLister\PageList;
use Scraper\Source\ContentLister\NewsPostList;
use Scraper\Source\ContentLister\DisciplinaryStatementList;
use Scraper\Source\ContentLister\AdvisoryCommitteeList;
use Scraper\Import\BaseImporter;
use Scraper\Import\NewsPostImporter;

// Configure filesystem cache
FileSystemCache::$cacheDir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'cache';

// Configure importers
BaseImporter::$authorId = 2;
BaseImporter::$skipExisting = true;

$resources = Spider::createCollectionFromUrl($_ENV['IMPORT_URL']);

//$pages = PageList::getList($resources);
$news = NewsPostList::getList($resources);
//$statements = DisciplinaryStatementList::getList($resources);
//$committees = AdvisoryCommitteeList::getList($resources);

NewsPostImporter::importMany($news);

//eval(\Psy\sh());
