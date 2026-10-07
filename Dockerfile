FROM php:8.2-apache

# Instala dependências do sistema e extensões PHP necessárias para CI4
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libpng-dev \
    libjpeg-dev \
    libwebp-dev \
    libfreetype6-dev \
    libzip-dev \
    libonig-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) \
        intl \
        mbstring \
        mysqli \
        pdo_mysql \
        gd \
        zip \
        opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Ativa módulo de reescrita do Apache (mod_rewrite)
RUN a2enmod rewrite

# Configura o DocumentRoot do Apache para a pasta /public do CodeIgniter 4
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Ajusta configurações de upload e memória do PHP
RUN echo "upload_max_filesize = 64M" > /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size = 64M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "memory_limit = 256M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "max_execution_time = 120" >> /usr/local/etc/php/conf.d/uploads.ini

# Instala o Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copia código do projeto
WORKDIR /var/www/html
COPY . /var/www/html

# Instala dependências do PHP via Composer
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Ajusta permissões na pasta writable e uploads
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/writable \
    && chmod -R 775 /var/www/html/public/upimg

EXPOSE 80

CMD ["apache2-foreground"]
