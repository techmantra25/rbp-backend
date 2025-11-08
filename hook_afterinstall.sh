#!/bin/bash
set -euo pipefail

# === Color codes ===
RED='\033[0;31m'
YELLOW='\033[1;33m'
GREEN='\033[0;32m'
NC='\033[0m' # No Color
TEMP_PROJECT_PATH="/home/saas/temp-rbp-backend"

PROJECT_PATH="/home/saas/app/rbp-backend"
printf "live\n"

echo -e "${YELLOW}************** Backend Deployment Start **************${NC}"

# === Rsync with parallelism ===
num_cpu=$(nproc)
process_cpu=$((num_cpu / 2))

if [ -z "$process_cpu" ]; then
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


echo -e "${GREEN}############# Backend Deployment End ##############${NC}"
