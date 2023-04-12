FROM ubuntu:23.04

# Copy project files
COPY . /var/www/html

# Set working directory
WORKDIR /var/www/html

# Install Dependencies
RUN apt-get update \
    && apt-get install -y gnupg gosu curl ca-certificates zip unzip git \
    && curl -sS 'https://keyserver.ubuntu.com/pks/lookup?op=get&search=0x14aa40ec0831756756d7f66c4f4ea0aae5267a6c' | gpg --dearmor | tee /etc/apt/keyrings/ppa_ondrej_php.gpg > /dev/null \
    && echo "deb [signed-by=/etc/apt/keyrings/ppa_ondrej_php.gpg] https://ppa.launchpadcontent.net/ondrej/php/ubuntu jammy main" > /etc/apt/sources.list.d/ppa_ondrej_php.list

RUN apt-get update \
    && apt-get install -y php8.2-cli php8.2-dev php8.2-curl php8.2-mysql \
       php8.2-mbstring php8.2-xml php8.2-zip php8.2-bcmath \
       php8.2-readline php8.2-redis \
       php8.2-memcached php8.2-xdebug

# Install Composer
RUN curl -sLS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer

# Install dependencies with Composer
RUN composer install --no-interaction

# Install MySQL
RUN apt-get install -y mysql-client

# Cleanup
RUN apt-get -y autoremove \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Set permissions for Laravel
RUN chown -R www-data:www-data storage
RUN chmod -R 775 storage
RUN chown -R www-data:www-data bootstrap/cache
RUN chmod -R 775 bootstrap/cache

# Expose port 8000
EXPOSE 8000

# Start laravel server
CMD ["php", "artisan", "serve", "--host=0.0.0.0"]
