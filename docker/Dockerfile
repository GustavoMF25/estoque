FROM php:8.3-apache

# Instalar dependências de sistema e extensões PHP para Laravel
RUN apt-get update && apt-get install -y \
    zip unzip git curl gnupg ca-certificates lsb-release libpng-dev libjpeg-dev libonig-dev libxml2-dev libzip-dev \
    default-mysql-client \
    && docker-php-ext-install pdo pdo_mysql mysqli zip gd mbstring

# Instalar Node.js (LTS) + npm via NodeSource
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# Verificação (opcional)
RUN node -v && npm -v

# Ativa o mod_rewrite para o Laravel funcionar
RUN a2enmod rewrite

# Copiar configs do Apache e PHP personalizadas
COPY ./docker/apache.conf /etc/apache2/sites-available/000-default.conf
COPY ./docker/php.ini /usr/local/etc/php/php.ini

# Instala Composer diretamente da imagem oficial do composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Define o diretório do projeto
WORKDIR /var/www/html
COPY . /var/www/html
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Permissões iniciais (não necessário se usar bind mount no compose)
RUN chown -R www-data:www-data /var/www/html

RUN chown -R www-data:www-data /var/www/html/storage
RUN chmod -R 775 /var/www/html/storage

RUN mkdir -p /var/www/html/storage/app/public/logos


# ✅ Cria .env baseado no exemplo
RUN cp .env.example .env

COPY ./docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh
ENTRYPOINT ["/entrypoint.sh"]

# Permissões
RUN chown -R www-data:www-data storage bootstrap/cache