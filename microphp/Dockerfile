FROM php:8.1-fpm-alpine

RUN apk add --no-cache \
    g++ \
    make \
    autoconf \
    libc-dev \
    libzip-dev \
    php81-sockets \ 
    docker-php-ext-install \
    zip


# Install PHP extensions (no changes here)
RUN docker-php-ext-install pdo pdo_mysql

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy application code
COPY . /app

# Install project dependencies
RUN composer install --no-dev --optimize-autoloader

# Expose port for RoadRunner
EXPOSE 8080

# Add RoadRunner binary.  Replace with the correct version/architecture.
RUN wget -O ./rr "https://github.com/spiral/roadrunner/releases/download/v2.12.0/rr-linux-amd64" \
  && chmod +x ./rr

# Entrypoint command to start RoadRunner
CMD ["./rr", "serve", "-c", ".rr.yaml"]


# Create a .rr.yaml file (this would normally be part of your project)
COPY .rr.yaml /app/.rr.yaml 