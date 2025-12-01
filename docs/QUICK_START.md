# Quick Start Guide - Phòng Khám

## For Local Development (XAMPP)

### 1. Prerequisites Check
```powershell
# Check PHP version (need 8.2+)
c:\xampp\php\php.exe -v

# Check MySQL is running
# Open XAMPP Control Panel and start MySQL
```

### 2. Install Dependencies
```powershell
cd c:\xampp\htdocs\phongkham

# Install PHP packages
c:\xampp\php\php.exe c:\xampp\php\composer install

# Install Node packages
npm install
```

### 3. Configure Environment
The `.env` file is already configured for XAMPP MySQL.

Verify database exists:
```sql
-- In phpMyAdmin (http://localhost/phpmyadmin)
-- Database "phongkham" should exist
```

### 4. Run Migrations
```powershell
cd c:\xampp\htdocs\phongkham

# Run migrations
c:\xampp\php\php.exe artisan migrate

# Seed roles and admin user
c:\xampp\php\php.exe artisan db:seed --class=RoleSeeder
c:\xampp\php\php.exe artisan db:seed --class=AdminUserSeeder
```

### 5. Build Frontend Assets
```powershell
# For development (with hot reload)
npm run dev

# For production
npm run build
```

### 6. Access Application
```
http://localhost/phongkham/public
```

**Login Credentials:**
- Email: `admin@phongkham.test`
- Password: `password`

### 7. Create Your First Tenant
1. Login with admin account
2. Navigate to "Central" → "Tenants"
3. Click "Create New Tenant"
4. Fill in tenant details:
   - Subdomain: `clinic1`
   - Clinic Name: `My Clinic`
   - Admin details
5. Submit

**Note:** For subdomain access, you need to configure virtual hosts (see README.md)

---

## For VPS/Production (Docker)

### 1. Prerequisites on VPS
```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh

# Install Docker Compose
sudo apt install docker-compose -y
```

### 2. Clone Repository
```bash
git clone <your-repository-url> /var/www/phongkham
cd /var/www/phongkham
```

### 3. Configure Environment
```bash
# Copy Docker environment template
cp .env.docker .env

# Edit configuration
nano .env

# Update these values:
# APP_URL=http://yourdomain.com
# DB_PASSWORD=your_secure_password
# REDIS_PASSWORD=your_redis_password
```

### 4. Run Setup Script
```bash
chmod +x docker-setup.sh
./docker-setup.sh
```

Or manually:
```bash
# Generate app key
docker-compose run --rm php php artisan key:generate

# Build and start containers
docker-compose up -d --build

# Wait for MySQL
sleep 10

# Run migrations
docker-compose exec php php artisan migrate --force
docker-compose exec php php artisan db:seed --class=RoleSeeder --force
docker-compose exec php php artisan db:seed --class=AdminUserSeeder --force

# Create storage link
docker-compose exec php php artisan storage:link

# Build assets
docker-compose exec node npm install
docker-compose exec node npm run build
```

### 5. Configure Domain (Optional)
```bash
# Install Nginx on host
sudo apt install nginx -y

# Create site configuration
sudo nano /etc/nginx/sites-available/phongkham

# Add configuration (see README.md)

# Enable site
sudo ln -s /etc/nginx/sites-available/phongkham /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### 6. Setup SSL (Let's Encrypt)
```bash
sudo apt install certbot python3-certbot-nginx -y
sudo certbot --nginx -d yourdomain.com
```

### 7. Access Application
```
http://yourdomain.com
```

---

## Common Tasks

### Create New Admin User
```php
php artisan tinker
> $user = User::create(['name' => 'John Doe', 'email' => 'john@example.com', 'password' => Hash::make('password')]);
> $user->assignRole('admin');
```

### Clear Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### View Logs
```bash
# XAMPP
tail -f storage/logs/laravel.log

# Docker
docker-compose logs -f php
```

### Run Queue Worker (XAMPP)
```powershell
c:\xampp\php\php.exe artisan queue:work
```

### Access Horizon (Docker)
```
http://yourdomain.com/horizon
```

### Backup Database
```bash
# XAMPP
c:\xampp\mysql\bin\mysqldump -u root phongkham > backup.sql

# Docker
docker-compose exec mysql mysqldump -u root -p phongkham > backup.sql
```

### Restore Database
```bash
# XAMPP
c:\xampp\mysql\bin\mysql -u root phongkham < backup.sql

# Docker
docker-compose exec -T mysql mysql -u root -p phongkham < backup.sql
```

---

## Troubleshooting

### Issue: "Class not found"
```bash
composer dump-autoload
php artisan optimize:clear
```

### Issue: "Permission denied" on storage
```bash
# XAMPP
icacls storage /grant Everyone:(OI)(CI)F /T

# Docker/Linux
sudo chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

### Issue: Assets not loading
```bash
# Rebuild assets
npm run build

# Clear Laravel cache
php artisan view:clear
```

### Issue: Database connection failed
1. Check MySQL is running
2. Verify `.env` credentials
3. Test connection:
```bash
php artisan tinker
> DB::connection()->getPdo();
```

### Issue: Subdomain not working (XAMPP)
1. Edit `C:\Windows\System32\drivers\etc\hosts`:
```
127.0.0.1 phongkham.test
127.0.0.1 clinic1.phongkham.test
```

2. Configure virtual host in Apache (see README.md)

3. Restart Apache

---

## Next Steps

1. **Explore Dashboard**: Login and familiarize yourself with the interface
2. **Create Tenants**: Set up your first clinic tenant
3. **Configure Roles**: Adjust permissions as needed
4. **Customize Theme**: Upload logo and set colors per tenant
5. **Implement Modules**: Start building out Patient, Encounter, etc.
6. **Test Integration**: Set up PrescriptionGateway with your pharmacy system

---

## Getting Help

- Check `README.md` for detailed documentation
- Review `docs/architecture.md` for system architecture
- Check module README files in `app/Modules/*/README.md`
- Review Laravel documentation: https://laravel.com/docs

## Development Workflow

1. Create feature branch
2. Make changes
3. Test locally
4. Run `php artisan test`
5. Build assets: `npm run build`
6. Commit and push
7. Deploy to staging/production

---

**System Ready! 🚀**

Login and start building your clinic management system!
