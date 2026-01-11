# Carbex Deployment Runbook

## Table of Contents

1. [Overview](#overview)
2. [Prerequisites](#prerequisites)
3. [Infrastructure Setup](#infrastructure-setup)
4. [Deployment Process](#deployment-process)
5. [Environment Configuration](#environment-configuration)
6. [Post-Deployment Verification](#post-deployment-verification)
7. [Rollback Procedures](#rollback-procedures)
8. [Monitoring & Alerts](#monitoring--alerts)
9. [Troubleshooting](#troubleshooting)

---

## Overview

This runbook covers the deployment of Carbex to production environments. The application is designed to run on any container-based infrastructure (Docker, Kubernetes) or traditional VPS/cloud servers.

### Deployment Architecture

```
                    ┌─────────────┐
                    │   CDN/WAF   │
                    │  (Cloudflare)│
                    └──────┬──────┘
                           │
                    ┌──────▼──────┐
                    │ Load Balancer│
                    │  (nginx/ALB) │
                    └──────┬──────┘
                           │
        ┌──────────────────┼──────────────────┐
        │                  │                  │
  ┌─────▼─────┐     ┌─────▼─────┐     ┌─────▼─────┐
  │  App Node │     │  App Node │     │  App Node │
  │  (PHP-FPM)│     │  (PHP-FPM)│     │  (PHP-FPM)│
  └─────┬─────┘     └─────┬─────┘     └─────┬─────┘
        │                 │                 │
        └────────────┬────┴────┬────────────┘
                     │         │
              ┌──────▼──┐ ┌────▼─────┐
              │PostgreSQL│ │  Redis   │
              │ (Primary)│ │ (Cluster)│
              └──────────┘ └──────────┘
```

---

## Prerequisites

### Required Services

- **PostgreSQL 15+**: Primary database
- **Redis 7+**: Cache, sessions, queues
- **S3-compatible storage**: File uploads, reports
- **SMTP service**: Transactional emails
- **Domain & SSL**: Valid TLS certificate

### Required Credentials

Before deployment, ensure you have:

- [ ] Database connection credentials
- [ ] Redis connection credentials
- [ ] AWS S3 or compatible storage credentials
- [ ] SMTP credentials (SendGrid, Mailgun, etc.)
- [ ] Bridge API credentials (banking)
- [ ] Stripe API keys (billing)
- [ ] Sentry DSN (error tracking)

---

## Infrastructure Setup

### Option 1: Docker Deployment

#### docker-compose.production.yml

```yaml
version: '3.8'

services:
  app:
    build:
      context: .
      dockerfile: Dockerfile
    environment:
      - APP_ENV=production
      - APP_DEBUG=false
    volumes:
      - storage:/var/www/html/storage
    depends_on:
      - postgres
      - redis
    deploy:
      replicas: 3
      resources:
        limits:
          memory: 512M
          cpus: '0.5'

  queue:
    build:
      context: .
      dockerfile: Dockerfile
    command: php artisan queue:work --queue=high,default,low --sleep=3 --tries=3
    environment:
      - APP_ENV=production
    depends_on:
      - postgres
      - redis
    deploy:
      replicas: 2

  scheduler:
    build:
      context: .
      dockerfile: Dockerfile
    command: php artisan schedule:work
    environment:
      - APP_ENV=production
    depends_on:
      - postgres
      - redis
    deploy:
      replicas: 1

  nginx:
    image: nginx:alpine
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./nginx.conf:/etc/nginx/nginx.conf
      - ./ssl:/etc/nginx/ssl
    depends_on:
      - app

  postgres:
    image: postgres:15-alpine
    environment:
      POSTGRES_DB: carbex
      POSTGRES_USER: carbex
      POSTGRES_PASSWORD: ${DB_PASSWORD}
    volumes:
      - postgres_data:/var/lib/postgresql/data

  redis:
    image: redis:7-alpine
    command: redis-server --appendonly yes
    volumes:
      - redis_data:/data

volumes:
  storage:
  postgres_data:
  redis_data:
```

### Option 2: Kubernetes Deployment

See `kubernetes/` directory for Helm charts and manifests.

### Option 3: Traditional Server

```bash
# Install dependencies
sudo apt update
sudo apt install -y php8.2-fpm php8.2-pgsql php8.2-redis \
    php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip \
    nginx redis-server supervisor

# Configure PHP-FPM
sudo nano /etc/php/8.2/fpm/pool.d/www.conf
# Set: pm = dynamic, pm.max_children = 50

# Configure nginx
sudo nano /etc/nginx/sites-available/carbex
# See nginx configuration below

# Configure supervisor for queue workers
sudo nano /etc/supervisor/conf.d/carbex-worker.conf
```

---

## Deployment Process

### 1. Pre-Deployment Checks

```bash
# Verify current version
git log -1 --format="%H %s"

# Check for pending migrations
php artisan migrate:status

# Verify environment
php artisan env

# Run tests
php artisan test --env=testing
```

### 2. Deploy New Version

```bash
# Pull latest code
git fetch origin
git checkout main
git pull origin main

# Install/update dependencies
composer install --no-dev --optimize-autoloader

# Install frontend dependencies and build
npm ci
npm run build

# Clear and cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

### 3. Database Migrations

```bash
# Enable maintenance mode
php artisan down --retry=60 --refresh=15

# Backup database before migration
pg_dump -h $DB_HOST -U $DB_USER $DB_NAME > backup_$(date +%Y%m%d_%H%M%S).sql

# Run migrations
php artisan migrate --force

# Disable maintenance mode
php artisan up
```

### 4. Post-Deploy Tasks

```bash
# Restart PHP-FPM
sudo systemctl restart php8.2-fpm

# Restart queue workers
php artisan queue:restart

# Clear application caches
php artisan cache:clear

# Warm caches
php artisan config:cache
php artisan route:cache
```

---

## Environment Configuration

### Production .env Template

```env
# Application
APP_NAME=Carbex
APP_ENV=production
APP_KEY=base64:YOUR_APP_KEY_HERE
APP_DEBUG=false
APP_URL=https://app.carbex.app

# Logging
LOG_CHANNEL=stack
LOG_LEVEL=warning

# Database
DB_CONNECTION=pgsql
DB_HOST=your-db-host
DB_PORT=5432
DB_DATABASE=carbex_production
DB_USERNAME=carbex
DB_PASSWORD=your-secure-password

# Redis
REDIS_HOST=your-redis-host
REDIS_PASSWORD=your-redis-password
REDIS_PORT=6379

# Cache & Session
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# File Storage
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=your-access-key
AWS_SECRET_ACCESS_KEY=your-secret-key
AWS_DEFAULT_REGION=eu-west-3
AWS_BUCKET=carbex-production
AWS_URL=https://cdn.carbex.app

# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your-sendgrid-api-key
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@carbex.app
MAIL_FROM_NAME="Carbex"

# Banking Integration
BRIDGE_API_URL=https://api.bridgeapi.io
BRIDGE_CLIENT_ID=your-bridge-client-id
BRIDGE_CLIENT_SECRET=your-bridge-secret
BRIDGE_WEBHOOK_SECRET=your-bridge-webhook-secret

# Billing
STRIPE_KEY=pk_live_xxx
STRIPE_SECRET=sk_live_xxx
STRIPE_WEBHOOK_SECRET=whsec_xxx

# Error Tracking
SENTRY_LARAVEL_DSN=https://xxx@sentry.io/xxx
SENTRY_TRACES_SAMPLE_RATE=0.1

# Security
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=strict
FORCE_HTTPS=true
```

### Security Checklist

- [ ] `APP_DEBUG=false`
- [ ] `APP_ENV=production`
- [ ] Strong database password
- [ ] Strong Redis password
- [ ] Secure cookies enabled
- [ ] HTTPS enforced
- [ ] Rate limiting configured
- [ ] CORS properly configured

---

## Post-Deployment Verification

### Health Checks

```bash
# Check application health
curl -s https://app.carbex.app/api/health | jq .

# Expected response:
{
  "status": "healthy",
  "database": "connected",
  "redis": "connected",
  "queue": "running"
}
```

### Functional Tests

```bash
# Test authentication
curl -X POST https://app.carbex.app/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"password"}'

# Test API endpoints
curl https://app.carbex.app/api/v1/emissions \
  -H "Authorization: Bearer $TOKEN"
```

### Monitor Logs

```bash
# Application logs
tail -f storage/logs/laravel.log

# Queue worker logs
tail -f /var/log/supervisor/carbex-worker*.log

# nginx access logs
tail -f /var/log/nginx/access.log
```

---

## Rollback Procedures

### Quick Rollback (Code Only)

```bash
# Enable maintenance mode
php artisan down

# Revert to previous commit
git checkout HEAD~1

# Reinstall dependencies
composer install --no-dev --optimize-autoloader

# Clear caches
php artisan config:cache
php artisan route:cache

# Restart services
sudo systemctl restart php8.2-fpm
php artisan queue:restart

# Disable maintenance mode
php artisan up
```

### Full Rollback (Including Database)

```bash
# Enable maintenance mode
php artisan down

# Restore database backup
psql -h $DB_HOST -U $DB_USER $DB_NAME < backup_YYYYMMDD_HHMMSS.sql

# Revert code
git checkout $PREVIOUS_COMMIT_SHA

# Reinstall dependencies
composer install --no-dev --optimize-autoloader

# Clear and rebuild caches
php artisan optimize:clear
php artisan config:cache
php artisan route:cache

# Restart services
sudo systemctl restart php8.2-fpm
php artisan queue:restart

# Disable maintenance mode
php artisan up
```

---

## Monitoring & Alerts

### Key Metrics to Monitor

| Metric | Warning Threshold | Critical Threshold |
|--------|-------------------|-------------------|
| Response time (p95) | > 500ms | > 2000ms |
| Error rate | > 1% | > 5% |
| Queue depth | > 100 jobs | > 500 jobs |
| Database connections | > 80% | > 95% |
| Redis memory | > 80% | > 95% |
| Disk usage | > 80% | > 95% |

### Alert Configuration (Example: Sentry)

Configure alerts in `config/sentry.php` and Sentry dashboard for:

- 500 error spikes
- Queue job failures
- Slow database queries (> 1s)
- Memory limit warnings

### Uptime Monitoring

Configure external monitoring (UptimeRobot, Pingdom) for:

- `https://app.carbex.app/api/health`
- `https://app.carbex.app/login`

---

## Troubleshooting

### Common Issues

#### 1. 500 Internal Server Error

```bash
# Check logs
tail -f storage/logs/laravel.log

# Common causes:
# - Missing .env variables
# - Database connection issues
# - Permission problems
```

#### 2. Queue Jobs Not Processing

```bash
# Check worker status
sudo supervisorctl status

# Restart workers
sudo supervisorctl restart carbex-worker:*

# Check failed jobs
php artisan queue:failed
php artisan queue:retry all
```

#### 3. Database Connection Errors

```bash
# Test connection
php artisan tinker
>>> DB::connection()->getPdo()

# Check PostgreSQL status
sudo systemctl status postgresql
```

#### 4. Redis Connection Issues

```bash
# Test connection
redis-cli -h $REDIS_HOST -a $REDIS_PASSWORD ping

# Check memory usage
redis-cli info memory
```

#### 5. File Upload Failures

```bash
# Check S3 credentials
php artisan tinker
>>> Storage::disk('s3')->put('test.txt', 'test')

# Check permissions
ls -la storage/app/
chmod -R 775 storage/
chown -R www-data:www-data storage/
```

---

## Emergency Contacts

| Role | Contact |
|------|---------|
| DevOps Lead | devops@carbex.app |
| Backend Lead | backend@carbex.app |
| On-Call | +33 X XX XX XX XX |

---

## Changelog

| Date | Version | Changes |
|------|---------|---------|
| 2024-12-29 | 1.0.0 | Initial runbook creation |
