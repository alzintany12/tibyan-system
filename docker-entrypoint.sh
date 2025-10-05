#!/bin/bash
set -e

# تنظيف الكاش قبل التشغيل
php artisan config:clear || true
php artisan cache:clear || true
php artisan route:clear || true
php artisan view:clear || true

# اختيارياً: تشغيل المهاجرات تلقائياً إن أردت
if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
  echo "RUN_MIGRATIONS=true → attempting migrate (retries)..."
  n=0
  until php artisan migrate --force; do
    n=$((n+1))
    if [ $n -ge 10 ]; then
      echo "Migrations failed after 10 attempts — aborting migration step."
      break
    fi
    echo "Migration failed, retrying in 5s..."
    sleep 5
  done
fi

exec "$@"
