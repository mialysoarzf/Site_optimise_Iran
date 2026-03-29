#!/usr/bin/env sh

ASSETS_DIR="/var/www/html/public/assets"
FONTS_DIR="$ASSETS_DIR/fonts"
INTER_VERSION="5.0.17"
BASE_URL="https://cdn.jsdelivr.net/npm/@fontsource/inter@${INTER_VERSION}/files"

if [ -d "$ASSETS_DIR" ]; then
  mkdir -p "$FONTS_DIR"

  for weight in 400 500 600 700; do
    file="inter-latin-${weight}-normal.woff2"
    target="$FONTS_DIR/$file"

    if [ ! -s "$target" ]; then
      echo "[assets] Downloading Inter ${weight} to $target"
      curl -fL --retry 4 --retry-delay 2 -o "$target" "$BASE_URL/$file"
    fi
  done
fi

exec apache2-foreground
