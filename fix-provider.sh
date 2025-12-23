#!/bin/bash

echo "=== Fix TenancyServiceProvider ==="

cd /var/www/phongkham

# Backup
cp app/Providers/TenancyServiceProvider.php app/Providers/TenancyServiceProvider.php.bak

# Comment out both problematic methods in boot()
cat > /tmp/fix_provider.php << 'EOF'
<?php
$file = 'app/Providers/TenancyServiceProvider.php';
$content = file_get_contents($file);

// Replace the boot method
$content = preg_replace(
    '/public function boot\(\)\s*\{[^}]*\}/',
    "public function boot()\n    {\n        \$this->bootEvents();\n        // Tenant routes are handled in bootstrap/app.php\n        // \$this->mapRoutes();\n        // \$this->makeTenancyMiddlewareHighestPriority();\n    }",
    $content
);

file_put_contents($file, $content);
echo "TenancyServiceProvider patched!\n";
EOF

php /tmp/fix_provider.php

# Clear all caches
echo "Clearing caches..."
php artisan optimize:clear
php artisan config:clear
php artisan route:clear

# Restart
echo "Restarting PHP-FPM..."
sudo systemctl restart php8.2-fpm

echo "=== Done! Try https://vnemr.com ==="
