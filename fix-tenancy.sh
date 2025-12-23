#!/bin/bash

echo "=== Fixing Tenancy Configuration ==="

cd /var/www/phongkham

# Backup config
echo "1. Backing up config..."
cp config/tenancy.php config/tenancy.php.bak

# Update config
echo "2. Updating tenancy.php..."
sed -i "s/'127.0.0.1',/'vnemr.com',/" config/tenancy.php
sed -i "s/'localhost',/'www.vnemr.com',/" config/tenancy.php

# Clear all caches
echo "3. Clearing all caches..."
rm -rf bootstrap/cache/*.php
rm -rf storage/framework/cache/data/*
rm -rf storage/framework/views/*

php artisan optimize:clear
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Restart services
echo "4. Restarting services..."
sudo systemctl restart php8.2-fpm
sudo systemctl restart nginx

echo "=== Done! ==="
echo "Please try accessing https://vnemr.com again"
