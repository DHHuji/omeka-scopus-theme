#!/usr/bin/env sh
set -e

MODULES_DIR=/var/www/html/modules
CSVIMPORT_DIR="$MODULES_DIR/CSVImport"
CSVIMPORT_VERSION=2.6.2
CSVIMPORT_ZIP="CSVImport-${CSVIMPORT_VERSION}.zip"
CSVIMPORT_URL="https://github.com/omeka-s-modules/CSVImport/releases/download/v${CSVIMPORT_VERSION}/${CSVIMPORT_ZIP}"

if [ -d "$CSVIMPORT_DIR" ]; then
  echo "CSVImport already present at $CSVIMPORT_DIR. Skipping download."
  exit 0
fi

mkdir -p "$MODULES_DIR"

TMP_ZIP="/tmp/$CSVIMPORT_ZIP"

if command -v curl >/dev/null 2>&1; then
  echo "Downloading $CSVIMPORT_URL"
  curl -L -o "$TMP_ZIP" "$CSVIMPORT_URL"
elif command -v wget >/dev/null 2>&1; then
  echo "Downloading $CSVIMPORT_URL"
  wget -O "$TMP_ZIP" "$CSVIMPORT_URL"
else
  echo "ERROR: Neither curl nor wget is available to download modules." >&2
  exit 1
fi

if command -v unzip >/dev/null 2>&1; then
  unzip -q "$TMP_ZIP" -d "$MODULES_DIR"
else
  echo "ERROR: unzip is not available to extract modules." >&2
  exit 1
fi

rm -f "$TMP_ZIP"

echo "CSVImport downloaded to $CSVIMPORT_DIR"
