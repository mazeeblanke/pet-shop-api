#!/bin/bash

# Copy env files
cp .env.example .env
cp .env.testing.example .env.testing

# Load environment variables
source .env

# Install dependencies
composer install

# Start docker containers
docker-compose up -d

# Wait for MySQL server to start up
echo "Waiting for DEV MySQL server to start up..."
until docker-compose exec pet-shop-db mysqladmin ping --silent; do
    sleep 10
done
echo "MySQL DEV server is now available!"

echo "Waiting for TEST MySQL server to start up..."
until docker-compose exec pet-shop-db-test mysqladmin ping --silent; do
    sleep 10
done
echo "MySQL TEST server is now available!"

# Securely set MYSQL Password
export MYSQL_PWD="${DB_PASSWORD}"

# Create MySQL DEV database
docker-compose exec pet-shop-db mysql -uroot -p"${DB_PASSWORD}" -e "CREATE DATABASE IF NOT EXISTS pet_shop;"

# Create MySQL TEST database
docker-compose exec pet-shop-db-test mysql -uroot -p"${DB_PASSWORD}" -e "CREATE DATABASE IF NOT EXISTS pet_shop_test_12345"

# Generate app key
docker-compose exec pet-shop-api bash -c "php artisan key:generate"

# Run database migrations and seeds
docker-compose exec pet-shop-api bash -c "php artisan migrate --seed"


# Run tests
php artisan test
