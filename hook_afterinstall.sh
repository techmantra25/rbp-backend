#!/bin/bash
set -euo pipefail

# === Color codes ===
RED='\033[0;31m'
YELLOW='\033[1;33m'
GREEN='\033[0;32m'
NC='\033[0m' # No Color

# === Path configuration ===
TEMP_PROJECT_PATH="/home/saas/temp-rbp-backend"
PROJECT_PATH="/home/saas/app/rbp-backend"
USER_NAME="saas"

echo -e "${YELLOW}************** Laravel Backend Deployment Start **************${NC}"

# === Rsync with parallelism ===
num_cpu=$(nproc)
process_cpu=$((num_cpu / 2))
if [ "$process_cpu" -lt 2 ]; then
  process_cpu=2
fi

printf "Executing - rsync --size-only with -p $process_cpu processes \n"
ls -A "$TEMP_PROJECT_PATH" | xargs -I {} -P $process_cpu -n 1 rsync -rlpgoDK \
  --size-only --exclude='.git' --delete-after \
  "$TEMP_PROJECT_PATH"/{} "$PROJECT_PATH" --out-format="%n"

printf "Executing - rsync -c with -p $process_cpu processes \n"
ls -A "$TEMP_PROJECT_PATH"/ | xargs -I {} -P $process_cpu -n 1 rsync -rlpgoDcK \
  --exclude='.git' \
  "$TEMP_PROJECT_PATH"/{} "$PROJECT_PATH" --out-format="%n"

# === Move to project directory ===
cd "$PROJECT_PATH"

# === Composer setup ===
echo -e "${YELLOW}Installing dependencies...${NC}"
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev
composer update --no-interaction --prefer-dist --optimize-autoloader

# === Laravel setup ===
echo -e "${YELLOW}Running Laravel setup commands...${NC}"
php artisan key:generate --force
php artisan storage:link || true

# === Permissions ===
echo -e "${YELLOW}Fixing permissions...${NC}"
sudo chown -R "$USER_NAME":www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# === Clear & Cache Laravel configs ===
echo -e "${YELLOW}Clearing and caching Laravel configuration...${NC}"
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

php artisan config:cache
php artisan route:cache
php artisan view:cache

# === Migrate database ===
echo -e "${YELLOW}Running database migrations...${NC}"
php artisan migrate --force

# === Done ===
echo -e "${GREEN}############# Laravel Backend Deployment Completed Successfully ##############${NC}"
