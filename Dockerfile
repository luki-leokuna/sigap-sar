FROM php:8.2-fpm as build

# Install system dependencies & Node.js
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    sqlite3 \
    libsqlite3-dev \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd pdo_sqlite

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy application files
COPY . /var/www

# Copy environment file
RUN cp .env.example .env

# Install Composer & NPM dependencies
RUN composer install --no-dev --optimize-autoloader
RUN npm install
RUN npm run build

# Generate application key & set up SQLite database
RUN php artisan key:generate
RUN mkdir -p database && touch database/database.sqlite
RUN chown -R www-data:www-data /var/www
RUN chmod -R 775 /var/www/storage /var/www/bootstrap/cache /var/www/database

# Expose port and start server
EXPOSE 8000
CMD sh -c "php artisan migrate --force && php artisan db:seed --force && php artisan serve --host=0.0.0.0 --port=${PORT:-8000}"
