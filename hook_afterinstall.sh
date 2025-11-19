#!/bin/bash
set -euo pipefail

TEMP="/home/saas/temp-rbp-backend"
DEST="/home/saas/app/rbp-backend"
USER="saas"

echo "************** Backend Deployment Start **************"

# 1) TEMP folder fix
echo "Fixing temp folder permissions..."
sudo chown -R $USER:$USER "$TEMP"

# 2) Ensure destination exists
if [ ! -d "$DEST" ]; then
  mkdir -p "$DEST"
fi

# 3) Sync files (exclude .env)
echo "Running rsync…"
rsync -av \
  --exclude=".git" \
  --exclude=".env" \
  --delete-after \
  "$TEMP"/ "$DEST"/


cd "$DEST"

# 4) Composer install
echo "Installing composer dependencies..."
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

sudo chown -R $USER:$USER "$DEST"  
# 5) Laravel setup
php artisan key:generate --force || true
php artisan storage:link || true

# 6) Correct full permissions (IMPORTANT)
echo "Fixing permissions..."

# Ownership
sudo chown -R $USER:www-data storage bootstrap/cache public

# Permissions
sudo chmod -R 775 storage bootstrap/cache public

# Ensure Laravel writable dirs
sudo chmod -R g+s storage bootstrap/cache

# FIX logs folder specifically
sudo chown -R $USER:www-data storage/logs
sudo chmod -R 775 storage/logs

su $USER
# 7) Cache clearing
echo "Clearing & caching..."
php artisan config:clear || true
php artisan cache:clear || true
php artisan route:clear || true
php artisan view:clear || true

php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# 8) DB migrations
echo "Running migrations..."
php artisan migrate --force || true

echo "############# Backend Deployment Completed Successfully ##############"
