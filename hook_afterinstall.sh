#!/bin/bash
set -euo pipefail

YELLOW='\033[1;33m'
GREEN='\033[0;32m'
NC='\033[0m'

TEMP_PROJECT_PATH="/home/saas/temp-rbp-backend"
PROJECT_PATH="/home/saas/app/rbp-backend"

echo -e "${YELLOW}************** Laravel Backend Deployment Start **************${NC}"

# === Parallel RSYNC ===
num_cpu=$(nproc)
process_cpu=$((num_cpu / 2))
if [ "$process_cpu" -lt 2 ]; then
  process_cpu=2
fi

echo "Syncing files using rsync..."

# PASS 1 (size-only) — IGNORE .env
ls -A "$TEMP_PROJECT_PATH" | xargs -I {} -P $process_cpu -n 1 rsync -rlpgoDK \
  --size-only \
  --exclude='.git' \
  --exclude='.env' \
  --delete-after \
  "$TEMP_PROJECT_PATH"/{} "$PROJECT_PATH" --out-format="%n"

# PASS 2 (checksum) — IGNORE .env
ls -A "$TEMP_PROJECT_PATH" | xargs -I {} -P $process_cpu -n 1 rsync -rlpgoDcK \
  --exclude='.git' \
  --exclude='.env' \
  "$TEMP_PROJECT_PATH"/{} "$PROJECT_PATH" --out-format="%n"

cd "$PROJECT_PATH"

# === Composer Install ===
echo -e "${YELLOW}Installing Composer dependencies...${NC}"
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

# === Laravel Setup ===
echo -e "${YELLOW}Running Laravel setup...${NC}"
php artisan key:generate --force || true
php artisan storage:link || true

# === Permissions ===
echo -e "${YELLOW}Fixing permissions...${NC}"

chown -R saas:saas "$PROJECT_PATH"
chmod -R 775 storage bootstrap/cache
chmod -R 775 vendor || true

# === Clear & Cache ===
echo -e "${YELLOW}Clearing and caching Laravel...${NC}"

php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

php artisan config:cache
php artisan route:cache
php artisan view:cache

# === Migrations ===
echo -e "${YELLOW}Running migrations...${NC}"
php artisan migrate --force || true

# === Restart services ===
echo -e "${YELLOW}Restarting PHP-FPM & Nginx...${NC}"
sudo systemctl restart php-fpm
sudo systemctl restart nginx

echo -e "${GREEN}############# Deployment Completed Successfully ##############${NC}"
