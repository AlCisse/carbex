# Cloudflare WAF Configuration

## Overview

Carbex uses Cloudflare as a Web Application Firewall (WAF) and CDN to protect against common attacks and improve performance.

## Setup Checklist

### 1. DNS Configuration

1. Add your domain to Cloudflare
2. Update nameservers at your registrar
3. Configure DNS records:

```
Type    Name            Content              Proxy
A       @               <server_ip>          ✅ Proxied
A       app             <server_ip>          ✅ Proxied
CNAME   www             carbex.eu            ✅ Proxied
MX      @               mail.carbex.eu       ❌ DNS only
```

### 2. SSL/TLS Settings

Navigate to **SSL/TLS** tab:

- **Encryption mode**: Full (strict)
- **Always Use HTTPS**: On
- **Automatic HTTPS Rewrites**: On
- **Minimum TLS Version**: 1.2
- **TLS 1.3**: On
- **HSTS**: Enable with settings:
  - Max Age: 12 months
  - Include subdomains: Yes
  - Preload: Yes

### 3. Firewall Rules

Navigate to **Security > WAF**:

#### Rule 1: Block Bad Bots
```
Expression: (cf.client.bot) and not (cf.verified_bot)
Action: Block
```

#### Rule 2: Rate Limit Login
```
Expression: (http.request.uri.path contains "/login") or (http.request.uri.path contains "/api/auth")
Action: Rate Limit (10 requests per minute)
```

#### Rule 3: Block Suspicious Countries (if needed)
```
Expression: (ip.geoip.country in {"CN" "RU" "KP"})
Action: Challenge
```

#### Rule 4: Protect Admin Panel
```
Expression: (http.request.uri.path contains "/admin") or (http.request.uri.path contains "/horizon")
Action: Challenge (or use Access for specific IPs)
```

#### Rule 5: Block SQL Injection Attempts
```
Expression: (http.request.uri.query contains "UNION") or
            (http.request.uri.query contains "SELECT") or
            (http.request.uri.query contains "DROP")
Action: Block
```

### 4. Page Rules

Navigate to **Rules > Page Rules**:

#### Cache Static Assets
```
URL: *carbex.eu/build/*
Settings:
  - Cache Level: Cache Everything
  - Edge Cache TTL: 1 month
  - Browser Cache TTL: 1 year
```

#### Bypass Cache for API
```
URL: *carbex.eu/api/*
Settings:
  - Cache Level: Bypass
  - Security Level: High
```

#### Bypass Cache for Livewire
```
URL: *carbex.eu/livewire/*
Settings:
  - Cache Level: Bypass
```

### 5. Security Settings

Navigate to **Security > Settings**:

- **Security Level**: High
- **Challenge Passage**: 30 minutes
- **Browser Integrity Check**: On
- **Hotlink Protection**: On (optional)

### 6. Bot Management

Navigate to **Security > Bots**:

- **Bot Fight Mode**: On
- **JavaScript Detection**: On
- Configure **Super Bot Fight Mode** if available

### 7. DDoS Protection

Navigate to **Security > DDoS**:

- Enable **DDoS Attack Protection**
- Set sensitivity to **High** for HTTP DDoS
- Enable **Rate Limiting** for your plan

### 8. Cloudflare Access (Zero Trust)

For admin panel protection:

1. Go to **Zero Trust** dashboard
2. Create an Access Application for `/admin` and `/horizon`
3. Configure authentication (email OTP, SSO, etc.)
4. Add allowed users/groups

## Monitoring

### Analytics

- Check **Analytics > Traffic** for suspicious patterns
- Monitor **Security > Events** for blocked requests
- Set up **Notifications** for attack alerts

### Logging

Enable **Logpush** to export logs to your SIEM:

```json
{
  "fields": [
    "ClientIP",
    "ClientRequestHost",
    "ClientRequestMethod",
    "ClientRequestURI",
    "EdgeResponseStatus",
    "SecurityLevel",
    "WAFAction"
  ]
}
```

## Environment Variables

Add to `.env` for Laravel:

```env
# Cloudflare configuration
CLOUDFLARE_ZONE_ID=your_zone_id
CLOUDFLARE_API_TOKEN=your_api_token

# Trust Cloudflare IPs
TRUSTED_PROXIES=*
```

## Firewall Origin Protection

Ensure your origin server only accepts traffic from Cloudflare:

```bash
# Allow only Cloudflare IPs (update regularly)
# https://www.cloudflare.com/ips/

# IPv4
ufw allow from 173.245.48.0/20 to any port 443
ufw allow from 103.21.244.0/22 to any port 443
# ... add all Cloudflare ranges

# Deny all other HTTPS
ufw deny 443
```

## Troubleshooting

### 521 Error (Web server is down)
- Check if origin server is running
- Verify firewall allows Cloudflare IPs
- Check SSL certificate on origin

### 522 Error (Connection timed out)
- Increase origin server timeout
- Check server load
- Verify network connectivity

### 524 Error (A timeout occurred)
- Increase PHP/application timeout
- Optimize slow endpoints
- Use Cloudflare caching

## Resources

- [Cloudflare Documentation](https://developers.cloudflare.com/)
- [WAF Managed Rules](https://developers.cloudflare.com/waf/managed-rules/)
- [Cloudflare IP Ranges](https://www.cloudflare.com/ips/)
