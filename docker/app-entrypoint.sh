#!/bin/sh
set -eu

mkdir -p /var/www/html/storage/app/public
cp /opt/sendiko-branding/favicon.jpg /opt/sendiko-branding/hero_image.jpg /var/www/html/storage/app/public/

exec "$@"
