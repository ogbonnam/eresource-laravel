#!/bin/sh

set -e

SCAN_DIR="$(php --ini | sed -n 's/^Scan for additional .ini files in: //p')"

if [ -z "$SCAN_DIR" ]; then
    echo "ERROR: Could not determine PHP additional ini scan directory."
    exit 1
fi

export PHP_INI_SCAN_DIR="$SCAN_DIR:/app/php-conf"

echo "PHP additional ini scan directory: $PHP_INI_SCAN_DIR"
echo "Upload limit: $(php -r 'echo ini_get("upload_max_filesize");')"
echo "POST limit: $(php -r 'echo ini_get("post_max_size");')"

php artisan storage:link
exec php artisan serve --host=0.0.0.0 --port=8000