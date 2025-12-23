#!/bin/bash

cd /var/www/phongkham

echo "=== Check Laravel Error Log ==="
tail -100 storage/logs/laravel.log | grep -A 5 "TenantCouldNotBeIdentifiedOnDomainException"

echo ""
echo "=== Check Nginx Error Log ==="
sudo tail -50 /var/log/nginx/error.log
