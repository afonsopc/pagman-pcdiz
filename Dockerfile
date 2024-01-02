FROM php:8.3.1-apache

WORKDIR /var/www/html

# Install mysqli extension
RUN docker-php-ext-install mysqli

# Copy a custom Apache configuration file that reads the environment variables
COPY apache-config.conf /etc/apache2/sites-available/000-default.conf

# Copy the PHP files
COPY ./src /var/www/html/

# Enable Apache modules and restart Apache
RUN a2enmod rewrite

CMD ["apache2-foreground"]
