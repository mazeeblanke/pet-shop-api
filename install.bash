#!/bin/bash

# Copy env files
cp .env.example .env
cp .env.testing.example .env.testing

# copy pem file
cp public/private-key.pem storage/app

# Load environment variables
source .env

# Install dependencies
composer install

# Start docker containers
docker-compose up -d

# Wait for MySQL server to start up
echo 'Waiting for DEV MySQL server to start up...'
until docker-compose exec pet-shop-db mysqladmin ping --silent; do
    sleep 10
done
echo 'MySQL DEV server is now available!'

# Create MySQL DEV database securely
docker-compose exec pet-shop-db bash -c '\
        source /data/.env && \
        export MYSQL_PWD="${DB_PASSWORD}" && \
        mysql -u root -e "CREATE DATABASE IF NOT EXISTS ${DB_DATABASE};"'


# Create MySQL Test database securely
docker-compose exec pet-shop-db bash -c '\
        source /data/.env.testing && \
        export MYSQL_PWD="${DB_PASSWORD}" && \
        echo ${DB_DATABASE} && \
        mysql -u root -e "CREATE DATABASE IF NOT EXISTS ${DB_DATABASE};"'

# Generate app key
docker-compose exec pet-shop-api bash -c 'php artisan key:generate'

# Run database migrations and seeds
docker-compose exec pet-shop-api bash -c 'php artisan migrate --seed'

# Run Pet Shop tests
echo 'Running PET SHOP Tests!'

# Run test in container
docker-compose exec pet-shop-api bash -c 'php artisan test'

# Run in container
docker-compose exec pet-shop-api bash -c 'cd packages/mazi/currency-converter && \
                composer install && \
                ./vendor/bin/phpunit'

