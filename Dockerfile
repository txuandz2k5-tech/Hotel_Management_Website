# ============================================
# Hotel Management Website - Dockerfile
# PHP 8.2 + Apache + MySQLi
# ============================================

FROM php:8.2-apache

# -----------------------------------------------
# 1. Cài đặt các extension PHP cần thiết
# -----------------------------------------------
RUN docker-php-ext-install mysqli pdo pdo_mysql \
    && a2enmod rewrite headers

# -----------------------------------------------
# 2. Cấu hình PHP cho production
# -----------------------------------------------
RUN cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

COPY docker/php.ini /usr/local/etc/php/conf.d/custom.ini

# -----------------------------------------------
# 3. Cấu hình Apache VirtualHost
# -----------------------------------------------
COPY docker/apache-vhost.conf /etc/apache2/sites-available/000-default.conf

# -----------------------------------------------
# 4. Sao chép mã nguồn ứng dụng vào container
# -----------------------------------------------
COPY . /var/www/html/

# -----------------------------------------------
# 5. Tạo thư mục cần thiết & phân quyền
# -----------------------------------------------
RUN mkdir -p /var/www/html/storage/logs \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 /var/www/html/storage

# -----------------------------------------------
# 6. Thay thế cấu hình DB bằng biến môi trường
#    (sẽ được inject từ docker-compose)
# -----------------------------------------------
COPY docker/connectDB.docker.php /var/www/html/MVC/Core/connectDB.php

# -----------------------------------------------
# 7. Cấu hình .htaccess cho Docker (root path)
# -----------------------------------------------
COPY docker/.htaccess.docker /var/www/html/.htaccess

EXPOSE 80

CMD ["apache2-foreground"]
