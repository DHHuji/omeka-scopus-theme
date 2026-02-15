#!/usr/bin/env sh
set -e

MODULES_DIR=/var/www/html/modules
CSVIMPORT_VERSION=2.6.2
COMMON_VERSION=3.4.78
REFERENCE_VERSION=3.4.57
ADVANCEDSEARCH_VERSION=3.4.56

download_module() {
  MODULE_NAME="$1"
  MODULE_VERSION="$2"
  MODULE_URL="$3"
  MODULE_DIR="$MODULES_DIR/$MODULE_NAME"
  MODULE_ZIP="$MODULE_NAME-$MODULE_VERSION.zip"
  TMP_ZIP="/tmp/$MODULE_ZIP"

  if [ -d "$MODULE_DIR" ]; then
    echo "$MODULE_NAME already present at $MODULE_DIR. Skipping download."
    return 0
  fi

  mkdir -p "$MODULES_DIR"

  if command -v curl >/dev/null 2>&1; then
    echo "Downloading $MODULE_URL"
    curl -L -o "$TMP_ZIP" "$MODULE_URL"
  elif command -v wget >/dev/null 2>&1; then
    echo "Downloading $MODULE_URL"
    wget -O "$TMP_ZIP" "$MODULE_URL"
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
  echo "$MODULE_NAME downloaded to $MODULE_DIR"
}

download_module \
  "CSVImport" \
  "$CSVIMPORT_VERSION" \
  "https://github.com/omeka-s-modules/CSVImport/releases/download/v${CSVIMPORT_VERSION}/CSVImport-${CSVIMPORT_VERSION}.zip"

download_module \
  "Common" \
  "$COMMON_VERSION" \
  "https://github.com/Daniel-KM/Omeka-S-module-Common/releases/download/${COMMON_VERSION}/Common-${COMMON_VERSION}.zip"

download_module \
  "Reference" \
  "$REFERENCE_VERSION" \
  "https://github.com/Daniel-KM/Omeka-S-module-Reference/releases/download/${REFERENCE_VERSION}/Reference-${REFERENCE_VERSION}.zip"

download_module \
  "AdvancedSearch" \
  "$ADVANCEDSEARCH_VERSION" \
  "https://github.com/Daniel-KM/Omeka-S-module-AdvancedSearch/releases/download/${ADVANCEDSEARCH_VERSION}/AdvancedSearch-${ADVANCEDSEARCH_VERSION}.zip"
