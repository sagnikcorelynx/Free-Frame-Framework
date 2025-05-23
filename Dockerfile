FROM php:8.0-fpm-alpine

RUN apk add --no-cache gcc musl-dev

RUN docker-php-ext-install pdo pdo_mysql

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app

COPY composer.json .
COPY composer.lock .
RUN composer install --no-dev

COPY . .

EXPOSE 9000
CMD ["php", "free", "serve", "--host=0.0.0.0", "--port=8000"]
