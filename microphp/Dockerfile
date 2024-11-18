FROM php:8.1-fpm-alpine

RUN apk add --no-cache \
    g++ \
    make \
    autoconf \
    libc-dev \
    libzip-dev \
    && docker-php-ext-install zip sockets pdo pdo_mysql 

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
# Download and extract the latest RoadRunner binary
RUN wget https://github.com/roadrunner-server/roadrunner/releases/download/v2024.2.1/roadrunner-2024.2.1-linux-amd64.tar.gz \
    && tar -xzvf roadrunner-2024.2.1-linux-amd64.tar.gz \
    && mv roadrunner-2024.2.1-linux-amd64/rr . \  
    && rm roadrunner-2024.2.1-linux-amd64.tar.gz \
    && chmod +x ./rr

# Entrypoint command to start RoadRunner
CMD ["./rr", "serve", "-c", "rr.yaml"]


# Create a .rr.yaml file (this would normally be part of your project)
COPY .rr.yaml /app/.rr.yaml