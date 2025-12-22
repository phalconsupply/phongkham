# Tổng Kết Khắc Phục Lỗi Multi-tenant Laravel

## Vấn đề gặp phải
Lỗi "Tenant could not be identified on domain" - Domain chính (central domain) bị nhận diện nhầm là tenant domain.

## Nguyên nhân
1. Domain chính chưa được khai báo trong `central_domains`
2. Thứ tự đăng ký routes không đúng trong `bootstrap/app.php`
3. Auth routes bị load trùng lặp

## Các bước khắc phục

### 1. Cấu hình Central Domains
**File:** `config/tenancy.php`

```php
'central_domains' => [
    '127.0.0.1',
    'localhost',
    'phongkham.test',  // Thêm domain chính của bạn
    // Hoặc domain production: 'yourdomain.com'
],
```

### 2. Sửa thứ tự đăng ký Routes
**File:** `bootstrap/app.php`

**QUAN TRỌNG:** Tenant routes PHẢI được đăng ký TRƯỚC central routes

```php
->withRouting(
    commands: __DIR__.'/../routes/console.php',
    health: '/up',
    then: function () {
        // TENANT ROUTES - Kiểm tra TRƯỚC
        Route::middleware([
            'web',
            \Stancl\Tenancy\Middleware\InitializeTenancyByDomain::class,
            \Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains::class,
        ])->group(function() {
            require base_path('routes/tenant.php');
            require base_path('routes/auth.php');
        });
        
        // CENTRAL ROUTES - Fallback SAU
        Route::middleware('web')
            ->group(base_path('routes/web.php'));
    }
)
```

**Giải thích:**
- Middleware `PreventAccessFromCentralDomains` sẽ kiểm tra domain
- Nếu là central domain → bỏ qua tenant routes → rơi xuống central routes
- Nếu là tenant domain → xử lý bởi tenant routes

### 3. Tách Auth Routes cho Tenant
**File:** `routes/tenant.php`

Xóa dòng:
```php
require __DIR__.'/auth.php';
```

Auth routes đã được load trong `bootstrap/app.php` cùng với tenant routes.

### 4. Cập nhật Database Seeder
**File:** `database/seeders/DatabaseSeeder.php`

```php
public function run(): void
{
    $this->call([
        RoleSeeder::class,
        PermissionSeeder::class,
        AdminUserSeeder::class,
    ]);
}
```

### 5. Clear Cache
Sau mỗi thay đổi config hoặc routes:

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

Hoặc clear tất cả:
```bash
php artisan optimize:clear
```

### 6. Xóa Bootstrap Cache
Nếu vẫn còn lỗi:
```powershell
Remove-Item "bootstrap\cache\*.php" -Force
```

## Triển khai trên VPS

### Bước 1: Clone và Setup
```bash
cd /var/www
git clone <repository-url> phongkham
cd phongkham
```

### Bước 2: Cài đặt Dependencies
```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
```

### Bước 3: Cấu hình Environment
```bash
cp .env.example .env
nano .env
```

Cập nhật:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=http://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=phongkham
DB_USERNAME=your_user
DB_PASSWORD=your_password
```

### Bước 4: Generate Key và Migrate
```bash
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
```

### Bước 5: Set Permissions
```bash
chown -R www-data:www-data /var/www/phongkham
chmod -R 755 /var/www/phongkham
chmod -R 775 /var/www/phongkham/storage
chmod -R 775 /var/www/phongkham/bootstrap/cache
```

### Bước 6: Cấu hình Nginx/Apache
**Nginx:**
```nginx
server {
    listen 80;
    server_name yourdomain.com *.yourdomain.com;
    root /var/www/phongkham/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

**Apache:**
```apache
<VirtualHost *:80>
    ServerName yourdomain.com
    ServerAlias *.yourdomain.com
    DocumentRoot /var/www/phongkham/public

    <Directory /var/www/phongkham/public>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/phongkham-error.log
    CustomLog ${APACHE_LOG_DIR}/phongkham-access.log combined
</VirtualHost>
```

### Bước 7: Cập nhật config/tenancy.php
```php
'central_domains' => [
    'yourdomain.com',
    'www.yourdomain.com',
],
```

### Bước 8: Optimize cho Production
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Bước 9: Restart Web Server
```bash
# Nginx
sudo systemctl restart nginx

# Apache
sudo systemctl restart apache2
```

## Debug Trên VPS

Nếu gặp lỗi, upload và chạy các file debug:

### 1. check-users.php
```bash
php check-users.php
```

### 2. debug-routing.php
```bash
php debug-routing.php
```

### 3. Xem Laravel Log
```bash
tail -f storage/logs/laravel.log
```

### 4. Xem Web Server Log
```bash
# Nginx
tail -f /var/log/nginx/error.log

# Apache
tail -f /var/log/apache2/error.log
```

## Thông tin đăng nhập mặc định

**Email:** admin@phongkham.test  
**Password:** password

## Checklist triển khai VPS

- [ ] Clone repository
- [ ] Cài đặt composer dependencies
- [ ] Cài đặt npm dependencies và build
- [ ] Copy .env và cấu hình database
- [ ] Generate APP_KEY
- [ ] Cập nhật central_domains với domain production
- [ ] Chạy migrations
- [ ] Chạy seeders
- [ ] Set permissions cho storage và bootstrap/cache
- [ ] Cấu hình virtual host (Nginx/Apache)
- [ ] Clear và cache config
- [ ] Test truy cập domain
- [ ] Tạo tenant đầu tiên
- [ ] Test subdomain tenant

## Lưu ý quan trọng

1. **Thứ tự routes rất quan trọng** - Tenant routes phải đăng ký trước
2. **Central domains phải match chính xác** - Bao gồm www và non-www nếu cần
3. **Clear cache sau mỗi thay đổi config**
4. **Kiểm tra permissions của storage và bootstrap/cache**
5. **Subdomain wildcard** - Đảm bảo DNS có record `*.yourdomain.com`
