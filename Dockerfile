# Utilizar la imagen oficial de PHP con Apache
FROM php:apache

# Actualizar repositorios e instalar dependencias necesarias
RUN apt-get update && \
    apt-get install -y \
    git \
    unzip

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Habilitar el módulo mod_rewrite en Apache
RUN a2enmod rewrite

# Establecer el directorio de trabajo
WORKDIR /var/www/html

# Copiar archivos del proyecto (si tienes un archivo composer.json)
COPY composer.json composer.json
COPY composer.lock composer.lock

# Instalar dependencias de Composer
RUN composer install --no-interaction --no-plugins --no-scripts

# Copiar el resto de los archivos del proyecto
COPY . .

# Exponer el puerto 80 para Apache
EXPOSE 80

# Comando para iniciar Apache
CMD ["apache2ctl", "-D", "FOREGROUND"]

