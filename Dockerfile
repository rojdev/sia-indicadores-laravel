FROM ubuntu:latest

# Actualizar el sistema e instalar dependencias necesarias
RUN apt-get update && apt-get install -y \
    software-properties-common \
    curl \
    zip \
    unzip \
    git

# Añadir repositorio para PHP 7.4
RUN add-apt-repository ppa:ondrej/php -y
RUN apt-get update

# Instalar PHP 7.4 y extensiones necesarias para Laravel
RUN apt-get install -y php7.4 php7.4-cli php7.4-fpm php7.4-curl php7.4-mbstring php7.4-xml php7.4-zip php7.4-mysql

# Instalar Composer 2
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer --version

# Establecer directorio de trabajo dentro del contenedor
WORKDIR /var/www/html

# Copiar archivos de tu proyecto Laravel al contenedor (asegúrate de que tu proyecto Laravel está en el mismo directorio que el Dockerfile o en un subdirectorio)
COPY . .

# Instalar dependencias de Laravel con Composer
# RUN composer install --no-interaction --optimize-autoloader

# Configurar permisos (si es necesario, dependiendo de tu proyecto)
# RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
# RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Exponer el puerto 80 (o el puerto que use tu servidor web)
EXPOSE 7777

# Comando para iniciar tu aplicación Laravel (ejemplo usando el servidor de desarrollo de PHP, NO USAR EN PRODUCCIÓN)
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=7777"]
