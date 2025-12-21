#!/bin/bash

# Application Deployment Script
# Run this on the VPS server after server setup is complete

APP_PATH="/var/www/phongkham"
REPO_URL="https://github.com/phalconsupply/phongkham.git"
BRANCH="main"

cd ${APP_PATH}

echo "Pulling latest code..."
git pull origin ${BRANCH}

echo "Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader

echo "Installing NPM dependencies..."
npm install

echo "Building assets..."
npm run build

echo "Setting permissions..."
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

echo "Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

echo "Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Running migrations..."
php artisan migrate --force

echo "Restarting services..."
sudo systemctl restart php8.2-fpm
sudo systemctl restart nginx

echo "Deployment complete!"
