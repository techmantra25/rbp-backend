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
  --delete-after \
  "$TEMP"/ "$DEST"/

cd "$DEST"

echo "Installing composer dependencies..."
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

sudo chown -R $USER:$USER "$DEST"

# 4) Laravel setup
sudo -u saas php artisan key:generate --force || true
sudo -u saas php artisan storage:link || true

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

echo "############# Backend Deployment Completed Successfully ##############"
