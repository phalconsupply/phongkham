#!/bin/bash

# Deployment Configuration
SERVER_IP="45.76.154.220"
DOMAIN="tinhyeu.biz"
APP_NAME="phongkham"
APP_PATH="/var/www/phongkham"
PHP_VERSION="8.2"
NODE_VERSION="18"
MYSQL_VERSION="8.0"

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo -e "${GREEN}=================================${NC}"
echo -e "${GREEN}VPS Deployment Setup${NC}"
echo -e "${GREEN}Server: ${SERVER_IP}${NC}"
echo -e "${GREEN}Domain: ${DOMAIN}${NC}"
echo -e "${GREEN}=================================${NC}"

# Check if running as root
if [[ $EUID -eq 0 ]]; then
   echo -e "${RED}Don't run this script as root${NC}" 
   exit 1
fi

echo -e "${YELLOW}Step 1: System Update${NC}"
sudo apt update && sudo apt upgrade -y

echo -e "${YELLOW}Step 2: Install Required Packages${NC}"
sudo apt install -y software-properties-common curl wget git unzip

echo -e "${YELLOW}Step 3: Install PHP ${PHP_VERSION}${NC}"
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y php${PHP_VERSION} \
    php${PHP_VERSION}-fpm \
    php${PHP_VERSION}-cli \
    php${PHP_VERSION}-common \
    php${PHP_VERSION}-mysql \
    php${PHP_VERSION}-xml \
    php${PHP_VERSION}-curl \
    php${PHP_VERSION}-zip \
    php${PHP_VERSION}-mbstring \
    php${PHP_VERSION}-redis \
    php${PHP_VERSION}-gd \
    php${PHP_VERSION}-bcmath \
    php${PHP_VERSION}-intl

echo -e "${YELLOW}Step 4: Install Composer${NC}"
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
composer --version

echo -e "${YELLOW}Step 5: Install Node.js ${NODE_VERSION}${NC}"
curl -fsSL https://deb.nodesource.com/setup_${NODE_VERSION}.x | sudo -E bash -
sudo apt install -y nodejs
node --version
npm --version

echo -e "${YELLOW}Step 6: Install MySQL ${MYSQL_VERSION}${NC}"
sudo apt install -y mysql-server
sudo systemctl start mysql
sudo systemctl enable mysql

echo -e "${YELLOW}Step 7: Install Redis${NC}"
sudo apt install -y redis-server
sudo systemctl start redis-server
sudo systemctl enable redis-server

echo -e "${YELLOW}Step 8: Install Nginx${NC}"
sudo apt install -y nginx
sudo systemctl start nginx
sudo systemctl enable nginx

echo -e "${YELLOW}Step 9: Install Certbot (SSL)${NC}"
sudo apt install -y certbot python3-certbot-nginx

echo -e "${YELLOW}Step 10: Configure PHP-FPM${NC}"
# Update PHP-FPM configuration
sudo sed -i 's/upload_max_filesize = 2M/upload_max_filesize = 20M/' /etc/php/${PHP_VERSION}/fpm/php.ini
sudo sed -i 's/post_max_size = 8M/post_max_size = 20M/' /etc/php/${PHP_VERSION}/fpm/php.ini
sudo sed -i 's/memory_limit = 128M/memory_limit = 256M/' /etc/php/${PHP_VERSION}/fpm/php.ini
sudo sed -i 's/max_execution_time = 30/max_execution_time = 300/' /etc/php/${PHP_VERSION}/fpm/php.ini

sudo systemctl restart php${PHP_VERSION}-fpm

echo -e "${YELLOW}Step 11: Configure Firewall${NC}"
sudo ufw allow 'Nginx Full'
sudo ufw allow OpenSSH
sudo ufw --force enable

echo -e "${YELLOW}Step 12: Create Application Directory${NC}"
sudo mkdir -p ${APP_PATH}
sudo chown -R $USER:www-data ${APP_PATH}
sudo chmod -R 755 ${APP_PATH}

echo -e "${GREEN}=================================${NC}"
echo -e "${GREEN}Server Setup Complete!${NC}"
echo -e "${GREEN}=================================${NC}"
echo ""
echo -e "${YELLOW}Next Steps:${NC}"
echo "1. Configure MySQL root password: sudo mysql_secure_installation"
echo "2. Create database and user"
echo "3. Clone repository to ${APP_PATH}"
echo "4. Configure Nginx virtual host"
echo "5. Setup SSL certificate"
echo ""
echo -e "${YELLOW}Run these commands:${NC}"
echo "sudo mysql_secure_installation"
echo ""
