# Carbex - robots.txt
# https://carbex.fr

User-agent: *
Allow: /

# Sitemaps
Sitemap: {{ url('/sitemap.xml') }}

# Disallow admin and authenticated areas
Disallow: /dashboard
Disallow: /dashboard/*
Disallow: /settings
Disallow: /settings/*
Disallow: /admin
Disallow: /admin/*
Disallow: /api/
Disallow: /livewire/
Disallow: /sanctum/

# Disallow login/register for crawlers (optional, remove if you want them indexed)
# Disallow: /login
# Disallow: /register

# Block common bot paths
Disallow: /wp-admin/
Disallow: /wp-login.php
Disallow: /.env
Disallow: /storage/

# Crawl-delay (optional, in seconds)
Crawl-delay: 1

# Specific rules for major search engines
User-agent: Googlebot
Allow: /

User-agent: Bingbot
Allow: /

User-agent: Slurp
Allow: /

# Block AI crawlers (optional - remove if you want AI indexing)
User-agent: GPTBot
Disallow: /

User-agent: ChatGPT-User
Disallow: /

User-agent: CCBot
Disallow: /

User-agent: anthropic-ai
Disallow: /
