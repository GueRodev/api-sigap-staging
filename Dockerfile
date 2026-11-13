# SIGAP · API (Laravel 13, PHP 8.5) — imagen para Dokploy
# Base: PHP-FPM + nginx en un solo contenedor, escucha en 8080.
FROM serversideup/php:8.5-fpm-nginx

# Laravel expone /up como health check (bootstrap/app.php).
ENV HEALTHCHECK_PATH=/up \
    PHP_OPCACHE_ENABLE=1

# Código con dueño www-data (el usuario que corre PHP).
COPY --chown=www-data:www-data . /var/www/html

# Dependencias de producción (sin Pest, Pint, etc.).
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# NO se cachea config aquí: las variables (.env) las inyecta
# Dokploy al arrancar. Con AUTORUN_ENABLED=true el contenedor
# ejecuta migrate --force y optimize al iniciar.
