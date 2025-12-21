#!/bin/bash

# Database Setup Script
# Run this on the VPS server

MYSQL_ROOT_PASSWORD="your_secure_root_password"
DB_NAME="phongkham"
DB_USER="phongkham_user"
DB_PASSWORD="your_secure_db_password"

echo "Setting up MySQL database..."

# Create database
sudo mysql -u root <<MYSQL_SCRIPT
CREATE DATABASE IF NOT EXISTS ${DB_NAME} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASSWORD}';
GRANT ALL PRIVILEGES ON ${DB_NAME}.* TO '${DB_USER}'@'localhost';
FLUSH PRIVILEGES;
MYSQL_SCRIPT

echo "Database created successfully!"
echo ""
echo "Database Credentials:"
echo "  Database: ${DB_NAME}"
echo "  User: ${DB_USER}"
echo "  Password: ${DB_PASSWORD}"
echo ""
echo "Add these to your .env file:"
echo "DB_CONNECTION=mysql"
echo "DB_HOST=127.0.0.1"
echo "DB_PORT=3306"
echo "DB_DATABASE=${DB_NAME}"
echo "DB_USERNAME=${DB_USER}"
echo "DB_PASSWORD=${DB_PASSWORD}"
