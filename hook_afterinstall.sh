#!/bin/bash
set -euo pipefail

# === Paths ===
TEMP="/home/saas/temp-rbp-backend"
DEST="/home/saas/app/rbp-backend"
USER="saas"

echo "************** Backend Deployment Start **************"

# ==========================================================
# 1) Fix TEMP folder permissions (CodeDeploy extracts as root)
# ==========================================================
echo "Fixing temp folder permissions..."
sudo chown -R $USER:$USER "$TEMP"

# ==========================================================
# 2) Ensure DEST folder exists and is owned by saas
# ==========================================================
if [ ! -d "$DEST" ]; then
  mkdir -p "$DEST"
fi

sudo chown -R $USER:$USER "$DEST"

# ==========================================================
# 3) RSYNC files except .env
# ==========================================================
echo "Running rsync…"

rsync -av \
  --exclude=".git" \
  --exclude=".env" \
  --delete-after \
  "$TEMP"/ "$DEST"/

# ==========================================================
# 4) Move into project directory
# ==========================================================
cd "$DEST"

# ==========================================================
# 5) Composer install (no-dev)
# ==========================================================
echo "Installing composer dependencies..."
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

# ==========================================================
# 6) Laravel setup (no .env override)
# ==========================================================
echo "Running Laravel setup..."

php artisan key:generate --force || true
php artisan storage:link || true

# ==========================================================
# 7) Fix permissions
# ==========================================================
echo "Fixing permissions..."
sudo chown -R $USER:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# ==========================================================
# 8) Cache Laravel
# ==========================================================
echo "Clearing & caching..."
php artisan config:clear || true
php artisan cache:clear || true
php artisan route:clear || true
php artisan view:clear || true

php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# ==========================================================
# 9) Migrate database
# ==========================================================
echo "Running migrations..."
php artisan migrate --force || true

echo "############# Backend Deployment Completed Successfully ##############"
