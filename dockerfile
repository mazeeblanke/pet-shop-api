FROM php:8.2

# Set working directory
WORKDIR /var/www/html

RUN apt-get install && apt-get update -y \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-install mysqli \
    && curl -sS https://getcomposer.org/installer | php -- \
       --install-dir=/usr/local/bin --filename=composer

COPY . .

# Install dependencies with Composer
RUN composer install --no-interaction

CMD [ "php", "artisan", "serve", "--host=0.0.0.0"]
