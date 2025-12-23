#!/bin/bash

echo "=== Debug Routes and Middleware ==="

cd /var/www/phongkham

echo "1. Check central_domains in config:"
php artisan tinker --execute="dd(config('tenancy.central_domains'));"

echo ""
echo "2. Check middleware on login route:"
php artisan route:list --path=login --columns=uri,method,middleware

echo ""
echo "3. Check middleware on root route:"
php artisan route:list --path=/ --columns=uri,method,middleware

echo ""
echo "4. Check if tenancy is initialized:"
php artisan tinker --execute="dd(tenancy()->initialized);"

echo "=== Done ==="
