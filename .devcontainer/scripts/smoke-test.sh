#!/usr/bin/env bash
set -euo pipefail

workspace="/workspaces/nihonreach"
cd "${workspace}"

printf '== Toolchain ==\n'
php --version | head -n 1
composer --version
node --version
npm --version
git --version
codex --version

printf '\n== Required PHP extensions ==\n'
for extension in bcmath curl mbstring pcntl pdo_mysql pdo_sqlite sqlite3; do
    php -r "exit(extension_loaded('${extension}') ? 0 : 1);"
    printf '%s: ok\n' "${extension}"
done

php -r "exit(extension_loaded('Zend OPcache') ? 0 : 1);"
printf 'Zend OPcache: ok\n'

printf '\n== MySQL connection ==\n'
php <<'PHP'
<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$version = Illuminate\Support\Facades\DB::selectOne('SELECT VERSION() AS version')->version;
echo "MySQL {$version}: ok\n";
PHP

printf '\n== Mailpit connection ==\n'
curl --fail --silent --show-error http://mailpit:8025/livez >/dev/null
printf 'Mailpit HTTP: ok\n'
