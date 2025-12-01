# Phòng Khám - Multi-Tenant Clinic Management System

## Overview

A modern, multi-tenant clinic management system built with Laravel 11, Vue 3, and TailwindCSS. Designed for touch-optimized interfaces with per-tenant theming and database isolation.

## Tech Stack

- **Backend**: Laravel 11.x
- **Frontend**: Vue 3 + Inertia.js
- **Styling**: TailwindCSS (with dark mode support)
- **Multi-tenancy**: stancl/tenancy (database-per-tenant)
- **Authentication**: Laravel Breeze (Fortify)
- **Permissions**: Spatie Laravel Permission
- **Database**: MySQL 8.0
- **Cache/Queue**: Redis + Laravel Horizon
- **Deployment**: Docker (Nginx + PHP-FPM + MySQL + Redis)

## Features

### Multi-Tenancy
- Database-per-tenant isolation using stancl/tenancy
- Subdomain-based tenant identification
- Per-tenant theming (logo, colors, clinic name)
- Central admin panel for tenant management

### Authentication & Authorization
- Laravel Breeze authentication
- Role-based access control (RBAC)
- Pre-configured roles:
  - **Admin**: Full system access
  - **Doctor**: Patient management, encounters, prescriptions
  - **Nurse**: Patient care, encounters, appointments
  - **Receptionist**: Appointments, patient registration

### Module Structure
Modular architecture for scalability:
- `app/Modules/Patient/` - Patient management
- `app/Modules/Encounter/` - Clinical encounters
- `app/Modules/Prescription/` - Prescription handling
- `app/Modules/Appointment/` - Appointment scheduling
- `app/Modules/Shared/` - Shared utilities

### UI/UX
- Touch-optimized interface (large buttons, spacious layout)
- Dark mode support
- Responsive design (mobile, tablet, desktop)
- Modern TailwindCSS styling

### Integration Ready
- PrescriptionGateway adapter for external pharmacy systems
- Interface-based design for easy integration
- Dummy implementation included

## Installation

### Local Development (XAMPP)

#### Prerequisites
- PHP 8.2+
- MySQL 8.0
- Composer
- Node.js 18+ & NPM
- XAMPP installed

#### Steps

1. **Clone/Navigate to project**
```bash
cd c:\xampp\htdocs\phongkham
```

2. **Install PHP dependencies**
```bash
c:\xampp\php\php.exe c:\xampp\php\composer install
```

3. **Install Node dependencies**
```bash
npm install
```

4. **Configure environment**
```bash
cp .env.example .env
```

Edit `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=phongkham
DB_USERNAME=root
DB_PASSWORD=

REDIS_CLIENT=predis
REDIS_HOST=127.0.0.1
```

5. **Generate application key**
```bash
c:\xampp\php\php.exe artisan key:generate
```

6. **Run migrations**
```bash
c:\xampp\php\php.exe artisan migrate --seed
```

7. **Create storage link**
```bash
c:\xampp\php\php.exe artisan storage:link
```

8. **Build frontend assets**
```bash
npm run build
# or for development with hot reload
npm run dev
```

9. **Start XAMPP**
- Start Apache
- Start MySQL

10. **Access application**
- Main site: `http://localhost/phongkham/public`
- Configure virtual host for cleaner URLs (see below)

### Docker Deployment (Production/VPS)

#### Prerequisites
- Docker Engine 20.10+
- Docker Compose 2.0+

#### Steps

1. **Clone repository on VPS**
```bash
git clone <repository-url> /var/www/phongkham
cd /var/www/phongkham
```

2. **Copy environment file**
```bash
cp .env.example .env
```

3. **Update .env for Docker**
```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=phongkham
DB_USERNAME=phongkham
DB_PASSWORD=secret

REDIS_CLIENT=predis
REDIS_HOST=redis

QUEUE_CONNECTION=redis
```

4. **Build and start containers**
```bash
docker-compose up -d --build
```

5. **Run migrations inside container**
```bash
docker-compose exec php php artisan migrate --seed
```

6. **Generate key**
```bash
docker-compose exec php php artisan key:generate
```

7. **Build assets**
```bash
docker-compose exec node npm install
docker-compose exec node npm run build
```

8. **Access application**
- Main site: `http://your-vps-ip`
- Configure domain/DNS for production

## Usage

### Creating Tenants

1. **Login as admin** (default after seeding)
2. **Navigate to** `/central/tenants`
3. **Click** "Create New Tenant"
4. **Fill in**:
   - Subdomain (e.g., `clinic1`)
   - Clinic name
   - Primary color
   - Logo (optional)
   - Admin user details
5. **Submit** - Tenant will be created with isolated database

### Accessing Tenants

Each tenant is accessible via subdomain:
- `http://clinic1.localhost/` (local)
- `http://clinic1.yourdomain.com/` (production)

### Roles & Permissions

Seed includes default roles. Assign roles to users:

```php
$user->assignRole('doctor');
```

Check permissions in views:
```vue
<template v-if="$page.props.auth.user.roles.includes('admin')">
    <!-- Admin only content -->
</template>
```

## Configuration

### Tenancy Configuration

Edit `config/tenancy.php`:
```php
'tenant_model' => \App\Models\Tenant::class,
'central_domains' => [
    'localhost',
    'yourdomain.com',
],
```

### Theme Configuration

Themes stored in `tenant_themes` table:
- `clinic_name`: Display name
- `logo_path`: Logo file path
- `primary_color`: Hex color code
- `secondary_color`: Hex color code

### Virtual Host (XAMPP)

Edit `C:\xampp\apache\conf\extra\httpd-vhosts.conf`:

```apache
<VirtualHost *:80>
    DocumentRoot "C:/xampp/htdocs/phongkham/public"
    ServerName phongkham.test
    ServerAlias *.phongkham.test
    
    <Directory "C:/xampp/htdocs/phongkham/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Add to `C:\Windows\System32\drivers\etc\hosts`:
```
127.0.0.1 phongkham.test
127.0.0.1 clinic1.phongkham.test
```

## Development

### Running Queue Worker
```bash
php artisan queue:work
```

### Running Horizon (Docker only)
```bash
docker-compose exec horizon php artisan horizon
```

### Building Assets
```bash
npm run dev    # Development with hot reload
npm run build  # Production build
```

### Running Tests
```bash
php artisan test
```

## Project Structure

```
phongkham/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       └── CentralAdmin/
│   │           └── TenantController.php
│   ├── Models/
│   │   ├── Tenant.php
│   │   ├── TenantTheme.php
│   │   └── User.php
│   ├── Modules/
│   │   ├── Patient/
│   │   ├── Encounter/
│   │   ├── Prescription/
│   │   ├── Appointment/
│   │   └── Shared/
│   └── Services/
│       └── PrescriptionGateway/
│           ├── PrescriptionGatewayInterface.php
│           └── DummyPrescriptionGateway.php
├── database/
│   ├── migrations/
│   │   ├── tenant/          # Tenant-specific migrations
│   │   └── *.php            # Central migrations
│   └── seeders/
│       └── RoleSeeder.php
├── docker/
│   ├── nginx/
│   ├── php/
│   └── mysql/
├── resources/
│   ├── js/
│   │   ├── Pages/
│   │   │   ├── CentralAdmin/
│   │   │   │   └── Tenants/
│   │   │   │       ├── Index.vue
│   │   │   │       └── Create.vue
│   │   │   └── Dashboard.vue
│   │   └── Components/
│   └── views/
├── routes/
│   ├── web.php
│   └── tenant.php           # Tenant-specific routes
└── docker-compose.yml
```

## Deployment to VPS (Ubuntu)

### Prerequisites on VPS
```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh

# Install Docker Compose
sudo apt install docker-compose -y

# Add user to docker group
sudo usermod -aG docker $USER
```

### Deploy
```bash
# Clone repository
git clone <repo> /var/www/phongkham
cd /var/www/phongkham

# Copy and configure .env
cp .env.example .env
nano .env

# Start services
docker-compose up -d --build

# Run migrations
docker-compose exec php php artisan migrate --seed

# Set permissions
sudo chown -R www-data:www-data storage bootstrap/cache
```

### Configure Nginx Reverse Proxy (Optional)
For SSL and domain routing, install Nginx on host:

```bash
sudo apt install nginx certbot python3-certbot-nginx -y
```

Create `/etc/nginx/sites-available/phongkham`:
```nginx
server {
    listen 80;
    server_name yourdomain.com *.yourdomain.com;

    location / {
        proxy_pass http://localhost:80;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

Enable site and SSL:
```bash
sudo ln -s /etc/nginx/sites-available/phongkham /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
sudo certbot --nginx -d yourdomain.com -d *.yourdomain.com
```

## Troubleshooting

### Storage permissions
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Clear cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

### Regenerate autoload
```bash
composer dump-autoload
```

## Roadmap

- [ ] Implement Patient module
- [ ] Implement Encounter module
- [ ] Implement Prescription module with external gateway
- [ ] Implement Appointment module
- [ ] Add reporting dashboard
- [ ] Add multi-language support
- [ ] Add audit logs
- [ ] Add backup automation

## License

Proprietary - All rights reserved

## Support

For support, contact: [your-email@example.com]
