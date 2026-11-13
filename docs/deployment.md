# Mylo Deployment Instructions

## Overview

Mylo is a modular Laravel monolith with three independent Next.js portals sharing one backend. This document covers deployment for production environments.

## Architecture Overview

```
┌─────────────────┐      ┌─────────────────┐      ┌─────────────────┐
│   Reader Portal │      │  Writer Portal  │      │ Admin Portal    │
│   (Next.js)     │      │   (Next.js)     │      │   (Next.js)     │
└─────────┬───────┘      └─────────┬───────┘      └─────────┬───────┘
          │                        │                        │
          └──────────┬─────────────┴─────────────┬────────┘
                     │                           │
          ┌──────────▼─────────────┐   ┌────────▼─────────┐
          │    Load Balancer       │   │   Laravel API    │
          │   (Nginx/Traefik)      │   │   (Laravel 11)   │
          └──────────┬─────────────┘   └────────┬─────────┘
                     │                           │
          ┌──────────▼─────────────┐   ┌────────▼─────────┐
          │   PostgreSQL 17        │   │   Redis (opt)    │
          │   (Primary DB)         │   │   (Cache/Queue)  │
          └────────────────────────┘   └──────────────────┘
                     │
          ┌──────────▼─────────────┐
          │   S3-Compatible Storage│
          │   (Media, Ebooks, Covers)│
          └────────────────────────┘
                     │
          ┌──────────▼─────────────┐
          │   Meilisearch          │
          │   (Search Index)       │
          └────────────────────────┘
```

## Prerequisites

- PHP 8.2+
- Composer 2.8+
- Node.js 24+
- PostgreSQL 17+
- Redis (optional, for production caching/queues)
- S3-compatible object storage (MinIO for dev, AWS S3 for prod)
- Meilisearch (for search)
- Email delivery service (SMTP, Mailgun, SendGrid, etc.)
- Payment gateway accounts (M-Pesa, Stripe)

## Environment Variables

Create a `.env` file in the `backend/` directory:

```bash
# Application
APP_NAME=Mylo
APP_ENV=production
APP_DEBUG=false
APP_URL=https://api.mylo.test
APP_TIMEZONE=Africa/Nairobi

# Database
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=mylo
DB_USERNAME=mylo
DB_PASSWORD=your-secure-password

# Cache & Queue (Redis recommended for production)
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_PASSWORD=your-redis-password

# Session
SESSION_DRIVER=redis
SESSION_LIFETIME=120

# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.yourprovider.com
MAIL_PORT=587
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@mylo.test
MAIL_FROM_NAME="Mylo"

# Sanctum
SANCTUM_STATEFUL_DOMAINS=reader.mylo.test,writer.mylo.test,admin.mylo.test
SESSION_DOMAIN=.mylo.test

# Media Storage
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=your-access-key
AWS_SECRET_ACCESS_KEY=your-secret-key
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=mylo-media
AWS_URL=https://cdn.mylo.test

# Meilisearch
MEILISEARCH_HOST=http://127.0.0.1:7700
MEILISEARCH_API_KEY=your-master-key

# Payment Gateways
MPESA_CONSUMER_KEY=your-mpesa-consumer-key
MPESA_CONSUMER_SECRET=your-mpesa-consumer-secret
MPESA_SHORTCODE=your-mpesa-shortcode
MPESA_PASSKEY=your-mpesa-passkey
MPESA_ENVIRONMENT=sandbox  # or production

STRIPE_KEY=your-stripe-publishable-key
STRIPE_SECRET=your-stripe-secret-key
STRIPE_WEBHOOK_SECRET=your-stripe-webhook-secret

# Platform Configuration
PLATFORM_COMMISSION_RATE=0.05
MINIMUM_PAYOUT=10.00
CURRENCY=KES
```

## Backend Deployment

### Option 1: Traditional Server

```bash
# 1. Clone the repository
git clone https://github.com/your-org/mylo.git
cd mylo/backend

# 2. Install PHP dependencies
composer install --optimize-autoloader --no-dev

# 3. Install Node.js dependencies (for frontend assets if needed)
npm ci --production

# 4. Generate application key
php artisan key:generate

# 5. Run migrations
php artisan migrate --force

# 6. Run seeders
php artisan db:seed --force

# 7. Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 8. Start queue worker (for background jobs)
php artisan queue:work --daemon &

# 9. Start Laravel server (or use PHP-FPM with Nginx)
php artisan serve --host=0.0.0.0 --port=8000
```

### Option 2: Docker

Create a `Dockerfile` in the `backend/` directory:

```dockerfile
FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip \
    libpq-dev postgresql-client \
    && docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy application
COPY . .

# Install dependencies
RUN composer install --optimize-autoloader --no-dev

# Generate application key
RUN php artisan key:generate

# Run migrations
RUN php artisan migrate --force

# Cache configuration
RUN php artisan config:cache
RUN php artisan route:cache
RUN php artisan view:cache

# Expose port
EXPOSE 8000

# Start server
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
```

Create a `docker-compose.yml` in the project root:

```yaml
version: '3.8'

services:
  backend:
    build: ./backend
    ports:
      - "8000:8000"
    environment:
      - DB_CONNECTION=pgsql
      - DB_HOST=db
      - DB_DATABASE=mylo
      - DB_USERNAME=mylo
      - DB_PASSWORD=mylo
      - CACHE_DRIVER=redis
      - QUEUE_CONNECTION=redis
      - REDIS_HOST=redis
    depends_on:
      - db
      - redis

  db:
    image: postgres:17
    environment:
      - POSTGRES_DB=mylo
      - POSTGRES_USER=mylo
      - POSTGRES_PASSWORD=mylo
    volumes:
      - postgres_data:/var/lib/postgresql/data

  redis:
    image: redis:7-alpine

  meilisearch:
    image: getmeili/meilisearch:latest
    environment:
      - MEILISEARCH_API_KEY=your-master-key
    ports:
      - "7700:7700"

  minio:
    image: minio/minio
    environment:
      - MINIO_ROOT_USER=mylo
      - MINIO_ROOT_PASSWORD=mylo-password
    command: server /data --console-address ":9001"
    ports:
      - "9000:9000"
      - "9001:9001"

volumes:
  postgres_data:
```

## Frontend Deployment

Each portal is a standalone Next.js application. Deploy each separately.

### Build

```bash
# Reader Portal
cd frontend/reader
npm ci --production
npm run build
npm start  # or deploy to Vercel/Netlify

# Writer Portal
cd frontend/writer
npm ci --production
npm run build
npm start

# Administrator Portal
cd frontend/admin
npm ci --production
npm run build
npm start
```

### Environment Variables for Frontends

Create `.env.production` in each frontend:

```bash
# Reader Portal (.env.production)
NEXT_PUBLIC_API_URL=https://api.mylo.test
NEXT_PUBLIC_API_URL=https://api.mylo.test/api/v1

# Writer Portal (.env.production)
NEXT_PUBLIC_API_URL=https://api.mylo.test
NEXT_PUBLIC_API_URL=https://api.mylo.test/api/v1

# Administrator Portal (.env.production)
NEXT_PUBLIC_API_URL=https://api.mylo.test
NEXT_PUBLIC_API_URL=https://api.mylo.test/api/v1
```

### Docker for Frontends

Create a `Dockerfile` for each portal:

```dockerfile
FROM node:24-alpine

WORKDIR /app

COPY package*.json ./
RUN npm ci --production

COPY . .
RUN npm run build

EXPOSE 3000

CMD ["npm", "start"]
```

## CI/CD Pipeline

### GitHub Actions Example

Create `.github/workflows/deploy.yml`:

```yaml
name: Deploy Mylo

on:
  push:
    branches: [main]

jobs:
  backend:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - name: Set up PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
      - name: Install Composer dependencies
        run: |
          cd backend
          composer install --optimize-autoloader --no-dev
      - name: Run migrations
        run: |
          cd backend
          php artisan migrate --force
        env:
          DB_CONNECTION: pgsql
          DB_HOST: ${{ secrets.DB_HOST }}
          DB_DATABASE: ${{ secrets.DB_DATABASE }}
          DB_USERNAME: ${{ secrets.DB_USERNAME }}
          DB_PASSWORD: ${{ secrets.DB_PASSWORD }}
      - name: Run tests
        run: |
          cd backend
          php artisan test
        env:
          DB_CONNECTION: pgsql
          DB_HOST: ${{ secrets.DB_HOST }}
          DB_DATABASE: ${{ secrets.DB_DATABASE }}
          DB_USERNAME: ${{ secrets.DB_USERNAME }}
          DB_PASSWORD: ${{ secrets.DB_PASSWORD }}

  frontend-reader:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - name: Set up Node.js
        uses: actions/setup-node@v4
        with:
          node-version: '24'
      - name: Install dependencies
        run: |
          cd frontend/reader
          npm ci --production
      - name: Build
        run: |
          cd frontend/reader
          npm run build
        env:
          NEXT_PUBLIC_API_URL: ${{ secrets.READER_API_URL }}

  frontend-writer:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - name: Set up Node.js
        uses: actions/setup-node@v4
        with:
          node-version: '24'
      - name: Install dependencies
        run: |
          cd frontend/writer
          npm ci --production
      - name: Build
        run: |
          cd frontend/writer
          npm run build
        env:
          NEXT_PUBLIC_API_URL: ${{ secrets.WRITER_API_URL }}

  frontend-admin:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - name: Set up Node.js
        uses: actions/setup-node@v4
        with:
          node-version: '24'
      - name: Install dependencies
        run: |
          cd frontend/admin
          npm ci --production
      - name: Build
        run: |
          cd frontend/admin
          npm run build
        env:
          NEXT_PUBLIC_API_URL: ${{ secrets.ADMIN_API_URL }}
```

## Backup Strategy

### Database Backups

```bash
# Daily database backup
pg_dump -h localhost -U mylo mylo > backup_$(date +%Y%m%d).sql

# Upload to S3
aws s3 cp backup_$(date +%Y%m%d).sql s3://mylo-backups/database/
```

### Media Backups

```bash
# Sync media to S3
aws s3 sync /var/www/mylo/storage/app/public s3://mylo-backups/media/
```

### Restore

```bash
# Restore database
pg_dump -h localhost -U mylo mylo < backup_20240101.sql

# Restore media
aws s3 sync s3://mylo-backups/media/ /var/www/mylo/storage/app/public
```

## Monitoring & Logging

### Laravel Logging
- Logs stored in `storage/logs/laravel.log`
- Configure error monitoring (Sentry, Bugsnag, etc.) in `config/logging.php`

### Health Check
- Endpoint: `GET /up`
- Returns 200 if application is running

### Uptime Monitoring
- Set up uptime monitoring for:
  - `https://api.mylo.test/up`
  - `https://reader.mylo.test`
  - `https://writer.mylo.test`
  - `https://admin.mylo.test`

## Security Checklist

- [ ] All API traffic over HTTPS
- [ ] `.env` file not committed to version control
- [ ] `APP_DEBUG=false` in production
- [ ] Strong database passwords
- [ ] Redis password configured
- [ ] S3 bucket not publicly accessible
- [ ] Payment webhook signatures verified
- [ ] Sanctum stateful domains configured correctly
- [ ] CORS configured (if needed)
- [ ] Rate limiting enabled
- [ ] Security headers enabled
- [ ] Database backups scheduled
- [ ] Error monitoring configured (Sentry, etc.)

## Performance Checklist

- [ ] Database indexes added (see migration `2024_01_01_000020`)
- [ ] Response caching enabled (CacheResponse middleware)
- [ ] Redis configured for cache and queue
- [ ] Image optimization enabled
- [ ] CDN configured for media delivery
- [ ] Laravel route and config caching enabled
- [ ] OPcache enabled in PHP
- [ ] Database query logging disabled in production

## Mobile App Readiness

The backend API is fully mobile-ready:
- All endpoints consumable via REST
- Sanctum token authentication
- Consistent JSON response format
- Pagination metadata
- Rate limiting
- Webhook verification for payments
- See `docs/mobile-readiness.md` for complete API documentation

## Support

For deployment issues, check:
1. `storage/logs/laravel.log` for application errors
2. `storage/logs/worker.log` for queue worker errors
3. Web server error logs (Nginx/Apache)
4. Database connection status
5. Environment variable configuration
