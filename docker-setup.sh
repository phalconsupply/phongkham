#!/bin/bash

# Phòng Khám - Quick Setup Script for VPS/Docker

echo "======================================"
echo "Phòng Khám - Setup Script"
echo "======================================"

# Check if Docker is installed
if ! command -v docker &> /dev/null; then
    echo "❌ Docker is not installed. Please install Docker first."
    exit 1
fi

if ! command -v docker-compose &> /dev/null; then
    echo "❌ Docker Compose is not installed. Please install Docker Compose first."
    exit 1
fi

echo "✅ Docker and Docker Compose are installed"

# Copy environment file
if [ ! -f .env ]; then
    echo "📋 Copying .env.docker to .env..."
    cp .env.docker .env
    echo "⚠️  Please edit .env file and configure your settings"
    read -p "Press enter to continue after editing .env..."
fi

# Generate application key
echo "🔑 Generating application key..."
docker-compose run --rm php php artisan key:generate

# Build and start containers
echo "🐳 Building and starting Docker containers..."
docker-compose up -d --build

# Wait for MySQL to be ready
echo "⏳ Waiting for MySQL to be ready..."
sleep 10

# Run migrations
echo "📊 Running migrations..."
docker-compose exec -T php php artisan migrate --force

# Seed roles and admin user
echo "👤 Seeding roles and admin user..."
docker-compose exec -T php php artisan db:seed --class=RoleSeeder --force
docker-compose exec -T php php artisan db:seed --class=AdminUserSeeder --force

# Create storage link
echo "🔗 Creating storage link..."
docker-compose exec -T php php artisan storage:link

# Set permissions
echo "🔒 Setting permissions..."
docker-compose exec -T php chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Build assets
echo "🎨 Building frontend assets..."
docker-compose exec -T node npm install
docker-compose exec -T node npm run build

echo ""
echo "======================================"
echo "✅ Setup completed!"
echo "======================================"
echo ""
echo "Default Admin Login:"
echo "  Email: admin@phongkham.test"
echo "  Password: password"
echo ""
echo "Access your application at: http://localhost"
echo ""
echo "Useful commands:"
echo "  docker-compose ps              # View running containers"
echo "  docker-compose logs -f php     # View PHP logs"
echo "  docker-compose exec php bash   # Access PHP container"
echo "  docker-compose down            # Stop all containers"
echo ""
