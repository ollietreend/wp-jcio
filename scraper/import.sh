#!/bin/bash
##########################################################################
#
# IMPORTER SCRIPT
#
# Here we run our import.php script via wp-cli's `eval-file` command.
# This allows us to make use of WordPress functions in the import script.
#
# This script requires a global installation of WP-CLI, which should be
# accessible via the `wp` command.
#
##########################################################################

# Check for the existence of WP-CLI
command -v wp >/dev/null 2>&1 || { echo >&2 "This script requires WP-CLI to be installed. Please install it and try again. Bye for now."; exit 1; }

# Run import script
wp eval-file import.php

# That's all folks!
