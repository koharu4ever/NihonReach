#!/usr/bin/env bash
set -euo pipefail

workspace="/workspaces/nihonreach"
cd "${workspace}"

if ! git config --global --get-all safe.directory 2>/dev/null | grep -Fxq "${workspace}"; then
    git config --global --add safe.directory "${workspace}"
fi

if [[ -f composer.json ]]; then
    composer install --no-interaction
fi

if [[ -f artisan && -f .env ]] && ! grep -Eq '^APP_KEY=base64:' .env; then
    php artisan key:generate --no-interaction
fi

if [[ -f package-lock.json ]]; then
    npm ci
fi

bash .devcontainer/scripts/smoke-test.sh

printf '\nNihonReach development container is ready.\n'

if git config --get user.name >/dev/null 2>&1 && git config --get user.email >/dev/null 2>&1; then
    printf 'Git author identity was detected. Confirm these sources before the first commit:\n'
    git config --show-origin --get user.name
    git config --show-origin --get user.email
else
    printf 'Git author identity is incomplete; configure and verify it before the first commit.\n'
fi

printf 'Run `codex` once and sign in interactively when you want to use Codex CLI here.\n'
