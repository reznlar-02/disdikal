#!/usr/bin/env bash
# Backup harian untuk database E-Questioner DISDIKAL.
# Data di tabel kunjungan & jawabansurvei adalah jawaban responden asli —
# tidak bisa diisi ulang kalau hilang, jadi backup ini wajib jalan sebelum go-live.
#
# Pemakaian:
#   ./scripts/backup-db.sh
#
# Jadwalkan lewat cron di server production, misalnya tiap hari jam 02:00:
#   0 2 * * * /path/ke/project/scripts/backup-db.sh >> /path/ke/project/storage/logs/backup.log 2>&1

set -euo pipefail

PROJECT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
ENV_FILE="$PROJECT_DIR/.env"
BACKUP_DIR="$PROJECT_DIR/storage/app/backups"
RETENTION_DAYS=14

if [ ! -f "$ENV_FILE" ]; then
    echo "Error: $ENV_FILE tidak ditemukan." >&2
    exit 1
fi

get_env() {
    grep -m1 "^${1}=" "$ENV_FILE" | cut -d '=' -f2- | tr -d '"'
}

DB_HOST="$(get_env DB_HOST)"
DB_PORT="$(get_env DB_PORT)"
DB_DATABASE="$(get_env DB_DATABASE)"
DB_USERNAME="$(get_env DB_USERNAME)"
DB_PASSWORD="$(get_env DB_PASSWORD)"

mkdir -p "$BACKUP_DIR"

TIMESTAMP="$(date +%Y%m%d_%H%M%S)"
OUTFILE="$BACKUP_DIR/${DB_DATABASE}_${TIMESTAMP}.sql.gz"

MYSQL_PWD="$DB_PASSWORD" mysqldump \
    --host="$DB_HOST" \
    --port="$DB_PORT" \
    --user="$DB_USERNAME" \
    --single-transaction \
    --quick \
    "$DB_DATABASE" | gzip > "$OUTFILE"

echo "Backup tersimpan: $OUTFILE"

# Hapus backup yang lebih tua dari retention window.
find "$BACKUP_DIR" -name "${DB_DATABASE}_*.sql.gz" -mtime "+${RETENTION_DAYS}" -delete
