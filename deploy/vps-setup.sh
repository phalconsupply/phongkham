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
if [[ $EUID -ne 0 ]]; then
   echo -e "${RED}This script must be run as root${NC}" 
   exit 1
fi

echo -e "${YELLOW}Step 0: Create Deploy User${NC}"
# Create a deploy user if not exists
if ! id "deploy" &>/dev/null; then
    useradd -m -s /bin/bash deploy
    usermod -aG sudo deploy
    echo "User 'deploy' created"
else
    echo "User 'deploy' already exists"
fi

echo -e "${YELLOW}Step 1: System Update${NC}"
sudo apt update && sudo apt upgrade -y

echo -e "${YELLOW}Step 2: Install Required Packages${NC}"
apt install -y software-properties-common curl wget git unzip

echo -e "${YELLOW}Step 3: Install PHP ${PHP_VERSION}${NC}"
add-apt-repository ppa:ondrej/php -y
apt update
apt install -y php${PHP_VERSION} \
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
mv composer.phar /usr/local/bin/composer
composer --version

echo -e "${YELLOW}Step 5: Install Node.js ${NODE_VERSION}${NC}"
curl -fsSL https://deb.nodesource.com/setup_${NODE_VERSION}.x | bash -
apt install -y nodejs
node --version
npm --version

echo -e "${YELLOW}Step 6: Install MySQL ${MYSQL_VERSION}${NC}"
apt install -y mysql-server
systemctl start mysql
systemctl enable mysql

echo -e "${YELLOW}Step 7: Install Redis${NC}"
apt install -y redis-server
systemctl start redis-server
systemctl enable redis-server

echo -e "${YELLOW}Step 8: Install Nginx${NC}"
apt install -y nginx
systemctl start nginx
systemctl enable nginx

echo -e "${YELLOW}Step 9: Install Certbot (SSL)${NC}"
apt install -y certbot python3-certbot-nginx

echo -e "${YELLOW}Step 10: Configure PHP-FPM${NC}"
# Update PHP-FPM configuration
sed -i 's/upload_max_filesize = 2M/upload_max_filesize = 20M/' /etc/php/${PHP_VERSION}/fpm/php.ini
sed -i 's/post_max_size = 8M/post_max_size = 20M/' /etc/php/${PHP_VERSION}/fpm/php.ini
sed -i 's/memory_limit = 128M/memory_limit = 256M/' /etc/php/${PHP_VERSION}/fpm/php.ini
sed -i 's/max_execution_time = 30/max_execution_time = 300/' /etc/php/${PHP_VERSION}/fpm/php.ini

systemctl restart php${PHP_VERSION}-fpm

echo -e "${YELLOW}Step 11: Configure Firewall${NC}"
ufw allow 'Nginx Full'
ufw allow OpenSSH
ufw --force enable

echo -e "${YELLOW}Step 12: Create Application Directory${NC}"
mkdir -p ${APP_PATH}
chown -R deploy:www-data ${APP_PATH}
chmod -R 755 ${APP_PATH}

echo -e "${GREEN}=================================${NC}"
echo -e "${GREEN}Server Setup Complete!${NC}"
echo -e "${GREEN}=================================${NC}"
echo ""
echo -e "${YELLOW}Deploy User Created:${NC}"
echo "Username: deploy"
echo "Password: (set with: passwd deploy)"
echo ""
echo -e "${YELLOW}Next Steps:${NC}"
echo "1. Set password for deploy user: passwd deploy"
echo "2. Configure MySQL root password: mysql_secure_installation"
echo "3. Switch to deploy user: su - deploy"
echo "4. Clone repository to ${APP_PATH}"
echo "5. Configure Nginx virtual host"
echo "6. Setup SSL certificate"
echo ""
echo -e "${YELLOW}Quick Commands:${NC}"
echo "passwd deploy"
echo "mysql_secure_installation"
echo ""
