FROM php:8.4-apache

# Adiciona metadata do Container
LABEL Name=scheduler Version=0.0.1

# Atualiza a lista de pacotes disponíveis
RUN apt-get -y update

# Instala o unzip, zip, curl e git
RUN apt-get install -y\
    unzip zip curl git 

# Instala extensões do PHP necessárias
RUN docker-php-ext-install pdo pdo_mysql

# Instala Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Ativa o mod_rewrite do Apache (necessário pro Slim)
RUN a2enmod rewrite

# Configura Document Root
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

# Atualiza o VirtualHost
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html