# VPS Deployment Guide - Phòng Khám
## Server: 45.76.154.220 (Ubuntu 22.04)
## Domain: tinhyeu.biz

## Prerequisites
- VPS với Ubuntu 22.04 LTS
- Root hoặc sudo access
- Domain đã trỏ về IP VPS (A record)
- SSH key đã setup

---

## Step 1: Kết Nối VPS

```bash
# Trên máy local, tạo SSH key (nếu chưa có)
ssh-keygen -t rsa -b 4096 -C "your_email@example.com"

# Copy SSH key lên VPS
ssh-copy-id root@45.76.154.220

# Kết nối VPS
ssh root@45.76.154.220
```

---

## Step 2: Setup Server

```bash
# Upload script lên server
scp deploy/vps-setup.sh root@45.76.154.220:/root/

# SSH vào server
ssh root@45.76.154.220

# Run setup script
cd /root
chmod +x vps-setup.sh
./vps-setup.sh
```

**Script sẽ cài đặt:**
- ✅ PHP 8.2 + extensions
- ✅ Composer
- ✅ Node.js 18 + NPM
- ✅ MySQL 8.0
- ✅ Redis
- ✅ Nginx
- ✅ Certbot (SSL)

---

## Step 3: Secure MySQL

```bash
sudo mysql_secure_installation
```

**Lựa chọn:**
- Set root password: YES
- Remove anonymous users: YES
- Disallow root login remotely: YES
- Remove test database: YES
- Reload privilege tables: YES

---

## Step 4: Setup Database

```bash
# Upload script
scp deploy/database-setup.sh root@45.76.154.220:/root/

# SSH vào server
ssh root@45.76.154.220

# Edit passwords trong script
nano database-setup.sh

# Run script
chmod +x database-setup.sh
./database-setup.sh
```

**Lưu lại credentials để dùng cho .env**

---

## Step 5: Clone Repository

```bash
cd /var/www/phongkham

# Clone repository
git clone https://github.com/phalconsupply/phongkham.git .

# Hoặc nếu đã có folder
git init
git remote add origin https://github.com/phalconsupply/phongkham.git
git pull origin main
```

---

## Step 6: Configure Environment

```bash
cd /var/www/phongkham

# Copy environment file
cp .env.example .env

# Edit .env
nano .env
```

**Important .env settings:**

```env
APP_NAME="Phòng Khám"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tinhyeu.biz

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=phongkham
DB_USERNAME=phongkham_user
DB_PASSWORD=your_secure_password

REDIS_CLIENT=predis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

SESSION_DRIVER=redis
CACHE_STORE=redis
QUEUE_CONNECTION=redis

# Tenancy
TENANCY_DATABASE_PREFIX=tenant_
```

```bash
# Generate app key
php artisan key:generate
```

---

## Step 7: Install Dependencies & Build

```bash
cd /var/www/phongkham

# Composer
composer install --no-dev --optimize-autoloader

# NPM
npm install
npm run build

# Set permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

---

## Step 8: Run Migrations & Seeders

```bash
cd /var/www/phongkham

# Run migrations
php artisan migrate --force

# Seed roles and permissions
php artisan db:seed --class=RoleSeeder
php artisan db:seed --class=PermissionSeeder

# Create admin user
php artisan db:seed --class=AdminUserSeeder
```

---

## Step 9: Configure Nginx

```bash
# Upload nginx config
scp deploy/nginx-phongkham.conf root@45.76.154.220:/etc/nginx/sites-available/phongkham

# Create symlink
sudo ln -s /etc/nginx/sites-available/phongkham /etc/nginx/sites-enabled/

# Remove default site
sudo rm /etc/nginx/sites-enabled/default

# Test nginx config
sudo nginx -t

# Restart nginx
sudo systemctl restart nginx
```

---

## Step 10: Setup SSL (Let's Encrypt)

```bash
# Ensure domain points to server IP first!
# Check: dig tinhyeu.biz

# Get SSL certificate
sudo certbot --nginx -d tinhyeu.biz -d www.tinhyeu.biz -d *.tinhyeu.biz

# Follow prompts:
# Email: your_email@example.com
# Agree to terms: Yes
# Redirect HTTP to HTTPS: Yes (recommended)

# Auto-renewal test
sudo certbot renew --dry-run
```

---

## Step 11: Optimize Application

```bash
cd /var/www/phongkham

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimize autoloader
composer dump-autoload --optimize
```

---

## Step 12: Setup Cron Jobs (Queue & Scheduler)

```bash
# Edit crontab
sudo crontab -e

# Add these lines:
* * * * * cd /var/www/phongkham && php artisan schedule:run >> /dev/null 2>&1
* * * * * cd /var/www/phongkham && php artisan queue:work --daemon >> /dev/null 2>&1
```

---

## Step 13: Setup Supervisor (Queue Worker)

```bash
# Install supervisor
sudo apt install supervisor -y

# Create config
sudo nano /etc/supervisor/conf.d/phongkham-worker.conf
```

**Content:**

```ini
[program:phongkham-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/phongkham/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/phongkham/storage/logs/worker.log
stopwaitsecs=3600
```

```bash
# Reload supervisor
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start phongkham-worker:*
```

---

## Step 14: Create First Tenant

```bash
# SSH vào server
ssh root@45.76.154.220
cd /var/www/phongkham

# Option 1: Via artisan tinker
php artisan tinker

# Run in tinker:
$tenant = App\Models\Tenant::create(['id' => 'clinic1']);
$tenant->domains()->create(['domain' => 'clinic1.tinhyeu.biz']);
exit
```

**Then login at:** https://tinhyeu.biz
- Email: admin@phongkham.test
- Password: password

**Create tenant via UI and access at:** https://clinic1.tinhyeu.biz

---

## Automated Deployment Script

```bash
# Upload deploy script
scp deploy/deploy.sh root@45.76.154.220:/var/www/phongkham/

# Run deployment
ssh root@45.76.154.220
cd /var/www/phongkham
chmod +x deploy.sh
./deploy.sh
```

---

## DNS Configuration

**Add these records to your domain registrar:**

```
Type    Name        Value               TTL
A       @           45.76.154.220       3600
A       www         45.76.154.220       3600
A       *           45.76.154.220       3600  (for subdomains)
```

**Verify DNS:**
```bash
dig tinhyeu.biz
dig www.tinhyeu.biz
dig clinic1.tinhyeu.biz
```

---

## Monitoring & Logs

```bash
# Nginx logs
sudo tail -f /var/log/nginx/phongkham-access.log
sudo tail -f /var/log/nginx/phongkham-error.log

# Laravel logs
tail -f /var/www/phongkham/storage/logs/laravel.log

# PHP-FPM logs
sudo tail -f /var/log/php8.2-fpm.log

# MySQL logs
sudo tail -f /var/log/mysql/error.log

# Check services status
sudo systemctl status nginx
sudo systemctl status php8.2-fpm
sudo systemctl status mysql
sudo systemctl status redis-server
```

---

## Security Checklist

- ✅ SSH key authentication (disable password login)
- ✅ Firewall configured (UFW)
- ✅ SSL certificate installed
- ✅ Database with strong password
- ✅ APP_DEBUG=false in production
- ✅ .env permissions (600)
- ✅ Regular updates: `sudo apt update && sudo apt upgrade`
- ✅ Fail2ban (optional): `sudo apt install fail2ban`

---

## Troubleshooting

### 502 Bad Gateway
```bash
sudo systemctl status php8.2-fpm
sudo systemctl restart php8.2-fpm
sudo systemctl restart nginx
```

### Permission Denied
```bash
sudo chown -R www-data:www-data /var/www/phongkham
sudo chmod -R 775 /var/www/phongkham/storage
sudo chmod -R 775 /var/www/phongkham/bootstrap/cache
```

### Database Connection Failed
```bash
# Check MySQL is running
sudo systemctl status mysql

# Test connection
mysql -u phongkham_user -p phongkham
```

### SSL Certificate Issues
```bash
# Renew certificate
sudo certbot renew

# Check certificate
sudo certbot certificates
```

---

## Performance Optimization

```bash
# Enable OPcache
sudo nano /etc/php/8.2/fpm/php.ini

# Add/uncomment:
opcache.enable=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=4000
opcache.revalidate_freq=60

# Restart PHP-FPM
sudo systemctl restart php8.2-fpm
```

---

## Backup Strategy

```bash
# Create backup script
sudo nano /root/backup.sh
```

```bash
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/root/backups"
mkdir -p ${BACKUP_DIR}

# Backup database
mysqldump -u phongkham_user -p phongkham > ${BACKUP_DIR}/db_${DATE}.sql

# Backup files
tar -czf ${BACKUP_DIR}/files_${DATE}.tar.gz /var/www/phongkham

# Keep only last 7 days
find ${BACKUP_DIR} -mtime +7 -delete
```

```bash
chmod +x /root/backup.sh

# Add to crontab (daily at 2 AM)
sudo crontab -e
0 2 * * * /root/backup.sh
```

---

## Quick Commands Reference

```bash
# Deploy new changes
cd /var/www/phongkham && git pull && ./deploy.sh

# Clear all caches
php artisan optimize:clear

# View logs
tail -f storage/logs/laravel.log

# Restart services
sudo systemctl restart nginx php8.2-fpm

# Check disk space
df -h

# Check memory usage
free -m
```

---

**Deployment Complete! 🚀**

Access your application at: **https://tinhyeu.biz**
