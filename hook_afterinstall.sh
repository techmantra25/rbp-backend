#!/bin/bash
set -euo pipefail

YELLOW='\033[1;33m'
GREEN='\033[0;32m'
NC='\033[0m'

TEMP_PROJECT_PATH="/home/saas/temp-rbp-backend"
PROJECT_PATH="/home/saas/app/rbp-backend"

echo -e "${YELLOW}************** Backend Deployment Start **************${NC}"

# === Parallel RSYNC ===
num_cpu=$(nproc)
process_cpu=$((num_cpu / 2))
if [ "$process_cpu" -lt 2 ]; then
  process_cpu=2
fi

echo "Syncing files using rsync..."

# PASS 1 — SIZE ONLY
ls -A "$TEMP_PROJECT_PATH" | xargs -I {} -P $process_cpu -n 1 rsync -rlpgoDK \
  --size-only \
  --exclude='.git' \
  --exclude='.env' \
  --exclude='hook_afterinstall.sh' \
  --exclude='hook_afterinstall_cleanup.sh' \
  --exclude='appspec.yml' \
  "$TEMP_PROJECT_PATH"/{} "$PROJECT_PATH" --out-format="%n"

# PASS 2 — CHECKSUM
ls -A "$TEMP_PROJECT_PATH" | xargs -I {} -P $process_cpu -n 1 rsync -rlpgoDcK \
  --exclude='.git' \
  --exclude='.env' \
  --exclude='hook_afterinstall.sh' \
  --exclude='hook_afterinstall_cleanup.sh' \
  --exclude='appspec.yml' \
  "$TEMP_PROJECT_PATH"/{} "$PROJECT_PATH" --out-format="%n"

cd "$PROJECT_PATH"

# === Composer Installation ===
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

# === Clear & Cache Laravel ===
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

# === Restart PHP-FPM & Nginx ===
echo -e "${YELLOW}Restarting PHP-FPM & Nginx...${NC}"
sudo systemctl restart php-fpm
sudo systemctl restart nginx

echo -e "${GREEN}############# Backend Deployment Completed Successfully ##############${NC}"
