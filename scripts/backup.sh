#!/bin/bash
# ===========================================
# Carbex - PostgreSQL Backup Script
# Uploads to Scaleway Object Storage
# ===========================================
# Usage: ./backup.sh [daily|weekly|monthly]
# Cron: 0 2 * * * /opt/carbex/scripts/backup.sh daily
# ===========================================

set -euo pipefail

# Configuration
BACKUP_TYPE="${1:-daily}"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/tmp/carbex-backups"
BACKUP_NAME="carbex_${BACKUP_TYPE}_${TIMESTAMP}"

# Database configuration (from environment or Docker secrets)
DB_HOST="${DB_HOST:-postgres}"
DB_PORT="${DB_PORT:-5432}"
DB_NAME="${DB_NAME:-carbex}"
DB_USER="${DB_USER:-carbex}"

# Load password from Docker secret if available
if [ -f /run/secrets/db_password ]; then
    export PGPASSWORD=$(cat /run/secrets/db_password)
else
    export PGPASSWORD="${DB_PASSWORD:-}"
fi

# Scaleway Object Storage configuration
SCW_ACCESS_KEY="${SCW_ACCESS_KEY:-}"
SCW_SECRET_KEY="${SCW_SECRET_KEY:-}"
SCW_REGION="${SCW_REGION:-fr-par}"
SCW_BUCKET="${SCW_BUCKET:-carbex-backups}"
SCW_ENDPOINT="https://s3.${SCW_REGION}.scw.cloud"

# Retention periods (in days)
RETENTION_DAILY=7
RETENTION_WEEKLY=30
RETENTION_MONTHLY=365

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

log_info() {
    echo -e "${GREEN}[INFO]${NC} $(date '+%Y-%m-%d %H:%M:%S') - $1"
}

log_warn() {
    echo -e "${YELLOW}[WARN]${NC} $(date '+%Y-%m-%d %H:%M:%S') - $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $(date '+%Y-%m-%d %H:%M:%S') - $1"
}

# Create backup directory
mkdir -p "${BACKUP_DIR}"

log_info "Starting ${BACKUP_TYPE} backup: ${BACKUP_NAME}"

# ===========================================
# PostgreSQL Backup
# ===========================================
log_info "Creating PostgreSQL dump..."

DUMP_FILE="${BACKUP_DIR}/${BACKUP_NAME}.sql.gz"

pg_dump -h "${DB_HOST}" -p "${DB_PORT}" -U "${DB_USER}" -d "${DB_NAME}" \
    --format=plain \
    --no-owner \
    --no-privileges \
    --clean \
    --if-exists \
    | gzip > "${DUMP_FILE}"

if [ $? -eq 0 ]; then
    DUMP_SIZE=$(du -h "${DUMP_FILE}" | cut -f1)
    log_info "PostgreSQL dump created: ${DUMP_FILE} (${DUMP_SIZE})"
else
    log_error "PostgreSQL dump failed!"
    exit 1
fi

# ===========================================
# Upload to Scaleway Object Storage
# ===========================================
if [ -n "${SCW_ACCESS_KEY}" ] && [ -n "${SCW_SECRET_KEY}" ]; then
    log_info "Uploading to Scaleway Object Storage..."

    # Configure AWS CLI for Scaleway
    export AWS_ACCESS_KEY_ID="${SCW_ACCESS_KEY}"
    export AWS_SECRET_ACCESS_KEY="${SCW_SECRET_KEY}"

    # Upload backup
    aws s3 cp "${DUMP_FILE}" \
        "s3://${SCW_BUCKET}/postgres/${BACKUP_TYPE}/${BACKUP_NAME}.sql.gz" \
        --endpoint-url "${SCW_ENDPOINT}" \
        --storage-class STANDARD

    if [ $? -eq 0 ]; then
        log_info "Backup uploaded successfully to s3://${SCW_BUCKET}/postgres/${BACKUP_TYPE}/"
    else
        log_error "Failed to upload backup to Scaleway!"
        exit 1
    fi

    # ===========================================
    # Cleanup old backups
    # ===========================================
    log_info "Cleaning up old backups..."

    case "${BACKUP_TYPE}" in
        daily)
            RETENTION=${RETENTION_DAILY}
            ;;
        weekly)
            RETENTION=${RETENTION_WEEKLY}
            ;;
        monthly)
            RETENTION=${RETENTION_MONTHLY}
            ;;
        *)
            RETENTION=${RETENTION_DAILY}
            ;;
    esac

    CUTOFF_DATE=$(date -d "${RETENTION} days ago" +%Y%m%d 2>/dev/null || date -v-${RETENTION}d +%Y%m%d)

    # List and delete old backups
    aws s3 ls "s3://${SCW_BUCKET}/postgres/${BACKUP_TYPE}/" \
        --endpoint-url "${SCW_ENDPOINT}" \
        | while read -r line; do
            BACKUP_DATE=$(echo "$line" | awk '{print $4}' | grep -oE '[0-9]{8}' | head -1)
            if [ -n "${BACKUP_DATE}" ] && [ "${BACKUP_DATE}" -lt "${CUTOFF_DATE}" ]; then
                FILE_NAME=$(echo "$line" | awk '{print $4}')
                log_info "Deleting old backup: ${FILE_NAME}"
                aws s3 rm "s3://${SCW_BUCKET}/postgres/${BACKUP_TYPE}/${FILE_NAME}" \
                    --endpoint-url "${SCW_ENDPOINT}"
            fi
        done

else
    log_warn "Scaleway credentials not configured. Backup saved locally only."
fi

# ===========================================
# Cleanup local files
# ===========================================
log_info "Cleaning up local temporary files..."
rm -f "${DUMP_FILE}"

# ===========================================
# Summary
# ===========================================
log_info "========================================="
log_info "Backup completed successfully!"
log_info "Type: ${BACKUP_TYPE}"
log_info "Name: ${BACKUP_NAME}"
log_info "Size: ${DUMP_SIZE}"
log_info "========================================="

# Send notification (optional - configure webhook)
if [ -n "${SLACK_WEBHOOK_URL:-}" ]; then
    curl -s -X POST "${SLACK_WEBHOOK_URL}" \
        -H 'Content-type: application/json' \
        -d "{\"text\": \"✅ Carbex backup completed: ${BACKUP_NAME} (${DUMP_SIZE})\"}"
fi

exit 0
