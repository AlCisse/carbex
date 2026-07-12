# Docker Secrets - Clés API IA

Ce dossier contient les clés API pour les différents providers IA.
**Ces fichiers sont ignorés par Git pour des raisons de sécurité.**

## Configuration des clés API

### 1. Anthropic (Claude)
```bash
echo "sk-ant-api03-VOTRE_CLE_ICI" > anthropic_api_key
chmod 600 anthropic_api_key
```

### 2. OpenAI (GPT)
```bash
echo "sk-VOTRE_CLE_ICI" > openai_api_key
chmod 600 openai_api_key
```

### 3. Google (Gemini)
```bash
echo "VOTRE_CLE_ICI" > google_api_key
chmod 600 google_api_key
```

### 4. DeepSeek
```bash
echo "sk-VOTRE_CLE_ICI" > deepseek_api_key
chmod 600 deepseek_api_key
```

## Utilisation dans Docker

Les secrets sont montés automatiquement dans `/run/secrets/` dans le container.

### docker-compose.yml
```yaml
services:
  app:
    secrets:
      - anthropic_api_key
      - openai_api_key
      - google_api_key
      - deepseek_api_key

secrets:
  anthropic_api_key:
    file: ./docker/secrets/anthropic_api_key
  openai_api_key:
    file: ./docker/secrets/openai_api_key
  google_api_key:
    file: ./docker/secrets/google_api_key
  deepseek_api_key:
    file: ./docker/secrets/deepseek_api_key
```

## Vérification

Dans le container, vérifiez que les secrets sont bien montés :
```bash
docker exec -it linscarbon-app ls -la /run/secrets/
```

## Sécurité

- Ne jamais commiter les fichiers de clés API
- Utiliser `chmod 600` pour restreindre les permissions
- Les fichiers `*_api_key`, `*.key` et `*.secret` sont ignorés par `.gitignore`
