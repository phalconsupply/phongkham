#!/bin/bash

cd /var/www/phongkham

echo "=== Remove all bootstrap cache ==="
rm -rf bootstrap/cache/*
rm -rf storage/framework/cache/*
rm -rf storage/framework/views/*

echo "=== Rebuild autoloader ==="
composer dump-autoload

echo "=== Clear all caches ==="
php artisan clear-compiled
php artisan optimize:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

echo "=== Restart services ==="
sudo systemctl restart php8.2-fpm
sudo systemctl restart nginx

echo "=== Test route ==="
sleep 2
curl -I https://vnemr.com 2>&1 | head -5

echo ""
echo "=== Done! Try browser now ==="
