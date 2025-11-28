#!/bin/bash
set -euo pipefail


TEMP="/home/saas/temp-rbp-backend"
DEST="/home/saas/app/rbp-backend"
USER="saas"

echo "************** Backend Deployment Start **************"


# 1) TEMP folder fix
sudo chown -R $USER:$USER "$TEMP"

# 2) Ensure destination exists
[ -d "$DEST" ] || mkdir -p "$DEST"

# 3) Sync files
rsync -av \
  --exclude=".git" \
  --exclude=".env" \
  --exclude="storage" \
  --exclude="uploads" \
  --delete-after \
  "$TEMP"/ "$DEST"/

cd "$DEST"

echo "Installing composer dependencies..."
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

sudo chown -R $USER:$USER "$DEST"

# 4) Laravel setup
# sudo -u saas php artisan key:generate --force || true
# sudo -u saas php artisan storage:link || true

echo "Fixing permissions..."
sudo chown -R $USER:www-data storage bootstrap/cache public
sudo chmod -R 775 storage bootstrap/cache public
sudo chmod -R g+s storage bootstrap/cache

sudo chown -R $USER:www-data storage/logs
sudo chmod -R 775 storage/logs

echo "Clearing & caching..."
sudo -u saas php artisan config:clear || true
sudo -u saas php artisan cache:clear || true
sudo -u saas php artisan route:clear || true
sudo -u saas php artisan view:clear || true

sudo -u saas php artisan config:cache || true
sudo -u saas php artisan route:cache || true
sudo -u saas php artisan view:cache || true

echo "Running migrations..."
#sudo -u saas php artisan migrate --force || true
=======
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

# php artisan storage:link || true

# ==========================================================
# 7) Fix permissions
# ==========================================================
echo "Fixing permissions..."
sudo chown -R $USER:$USER storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# ==========================================================
# 8) Cache Laravel
# ==========================================================
echo "Clearing & caching..."
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# ==========================================================
# 9) Migrate database
# ==========================================================
echo "Running migrations..."
# php artisan migrate --force || true

echo "############# Backend Deployment Completed Successfully ##############"
