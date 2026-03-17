# ---- Build stage: install dependencies and compile assets ----
FROM php:8.4-cli-alpine AS build

WORKDIR /var/www

# Install system dependencies
RUN apk add --no-cache \
    git curl nodejs npm \
    libpng-dev oniguruma-dev libxml2-dev postgresql-dev \
    icu-dev libzip-dev xz linux-headers \
    && pecl install redis && docker-php-ext-enable redis \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-configure intl \
    && docker-php-ext-install pdo pdo_pgsql pgsql mbstring exif pcntl bcmath gd intl zip opcache \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy package files first for better layer caching
COPY package*.json ./
RUN npm ci --no-audit

COPY composer.json composer.lock ./
RUN composer install --optimize-autoloader --no-dev --no-scripts --no-interaction

# Copy application files
COPY . /var/www

# Run post-install scripts, build assets, download RoadRunner binary
RUN git config --global --add safe.directory /var/www \
    && composer run post-autoload-dump \
    && npm run build \
    && php artisan storage:link || true \
    && vendor/bin/rr get-binary --location /usr/local/bin

# ---- Production stage: lean runtime image ----
FROM php:8.4-cli-alpine

WORKDIR /var/www

# Install only runtime dependencies (no build tools)
RUN apk add --no-cache \
    curl libpng oniguruma libxml2 libpq \
    icu-libs libzip xz \
    && pecl install redis && docker-php-ext-enable redis \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-configure intl \
    && docker-php-ext-install pdo pdo_pgsql pgsql mbstring exif pcntl bcmath gd intl zip opcache \
    && rm -rf /tmp/* /var/cache/apk/*

# Configure OPcache for production
RUN echo "[opcache]" > /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.memory_consumption=256" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.interned_strings_buffer=64" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.max_accelerated_files=32531" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.validate_timestamps=0" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.save_comments=1" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.jit=1255" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.jit_buffer_size=128M" >> /usr/local/etc/php/conf.d/opcache.ini

# Configure PHP settings
RUN echo "upload_max_filesize = 128M" > /usr/local/etc/php/conf.d/php.ini \
    && echo "post_max_size = 128M" >> /usr/local/etc/php/conf.d/php.ini \
    && echo "max_execution_time = 300" >> /usr/local/etc/php/conf.d/php.ini \
    && echo "memory_limit = 256M" >> /usr/local/etc/php/conf.d/php.ini \
    && echo "realpath_cache_size = 4096K" >> /usr/local/etc/php/conf.d/php.ini \
    && echo "realpath_cache_ttl = 600" >> /usr/local/etc/php/conf.d/php.ini

# Copy RoadRunner binary from build stage
COPY --from=build /usr/local/bin/rr /usr/local/bin/rr

# Copy application from build stage
COPY --from=build --chown=www-data:www-data /var/www /var/www

# Set permissions
RUN chmod -R 755 /var/www/storage /var/www/bootstrap/cache /var/www/public

# Expose port 8000
EXPOSE 8000

# Health check
HEALTHCHECK --interval=30s --timeout=10s --start-period=30s --retries=3 \
    CMD curl -f http://localhost:8000/up || exit 1

# Fix bind mount permissions and start Octane with RoadRunner
CMD ["sh", "-c", "chown -R www-data:www-data /var/www/storage && php artisan optimize && php artisan octane:start --server=roadrunner --host=0.0.0.0 --port=8000 --workers=auto --max-requests=500"]
