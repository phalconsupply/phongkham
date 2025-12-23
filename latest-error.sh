#!/bin/bash

cd /var/www/phongkham

echo "=== Latest Laravel Error ==="
tail -200 storage/logs/laravel.log | grep -A 10 "production.ERROR" | tail -50

echo ""
echo "=== Try to identify exact error ==="
php artisan route:list --path=/ 2>&1 | head -5
