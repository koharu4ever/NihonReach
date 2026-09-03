#!/bin/sh
set -eu

# Secrets are supplied at runtime. Never generate a fresh key on deployment.
: "${APP_KEY:?Set a persistent production APP_KEY in Coolify}"

# Cache only after runtime variables have been injected. No migration or seed here.
php artisan config:cache
php artisan route:cache
php artisan view:cache

exec apache2-foreground
