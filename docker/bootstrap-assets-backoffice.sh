#!/usr/bin/env sh

set -eu

ASSETS_DIR="/var/www/html/public/assets"
FONTS_DIR="$ASSETS_DIR/fonts"
INTER_VERSION="5.0.17"
INTER_BASE_URL="https://cdn.jsdelivr.net/npm/@fontsource/inter@${INTER_VERSION}/files"

TINYMCE_VERSION="7.9.1"
TINYMCE_DIR="/var/www/html/public/vendor/tinymce"
TINYMCE_TMP="/tmp/tinymce.tgz"

if [ -d "$ASSETS_DIR" ]; then
  mkdir -p "$FONTS_DIR"

  for weight in 400 500 600 700; do
    file="inter-latin-${weight}-normal.woff2"
    target="$FONTS_DIR/$file"

    if [ ! -s "$target" ]; then
      echo "[assets] Downloading Inter ${weight} to $target"
      curl -fL --retry 4 --retry-delay 2 -o "$target" "$INTER_BASE_URL/$file"
    fi
  done
fi

if [ ! -s "$TINYMCE_DIR/tinymce.min.js" ]; then
  echo "[assets] Downloading TinyMCE ${TINYMCE_VERSION} (self-hosted)"
  rm -rf "$TINYMCE_DIR"
  mkdir -p "$TINYMCE_DIR"

  curl -fL --retry 4 --retry-delay 2 -o "$TINYMCE_TMP" "https://registry.npmjs.org/tinymce/-/tinymce-${TINYMCE_VERSION}.tgz"
  tar -xzf "$TINYMCE_TMP" -C /tmp
  cp -R /tmp/package/. "$TINYMCE_DIR/"
  rm -rf /tmp/package "$TINYMCE_TMP"
fi

exec apache2-foreground
