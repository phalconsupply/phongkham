#!/bin/bash

echo "=== Debugging Tenancy Issue ==="

cd /var/www/phongkham

# Comment out makeTenancyMiddlewareHighestPriority
echo "1. Patching TenancyServiceProvider..."
sed -i 's/\$this->makeTenancyMiddlewareHighestPriority();/\/\/ \$this->makeTenancyMiddlewareHighestPriority();/' app/Providers/TenancyServiceProvider.php

# Comment out mapRoutes (since it's already in bootstrap/app.php)
sed -i 's/\$this->mapRoutes();/\/\/ \$this->mapRoutes();/' app/Providers/TenancyServiceProvider.php

# Clear caches
echo "2. Clearing caches..."
php artisan optimize:clear
php artisan config:clear

# Restart
echo "3. Restarting PHP-FPM..."
sudo systemctl restart php8.2-fpm

echo "=== Done! Test https://vnemr.com ==="
