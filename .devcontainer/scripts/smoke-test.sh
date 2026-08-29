#!/usr/bin/env bash
set -euo pipefail

printf '== Toolchain ==\n'
php --version | head -n 1
composer --version
node --version
npm --version
git --version
codex --version

printf '\n== Required PHP extensions ==\n'
for extension in bcmath curl mbstring pcntl pdo_mysql; do
    php -r "exit(extension_loaded('${extension}') ? 0 : 1);"
    printf '%s: ok\n' "${extension}"
done

php -r "exit(extension_loaded('Zend OPcache') ? 0 : 1);"
printf 'Zend OPcache: ok\n'

printf '\n== MySQL connection ==\n'
php <<'PHP'
<?php

$dsn = sprintf(
    'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
    getenv('DB_HOST'),
    getenv('DB_PORT'),
    getenv('DB_DATABASE'),
);

$pdo = new PDO($dsn, getenv('DB_USERNAME'), getenv('DB_PASSWORD'), [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

echo 'MySQL ', $pdo->query('SELECT VERSION()')->fetchColumn(), ": ok\n";
PHP

printf '\n== Mailpit connection ==\n'
curl --fail --silent --show-error http://mailpit:8025/livez >/dev/null
printf 'Mailpit HTTP: ok\n'
