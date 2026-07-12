# LinsCarbon — Architecture Hosting & Sécurité
## Document Complémentaire à la Constitution

Ce document décrit l'architecture d'hébergement sécurisée pour un SaaS B2B gérant des données financières sensibles (Open Banking, comptabilité).

---

## 1. Principes de Sécurité

### 1.1 Niveau de sécurité cible

**Niveau bancaire** — LinsCarbon manipule :
- Données Open Banking (transactions bancaires)
- Données comptables (FEC, DATEV)
- Tokens d'accès API tierces
- Données de paiement (via Stripe)

### 1.2 Menaces à adresser

| Menace | Impact | Priorité |
|--------|--------|----------|
| **Piratage / Data breach** | Catastrophique — Fin de l'entreprise | Critique |
| **DDoS** | Service indisponible | Critique |
| **Scraping concurrent** | Vol de données, espionnage | Haute |
| **Brute force login** | Compromission comptes | Haute |
| **Injection SQL/XSS** | Vol de données | Haute |
| **Man-in-the-middle** | Interception tokens | Haute |
| **Secrets leakés** | Accès infra/APIs | Critique |

---

## 2. Architecture Infrastructure

### 2.1 Vue d'ensemble

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                              INTERNET                                        │
└─────────────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│                         CLOUDFLARE (Edge)                                    │
│  ┌─────────────────────────────────────────────────────────────────────────┐│
│  │  • WAF (Web Application Firewall)                                       ││
│  │  • DDoS Protection (L3/L4/L7)                                          ││
│  │  • Bot Management                                                       ││
│  │  • Rate Limiting                                                        ││
│  │  • Geo-blocking (hors EU si besoin)                                    ││
│  │  • SSL/TLS Termination (Full Strict)                                   ││
│  │  • Cache statique (CDN)                                                ││
│  │  • Zero Trust Access (admin)                                           ││
│  └─────────────────────────────────────────────────────────────────────────┘│
└─────────────────────────────────────────────────────────────────────────────┘
                                    │
                    SEULES IPs Cloudflare autorisées (voir 2.3)
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│                         FIREWALL HOST (iptables/nftables)                    │
│  ┌─────────────────────────────────────────────────────────────────────────┐│
│  │  • DROP ALL par défaut                                                  ││
│  │  • ACCEPT uniquement IPs Cloudflare sur 80/443                         ││
│  │  • ACCEPT SSH (22) depuis IP admin uniquement                          ││
│  │  • Fail2Ban actif                                                       ││
│  └─────────────────────────────────────────────────────────────────────────┘│
└─────────────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│                         DOCKER SWARM CLUSTER                                 │
│  ┌───────────────────────────────────────────────────────────────────────┐  │
│  │                         TRAEFIK (Reverse Proxy)                        │  │
│  │  • Seul point d'entrée HTTPS                                          │  │
│  │  • Let's Encrypt via Cloudflare DNS Challenge                         │  │
│  │  • Headers sécurité (HSTS, CSP, X-Frame-Options)                      │  │
│  │  • Rate limiting par IP                                                │  │
│  │  • IP Whitelist Cloudflare only                                       │  │
│  │  • Middlewares auth (JWT validation)                                  │  │
│  └───────────────────────────────────────────────────────────────────────┘  │
│                                    │                                         │
│          ┌─────────────────────────┼─────────────────────────┐              │
│          ▼                         ▼                         ▼              │
│  ┌───────────────┐        ┌───────────────┐        ┌───────────────┐       │
│  │   LARAVEL     │        │   LARAVEL     │        │   LARAVEL     │       │
│  │   APP (x3)    │        │   QUEUE (x2)  │        │   SCHEDULER   │       │
│  │               │        │   (Horizon)   │        │   (cron)      │       │
│  │  /run/secrets │        │  /run/secrets │        │  /run/secrets │       │
│  └───────────────┘        └───────────────┘        └───────────────┘       │
│          │                         │                         │              │
│          └─────────────────────────┼─────────────────────────┘              │
│                                    ▼                                         │
│  ┌───────────────────────────────────────────────────────────────────────┐  │
│  │                     NETWORK INTERNE (overlay)                          │  │
│  │  • Isolée du monde extérieur                                          │  │
│  │  • Communication inter-services chiffrée                              │  │
│  └───────────────────────────────────────────────────────────────────────┘  │
│          │                         │                         │              │
│          ▼                         ▼                         ▼              │
│  ┌───────────────┐        ┌───────────────┐        ┌───────────────┐       │
│  │  POSTGRESQL   │        │     REDIS     │        │  MEILISEARCH  │       │
│  │   (Primary)   │        │   (Cache/Q)   │        │   (Search)    │       │
│  │               │        │               │        │               │       │
│  │  Encrypted    │        │  Password     │        │  API Key      │       │
│  │  at rest      │        │  protected    │        │  protected    │       │
│  └───────────────┘        └───────────────┘        └───────────────┘       │
│                                                                              │
│  ┌───────────────────────────────────────────────────────────────────────┐  │
│  │                     DOCKER SECRETS (Swarm)                             │  │
│  │  • Chiffrés at rest (Raft log)                                        │  │
│  │  • Montés en RAM dans /run/secrets/                                   │  │
│  │  • Jamais en .env, jamais en variable d'environnement                 │  │
│  └───────────────────────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────────────────────┘
```

### 2.2 Stack technique

| Composant | Technologie | Rôle |
|-----------|-------------|------|
| **CDN/WAF** | Cloudflare Pro | Protection edge, cache, DDoS |
| **Reverse Proxy** | Traefik 3.x | Routing, TLS, middlewares |
| **Orchestration** | Docker Swarm | HA, secrets, rolling updates |
| **Firewall Host** | nftables + Fail2Ban | Filtrage IP, anti-bruteforce |
| **Secrets** | Docker Swarm Secrets | Gestion sécurisée credentials |
| **Monitoring** | Prometheus + Grafana | Métriques, alertes |
| **Logs** | Loki + Promtail | Centralisation, audit |
| **Backup** | Restic + S3 | Chiffré, versionné |

### 2.3 Whitelist IPs Cloudflare

Script de mise à jour automatique des IPs Cloudflare :

```bash
#!/bin/bash
# /opt/scripts/update-cloudflare-ips.sh
# À exécuter via cron chaque jour

set -e

# Récupérer les IPs Cloudflare
CF_IPS_V4=$(curl -s https://www.cloudflare.com/ips-v4)
CF_IPS_V6=$(curl -s https://www.cloudflare.com/ips-v6)

# Flush les anciennes règles
nft flush chain inet filter cloudflare 2>/dev/null || true

# Créer la chaîne si n'existe pas
nft add chain inet filter cloudflare 2>/dev/null || true

# Ajouter les IPs v4
for ip in $CF_IPS_V4; do
    nft add rule inet filter cloudflare ip saddr $ip tcp dport {80, 443} accept
done

# Ajouter les IPs v6
for ip in $CF_IPS_V6; do
    nft add rule inet filter cloudflare ip6 saddr $ip tcp dport {80, 443} accept
done

# Log
echo "$(date): Cloudflare IPs updated" >> /var/log/cloudflare-ips.log
```

---

## 3. Configuration Cloudflare

### 3.1 DNS

```
Type    Name              Content              Proxy    TTL
A       linscarbon.io         <SERVER_IP>          ON       Auto
CNAME   www               linscarbon.io            ON       Auto
CNAME   app               linscarbon.io            ON       Auto
CNAME   api               linscarbon.io            ON       Auto
CNAME   admin             linscarbon.io            ON       Auto (+ Zero Trust)
```

### 3.2 SSL/TLS Settings

```yaml
SSL Mode: Full (Strict)
# Le serveur a un cert Let's Encrypt valide

Minimum TLS Version: TLS 1.2
TLS 1.3: Enabled
Automatic HTTPS Rewrites: ON
Always Use HTTPS: ON

# Origin Certificates
# Générer un cert Cloudflare Origin pour Traefik
```

### 3.3 WAF Rules (Custom)

```javascript
// Rule 1: Block non-EU countries (si applicable)
(not ip.geoip.continent in {"EU"}) and not cf.client.bot
→ Block

// Rule 2: Rate limit API endpoints
(http.request.uri.path contains "/api/" and not cf.client.bot)
→ Rate limit: 100 req/min per IP

// Rule 3: Block suspicious user agents
(http.user_agent contains "curl" or http.user_agent contains "wget")
and not ip.src in {<ALLOWED_IPS>}
→ Block

// Rule 4: Protect login endpoints
(http.request.uri.path eq "/login" or http.request.uri.path eq "/api/auth")
→ Rate limit: 10 req/min per IP

// Rule 5: Block known bad bots
(cf.client.bot and not cf.bot_management.verified_bot)
→ Managed Challenge
```

### 3.4 Zero Trust Access (Admin Panel)

```yaml
Application: LinsCarbon Admin
Domain: admin.linscarbon.io

Policies:
  - Name: Admin Only
    Action: Allow
    Include:
      - Emails ending in: @linscarbon.io
    Require:
      - Login Methods: Google Workspace
      - Device Posture: Require WARP client

Authentication:
  - Identity Provider: Google
  - Session Duration: 8 hours
```

---

## 4. Configuration Traefik

### 4.1 traefik.yml (Static Config)

```yaml
# /etc/traefik/traefik.yml

api:
  dashboard: true
  insecure: false

entryPoints:
  web:
    address: ":80"
    http:
      redirections:
        entryPoint:
          to: websecure
          scheme: https

  websecure:
    address: ":443"
    http:
      middlewares:
        - cloudflare-only@file
        - security-headers@file
      tls:
        certResolver: cloudflare

certificatesResolvers:
  cloudflare:
    acme:
      email: ssl@linscarbon.io
      storage: /etc/traefik/acme.json
      dnsChallenge:
        provider: cloudflare
        delayBeforeCheck: 10
        resolvers:
          - "1.1.1.1:53"
          - "8.8.8.8:53"

providers:
  docker:
    endpoint: "tcp://socket-proxy:2375"
    exposedByDefault: false
    swarmMode: true
    network: traefik-public
  file:
    directory: /etc/traefik/dynamic
    watch: true

log:
  level: WARN
  filePath: /var/log/traefik/traefik.log

accessLog:
  filePath: /var/log/traefik/access.log
  format: json
  fields:
    headers:
      names:
        CF-Connecting-IP: keep
        CF-IPCountry: keep
        X-Forwarded-For: keep
```

### 4.2 Middlewares (Dynamic Config)

```yaml
# /etc/traefik/dynamic/middlewares.yml

http:
  middlewares:
    # Accepter uniquement les IPs Cloudflare
    cloudflare-only:
      ipAllowList:
        sourceRange:
          # IPv4 Cloudflare (à mettre à jour régulièrement)
          - "173.245.48.0/20"
          - "103.21.244.0/22"
          - "103.22.200.0/22"
          - "103.31.4.0/22"
          - "141.101.64.0/18"
          - "108.162.192.0/18"
          - "190.93.240.0/20"
          - "188.114.96.0/20"
          - "197.234.240.0/22"
          - "198.41.128.0/17"
          - "162.158.0.0/15"
          - "104.16.0.0/13"
          - "104.24.0.0/14"
          - "172.64.0.0/13"
          - "131.0.72.0/22"
          # IPv6 Cloudflare
          - "2400:cb00::/32"
          - "2606:4700::/32"
          - "2803:f800::/32"
          - "2405:b500::/32"
          - "2405:8100::/32"
          - "2a06:98c0::/29"
          - "2c0f:f248::/32"
          # IPs internes Docker (pour healthchecks)
          - "10.0.0.0/8"
          - "172.16.0.0/12"
          - "192.168.0.0/16"

    # Headers de sécurité
    security-headers:
      headers:
        browserXssFilter: true
        contentTypeNosniff: true
        frameDeny: true
        sslRedirect: true
        stsIncludeSubdomains: true
        stsPreload: true
        stsSeconds: 31536000
        customFrameOptionsValue: "SAMEORIGIN"
        customResponseHeaders:
          X-Robots-Tag: "noindex, nofollow"
          server: ""  # Hide server header
        contentSecurityPolicy: |
          default-src 'self';
          script-src 'self' 'unsafe-inline' https://js.stripe.com;
          style-src 'self' 'unsafe-inline';
          img-src 'self' data: https:;
          font-src 'self';
          connect-src 'self' https://api.stripe.com;
          frame-src https://js.stripe.com;

    # Rate limiting API
    rate-limit-api:
      rateLimit:
        average: 100
        burst: 200
        period: 1m

    # Rate limiting auth (plus strict)
    rate-limit-auth:
      rateLimit:
        average: 10
        burst: 20
        period: 1m

    # Compression
    compress:
      compress:
        excludedContentTypes:
          - text/event-stream
```

---

## 5. Docker Swarm Secrets

### 5.1 Liste des secrets

```bash
# Secrets à créer AVANT le déploiement

# Database
echo "SuperSecurePassword123!" | docker secret create db_password -
echo "linscarbon_production" | docker secret create db_database -
echo "linscarbon_user" | docker secret create db_username -

# Redis
openssl rand -base64 32 | docker secret create redis_password -

# Laravel
php artisan key:generate --show | docker secret create app_key -

# API Keys tierces
echo "sk_live_..." | docker secret create stripe_secret_key -
echo "whsec_..." | docker secret create stripe_webhook_secret -
echo "sk-ant-..." | docker secret create claude_api_key -
echo "xxx" | docker secret create bridge_client_id -
echo "xxx" | docker secret create bridge_client_secret -
echo "xxx" | docker secret create finapi_client_id -
echo "xxx" | docker secret create finapi_client_secret -

# Cloudflare (pour Traefik ACME)
echo "your-cf-api-token" | docker secret create cloudflare_api_token -

# Meilisearch
openssl rand -base64 32 | docker secret create meilisearch_master_key -
```

### 5.2 Lecture des secrets dans Laravel

```php
// config/database.php

'pgsql' => [
    'driver' => 'pgsql',
    'host' => env('DB_HOST', 'postgres'),
    'port' => env('DB_PORT', '5432'),
    'database' => file_exists('/run/secrets/db_database')
        ? trim(file_get_contents('/run/secrets/db_database'))
        : env('DB_DATABASE', 'linscarbon'),
    'username' => file_exists('/run/secrets/db_username')
        ? trim(file_get_contents('/run/secrets/db_username'))
        : env('DB_USERNAME', 'linscarbon'),
    'password' => file_exists('/run/secrets/db_password')
        ? trim(file_get_contents('/run/secrets/db_password'))
        : env('DB_PASSWORD', ''),
    // ...
],
```

```php
// app/Providers/AppServiceProvider.php

public function boot(): void
{
    // Charger les secrets Docker Swarm
    $this->loadDockerSecrets();
}

private function loadDockerSecrets(): void
{
    $secretsPath = '/run/secrets';

    if (!is_dir($secretsPath)) {
        return; // Pas en environnement Swarm
    }

    $secretMappings = [
        'app_key' => 'APP_KEY',
        'db_password' => 'DB_PASSWORD',
        'redis_password' => 'REDIS_PASSWORD',
        'stripe_secret_key' => 'STRIPE_SECRET',
        'stripe_webhook_secret' => 'STRIPE_WEBHOOK_SECRET',
        'claude_api_key' => 'ANTHROPIC_API_KEY',
        'bridge_client_id' => 'BRIDGE_CLIENT_ID',
        'bridge_client_secret' => 'BRIDGE_CLIENT_SECRET',
        'meilisearch_master_key' => 'MEILISEARCH_KEY',
    ];

    foreach ($secretMappings as $secretFile => $envKey) {
        $filePath = "{$secretsPath}/{$secretFile}";
        if (file_exists($filePath)) {
            config()->set(
                $this->getConfigKey($envKey),
                trim(file_get_contents($filePath))
            );
        }
    }
}
```

### 5.3 Docker Compose (Swarm mode)

```yaml
# docker-compose.prod.yml

version: "3.9"

services:
  app:
    image: linscarbon/app:${VERSION:-latest}
    deploy:
      replicas: 3
      update_config:
        parallelism: 1
        delay: 30s
        failure_action: rollback
      restart_policy:
        condition: on-failure
        delay: 5s
        max_attempts: 3
      labels:
        - "traefik.enable=true"
        - "traefik.http.routers.app.rule=Host(`app.linscarbon.io`)"
        - "traefik.http.routers.app.entrypoints=websecure"
        - "traefik.http.routers.app.tls.certresolver=cloudflare"
        - "traefik.http.services.app.loadbalancer.server.port=8080"
    secrets:
      - app_key
      - db_password
      - db_database
      - db_username
      - redis_password
      - stripe_secret_key
      - stripe_webhook_secret
      - claude_api_key
      - bridge_client_id
      - bridge_client_secret
      - meilisearch_master_key
    networks:
      - traefik-public
      - internal
    environment:
      - APP_ENV=production
      - APP_DEBUG=false
      - LOG_CHANNEL=stderr
      # Pas de secrets ici ! Uniquement configs non-sensibles
      - DB_HOST=postgres
      - REDIS_HOST=redis
      - CACHE_DRIVER=redis
      - QUEUE_CONNECTION=redis
      - SESSION_DRIVER=redis

  queue:
    image: linscarbon/app:${VERSION:-latest}
    command: php artisan horizon
    deploy:
      replicas: 2
    secrets:
      - app_key
      - db_password
      - db_database
      - db_username
      - redis_password
      - stripe_secret_key
      - claude_api_key
      - bridge_client_id
      - bridge_client_secret
    networks:
      - internal

  scheduler:
    image: linscarbon/app:${VERSION:-latest}
    command: php artisan schedule:work
    deploy:
      replicas: 1
    secrets:
      - app_key
      - db_password
      - db_database
      - db_username
      - redis_password
    networks:
      - internal

  postgres:
    image: postgres:17-alpine
    deploy:
      replicas: 1
      placement:
        constraints:
          - node.labels.db == true
    secrets:
      - db_password
      - db_database
      - db_username
    environment:
      - POSTGRES_PASSWORD_FILE=/run/secrets/db_password
      - POSTGRES_DB_FILE=/run/secrets/db_database
      - POSTGRES_USER_FILE=/run/secrets/db_username
    volumes:
      - postgres_data:/var/lib/postgresql/data
    networks:
      - internal

  redis:
    image: redis:7.4-alpine
    command: >
      sh -c "redis-server --requirepass $(cat /run/secrets/redis_password)"
    deploy:
      replicas: 1
    secrets:
      - redis_password
    volumes:
      - redis_data:/data
    networks:
      - internal

  meilisearch:
    image: getmeili/meilisearch:v1.11
    deploy:
      replicas: 1
    secrets:
      - meilisearch_master_key
    environment:
      - MEILI_MASTER_KEY_FILE=/run/secrets/meilisearch_master_key
      - MEILI_ENV=production
    volumes:
      - meilisearch_data:/meili_data
    networks:
      - internal

  traefik:
    image: traefik:v3.2
    deploy:
      replicas: 1
      placement:
        constraints:
          - node.role == manager
    ports:
      - target: 80
        published: 80
        protocol: tcp
        mode: host
      - target: 443
        published: 443
        protocol: tcp
        mode: host
    secrets:
      - cloudflare_api_token
    environment:
      - CF_API_TOKEN_FILE=/run/secrets/cloudflare_api_token
    volumes:
      - /etc/traefik:/etc/traefik:ro
      - traefik_certs:/etc/traefik/acme
      - /var/log/traefik:/var/log/traefik
    networks:
      - traefik-public
      - internal

  socket-proxy:
    image: tecnativa/docker-socket-proxy
    deploy:
      replicas: 1
      placement:
        constraints:
          - node.role == manager
    environment:
      - CONTAINERS=1
      - SERVICES=1
      - SWARM=1
      - TASKS=1
      - NETWORKS=1
    volumes:
      - /var/run/docker.sock:/var/run/docker.sock:ro
    networks:
      - internal

secrets:
  app_key:
    external: true
  db_password:
    external: true
  db_database:
    external: true
  db_username:
    external: true
  redis_password:
    external: true
  stripe_secret_key:
    external: true
  stripe_webhook_secret:
    external: true
  claude_api_key:
    external: true
  bridge_client_id:
    external: true
  bridge_client_secret:
    external: true
  finapi_client_id:
    external: true
  finapi_client_secret:
    external: true
  meilisearch_master_key:
    external: true
  cloudflare_api_token:
    external: true

networks:
  traefik-public:
    driver: overlay
    attachable: true
  internal:
    driver: overlay
    internal: true  # Pas d'accès internet

volumes:
  postgres_data:
  redis_data:
  meilisearch_data:
  traefik_certs:
```

---

## 6. Fail2Ban Configuration

### 6.1 Installation et config de base

```bash
# Installation
apt install fail2ban -y

# Config principale
cat > /etc/fail2ban/jail.local << 'EOF'
[DEFAULT]
bantime = 1h
findtime = 10m
maxretry = 5
banaction = nftables-multiport
ignoreip = 127.0.0.1/8 ::1

# Email alerting
destemail = security@linscarbon.io
sender = fail2ban@linscarbon.io
action = %(action_mwl)s

[sshd]
enabled = true
port = 22
maxretry = 3
bantime = 24h

[traefik-auth]
enabled = true
filter = traefik-auth
logpath = /var/log/traefik/access.log
maxretry = 10
findtime = 5m
bantime = 1h

[traefik-badbots]
enabled = true
filter = traefik-badbots
logpath = /var/log/traefik/access.log
maxretry = 1
bantime = 7d
EOF
```

### 6.2 Filtres personnalisés

```bash
# Filtre pour les échecs d'auth via Traefik
cat > /etc/fail2ban/filter.d/traefik-auth.conf << 'EOF'
[Definition]
failregex = ^.*"ClientHost":"<HOST>".*"RequestPath":"/(login|api/auth)".*"DownstreamStatus":(401|403).*$
ignoreregex =
EOF

# Filtre pour les bots malveillants
cat > /etc/fail2ban/filter.d/traefik-badbots.conf << 'EOF'
[Definition]
failregex = ^.*"ClientHost":"<HOST>".*"RequestPath":".*(\.php|\.asp|\.env|wp-admin|\.git|xmlrpc).*".*$
            ^.*"ClientHost":"<HOST>".*"request_User-Agent":".*(sqlmap|nikto|nmap|masscan|zgrab).*".*$
ignoreregex =
EOF
```

---

## 7. Monitoring & Alerting

### 7.1 Stack Monitoring

```yaml
# Ajout au docker-compose.prod.yml

  prometheus:
    image: prom/prometheus:v2.50.0
    deploy:
      replicas: 1
    volumes:
      - prometheus_data:/prometheus
      - ./prometheus.yml:/etc/prometheus/prometheus.yml:ro
    networks:
      - internal
    command:
      - '--config.file=/etc/prometheus/prometheus.yml'
      - '--storage.tsdb.retention.time=30d'

  grafana:
    image: grafana/grafana:10.3.0
    deploy:
      replicas: 1
      labels:
        - "traefik.enable=true"
        - "traefik.http.routers.grafana.rule=Host(`monitoring.linscarbon.io`)"
        - "traefik.http.routers.grafana.middlewares=auth-admin@file"
    volumes:
      - grafana_data:/var/lib/grafana
    networks:
      - traefik-public
      - internal

  loki:
    image: grafana/loki:2.9.0
    deploy:
      replicas: 1
    volumes:
      - loki_data:/loki
    networks:
      - internal

  promtail:
    image: grafana/promtail:2.9.0
    deploy:
      mode: global
    volumes:
      - /var/log:/var/log:ro
      - ./promtail.yml:/etc/promtail/config.yml:ro
    networks:
      - internal
```

### 7.2 Alertes critiques

```yaml
# alerting-rules.yml (Prometheus)

groups:
  - name: linscarbon-critical
    rules:
      - alert: HighErrorRate
        expr: rate(traefik_service_requests_total{code=~"5.."}[5m]) > 0.1
        for: 5m
        labels:
          severity: critical
        annotations:
          summary: "High 5xx error rate"

      - alert: DatabaseDown
        expr: pg_up == 0
        for: 1m
        labels:
          severity: critical
        annotations:
          summary: "PostgreSQL is down"

      - alert: HighMemoryUsage
        expr: (node_memory_MemTotal_bytes - node_memory_MemAvailable_bytes) / node_memory_MemTotal_bytes > 0.9
        for: 5m
        labels:
          severity: warning

      - alert: SSLCertExpiringSoon
        expr: probe_ssl_earliest_cert_expiry - time() < 86400 * 7
        labels:
          severity: warning
        annotations:
          summary: "SSL certificate expires in less than 7 days"

      - alert: SuspiciousLoginAttempts
        expr: rate(laravel_failed_logins_total[5m]) > 10
        for: 2m
        labels:
          severity: warning
        annotations:
          summary: "Unusual number of failed login attempts"
```

---

## 8. Backup & Disaster Recovery

### 8.1 Strategy

| Données | Fréquence | Rétention | Destination |
|---------|-----------|-----------|-------------|
| PostgreSQL | Toutes les 6h | 30 jours | S3 Scaleway (chiffré) |
| Redis (RDB) | Quotidien | 7 jours | S3 Scaleway |
| Uploads (S3) | Continu (réplication) | Illimité | S3 cross-region |
| Secrets | Manuel (vault) | Versionné | Bitwarden/1Password |
| Configs | Git | Illimité | GitHub Private |

### 8.2 Script backup PostgreSQL

```bash
#!/bin/bash
# /opt/scripts/backup-postgres.sh

set -e

BACKUP_DIR="/tmp/backups"
S3_BUCKET="s3://linscarbon-backups-eu/postgres"
DATE=$(date +%Y%m%d-%H%M%S)
ENCRYPTION_KEY=$(cat /run/secrets/backup_encryption_key)

# Dump
docker exec $(docker ps -q -f name=postgres) \
  pg_dump -U linscarbon -Fc linscarbon > "${BACKUP_DIR}/linscarbon-${DATE}.dump"

# Encrypt
gpg --symmetric --cipher-algo AES256 \
  --passphrase "$ENCRYPTION_KEY" \
  --batch --yes \
  "${BACKUP_DIR}/linscarbon-${DATE}.dump"

# Upload to S3
aws s3 cp "${BACKUP_DIR}/linscarbon-${DATE}.dump.gpg" "${S3_BUCKET}/"

# Cleanup local
rm -f "${BACKUP_DIR}/linscarbon-${DATE}.dump"*

# Cleanup old backups (keep 30 days)
aws s3 ls "${S3_BUCKET}/" | while read -r line; do
  createDate=$(echo $line | awk '{print $1" "$2}')
  createDate=$(date -d "$createDate" +%s)
  olderThan=$(date -d "-30 days" +%s)
  if [[ $createDate -lt $olderThan ]]; then
    fileName=$(echo $line | awk '{print $4}')
    aws s3 rm "${S3_BUCKET}/${fileName}"
  fi
done

echo "$(date): Backup completed successfully" >> /var/log/backup.log
```

---

## 9. Checklist Sécurité Production

### 9.1 Avant mise en production

- [ ] **Firewall** : Tous ports fermés sauf 80/443 (Cloudflare IPs only) et 22 (IP admin)
- [ ] **Cloudflare** : Proxy activé (orange cloud), WAF rules configurées
- [ ] **Secrets** : Aucun secret dans .env, tous en Docker Secrets
- [ ] **TLS** : Full Strict mode, HSTS activé, TLS 1.2 minimum
- [ ] **Headers** : CSP, X-Frame-Options, X-Content-Type-Options configurés
- [ ] **Fail2Ban** : Actif sur SSH et Traefik logs
- [ ] **Backups** : Testés et chiffrés
- [ ] **Monitoring** : Prometheus + Grafana + alertes configurés
- [ ] **Logs** : Centralisés dans Loki, rétention 90 jours
- [ ] **Updates** : Processus de mise à jour automatique des images

### 9.2 Audits réguliers

| Audit | Fréquence | Outil |
|-------|-----------|-------|
| Scan vulnérabilités images | Chaque build | Trivy |
| Pentest externe | Annuel | Prestataire certifié |
| Revue secrets | Trimestriel | Manuel |
| Rotation credentials | Semestriel | Scripts |
| Test restoration backup | Mensuel | Scripts |
| Revue logs accès | Hebdomadaire | Grafana dashboards |

---

## 10. Coûts Infrastructure

### 10.1 Estimation mensuelle (Launch)

| Service | Provider | Specs | Coût/mois |
|---------|----------|-------|-----------|
| VPS Manager | Scaleway DEV1-M | 3 vCPU, 4GB RAM | 15€ |
| VPS Worker x2 | Scaleway DEV1-M | 3 vCPU, 4GB RAM | 30€ |
| Object Storage | Scaleway S3 | 100GB | 5€ |
| Cloudflare Pro | Cloudflare | WAF + analytics | 20€ |
| Backup storage | Scaleway S3 | 50GB | 2€ |
| **TOTAL** | | | **~72€/mois** |

### 10.2 Évolution (Scale)

| Phase | Infra | Coût estimé |
|-------|-------|-------------|
| Launch (0-100 clients) | 3 nodes Swarm | ~75€/mois |
| Growth (100-500 clients) | 5 nodes + DB dédié | ~200€/mois |
| Scale (500-2000 clients) | Cluster HA + CDN | ~500€/mois |

---

## Conclusion

Cette architecture offre :

- **Défense en profondeur** : Cloudflare → Firewall → Traefik → App
- **Zero secrets en clair** : Docker Swarm Secrets uniquement
- **Protection DDoS** : Cloudflare Pro + Rate limiting
- **Anti-scraping** : Bot management + WAF rules
- **Conformité RGPD** : Données en EU, chiffrement, audit logs
- **Haute disponibilité** : Swarm replicas, rolling updates
- **Coût maîtrisé** : ~75€/mois au lancement

Cette architecture est adaptée à un SaaS B2B manipulant des données financières sensibles et peut évoluer avec la croissance de LinsCarbon.

---

## Historique des Versions

| Version | Date | Auteur | Modifications |
|---------|------|--------|---------------|
| 1.0 | 28.12.2025 | Claude AI + Alhassane Cisse | Création initiale |
