FROM php:8.2-cli

WORKDIR /app

# Устанавливаем расширения
RUN docker-php-ext-install curl

# Копируем файлы
COPY . .

# Запускаем встроенный сервер PHP
CMD ["php", "-S", "0.0.0.0:10000"]
