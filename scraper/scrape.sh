#!/bin/bash

DIRECTORY="import_content"
CACHE="cache"

if [ -d "$DIRECTORY" ]; then
    echo "Directory \"$DIRECTORY\" already exists."
    read -p "Overwrite? [y/N]: " -n 1 -r OVERWRITE
    echo ""
    if [[ ! $OVERWRITE =~ ^[Yy]$ ]]; then
        echo "Aborting."
        exit 1
    else
        rm -Rf "$DIRECTORY"
    fi
fi

# Download the site
wget --mirror --adjust-extension --page-requisites --execute robots=off --convert-links http://judicialconduct.judiciary.gov.uk/
mv "judicialconduct.judiciary.gov.uk" "$DIRECTORY"

# Remove old cache files
if [ -d "$CACHE" ]; then
    rm -f $CACHE/*
fi

echo "Finished downloading site."
