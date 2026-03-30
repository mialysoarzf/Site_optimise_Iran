#!/usr/bin/env sh

set -eu

ASSETS_DIR="/var/www/html/public/assets"
FONTS_DIR="$ASSETS_DIR/fonts"
VENDOR_DIR="/var/www/html/public/vendor"

INTER_VERSION="5.0.17"
INTER_BASE_URL="https://cdn.jsdelivr.net/npm/@fontsource/inter@${INTER_VERSION}/files"
MERRI_VERSION="5.2.11"
MERRI_BASE_URL="https://cdn.jsdelivr.net/npm/@fontsource/merriweather@${MERRI_VERSION}/files"

if [ -d "$ASSETS_DIR" ]; then
  mkdir -p "$FONTS_DIR"

  for weight in 400 500 600 700; do
    file="inter-latin-${weight}-normal.woff2"
    target="$FONTS_DIR/$file"
    if [ ! -s "$target" ]; then
      echo "[assets] Downloading Inter ${weight} -> $target"
      curl -fL --retry 4 --retry-delay 2 -o "$target" "$INTER_BASE_URL/$file"
    fi
  done

  for weight in 400 700; do
    file="merriweather-latin-${weight}-normal.woff2"
    target="$FONTS_DIR/$file"
    if [ ! -s "$target" ]; then
      echo "[assets] Downloading Merriweather ${weight} -> $target"
      curl -fL --retry 4 --retry-delay 2 -o "$target" "$MERRI_BASE_URL/$file"
    fi
  done

fi

mkdir -p "$VENDOR_DIR"

exec apache2-foreground
