#!/bin/bash

cd /var/www/phongkham

echo "=== Check TenancyServiceProvider boot method ==="
grep -A 10 "public function boot()" app/Providers/TenancyServiceProvider.php

echo ""
echo "=== Try accessing with curl ==="
curl -I https://vnemr.com 2>&1 | head -20
