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
use Scraper\Import\PageImporter;
use Scraper\Import\AdvisoryCommitteeImporter;
use Scraper\Import\DisciplinaryStatementImporter;

// Configure filesystem cache
FileSystemCache::$cacheDir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'cache';

// Configure importers
BaseImporter::$authorId = 2;
BaseImporter::$skipExisting = true;

$resources = Spider::createCollectionFromUrl($_ENV['IMPORT_URL']);

echo <<<EOF

+------------------------------------------------------------------------+
| IMPORTING PAGES                                                        |
+------------------------------------------------------------------------+

EOF;
$pages = PageList::getList($resources);
PageImporter::importMany($pages);

echo <<<EOF

+------------------------------------------------------------------------+
| IMPORTING NEWS POSTS                                                   |
+------------------------------------------------------------------------+

EOF;
$news = NewsPostList::getList($resources);
NewsPostImporter::importMany($news);

echo <<<EOF

+------------------------------------------------------------------------+
| IMPORTING ADVISORY COMMITTEES                                          |
+------------------------------------------------------------------------+

EOF;
$committees = AdvisoryCommitteeList::getList($resources);
AdvisoryCommitteeImporter::importMany($committees);

echo <<<EOF

+------------------------------------------------------------------------+
| IMPORTING DISCIPLINARY STATEMENTS                                      |
+------------------------------------------------------------------------+

EOF;
$statements = DisciplinaryStatementList::getList($resources);
DisciplinaryStatementImporter::importMany($statements);
