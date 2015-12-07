# Import Script

This script is used to import content from the static RedDot version of the JCIO website into the WordPress installation.

Content types that are imported:

* Pages
* News posts
* Advisory committees
* Disciplinary statements

## Requirements

This script must be run on the command line.

* PHP 5.4+
* Command line access with:
    * WP-CLI (must be accessible as `wp` from a command line)
    * wget
* A working WordPress installation

## Running the Import

1. Run `scrape.sh` to download a static copy of the website.
2. Run `import.sh` to begin the import.
