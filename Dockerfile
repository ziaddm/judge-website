FROM php:8.2-apache

# Install mysqli extension and enable it
RUN docker-php-ext-install mysqli pdo pdo_mysql && docker-php-ext-enable mysqli

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Suppress ServerName warning
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Copy application files
COPY . /var/www/html/

# Set permissions
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]
