# Stage 1: Build frontend assets
FROM node:20-alpine AS frontend
WORKDIR /app
COPY package.json ./
COPY package-lock.json* ./
RUN npm install
COPY vite.config.js ./
COPY resources/ ./resources/
RUN npm run build

# Stage 2: Install PHP dependencies
FROM composer:2 AS composer
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --ignore-platform-reqs
COPY . .
RUN rm -f bootstrap/cache/*.php \
    && composer dump-autoload --optimize --no-dev --no-scripts

# Stage 3: Production image
FROM php:8.4-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    nginx \
    supervisor \
    libpng \
    libjpeg-turbo \
    freetype \
    libwebp \
    oniguruma \
    curl \
    && apk add --no-cache --virtual .build-deps \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libwebp-dev \
    oniguruma-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install pdo pdo_mysql mbstring gd opcache bcmath \
    && apk del .build-deps

# Configure PHP for production
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY docker/php.ini /usr/local/etc/php/conf.d/custom.ini

# Configure Nginx
COPY docker/nginx.conf /etc/nginx/http.d/default.conf
RUN rm -f /etc/nginx/http.d/default.conf.bak

# Create all required directories
RUN mkdir -p /var/log/supervisor \
    && mkdir -p /run/nginx

# Configure Supervisor
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copy entrypoint
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Set working directory
WORKDIR /var/www/html

# Copy application code from composer stage
COPY --from=composer /app /var/www/html

# Copy built frontend assets
COPY --from=frontend /app/public/build /var/www/html/public/build

# Remove unnecessary files from image
RUN rm -rf tests .git .kiro node_modules .env .env.* docker-compose* Dockerfile .dockerignore

# Create Laravel directories
RUN mkdir -p storage/framework/sessions \
    && mkdir -p storage/framework/views \
    && mkdir -p storage/framework/cache/data \
    && mkdir -p storage/app/public/photos \
    && mkdir -p storage/app/public/videos \
    && mkdir -p storage/logs \
    && mkdir -p bootstrap/cache

# Create storage symlink
RUN ln -sf /var/www/html/storage/app/public /var/www/html/public/storage

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# Expose port
EXPOSE 80

# Health check
HEALTHCHECK --interval=30s --timeout=5s --start-period=60s --retries=3 \
    CMD curl -fsS -o /dev/null http://localhost/login || exit 1

# Start via entrypoint
CMD ["/usr/local/bin/entrypoint.sh"]
