# HƯỚNG DẪN FIX NHANH

## Cách 1: Dùng PHP Script (Khuyên dùng)

1. Upload file `fix-tenant-bug.php` lên VPS
2. SSH vào VPS và chạy:
```bash
php fix-tenant-bug.php
```

## Cách 2: Chạy commands trực tiếp

SSH vào VPS và chạy từng lệnh:

```bash
# Tìm project
cd $(find /var/www -name "artisan" | head -1 | xargs dirname)

# Backup
cp config/tenancy.php config/tenancy.php.backup

# Xem config hiện tại
grep -A 10 "'central_domains'" config/tenancy.php

# Edit config thủ công
nano config/tenancy.php
# Thêm 'vnemr.com' và 'www.vnemr.com' vào mảng central_domains

# Clear cache
php artisan optimize:clear

# Cache lại
php artisan config:cache
php artisan route:cache

# Restart
systemctl restart nginx

# Test
curl -I http://vnemr.com
```

## Cách 3: Copy-Paste Script

Copy toàn bộ nội dung file `quick-fix.sh`, paste vào SSH terminal và Enter.

## Kiểm tra lỗi

Nếu vẫn lỗi, chạy:
```bash
# Xem Laravel log
tail -50 storage/logs/laravel.log

# Xem web server log  
tail -50 /var/log/nginx/error.log
```

## File cần sửa

### config/tenancy.php
```php
'central_domains' => [
    '127.0.0.1',
    'localhost',
    'vnemr.com',        // <- Thêm dòng này
    'www.vnemr.com',    // <- Thêm dòng này
],
```

### bootstrap/app.php (nếu cần)
Tenant routes phải đăng ký TRƯỚC central routes.
