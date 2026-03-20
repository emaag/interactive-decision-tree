FROM php:8.2.30-apache

COPY . /var/www/html/

RUN chown -R www-data:www-data /var/www/html/xml \
    && chmod 755 /var/www/html/xml

HEALTHCHECK --interval=30s --timeout=5s --start-period=10s --retries=3 \
    CMD curl -f http://localhost/ || exit 1
