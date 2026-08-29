FROM node:24.20.0-bookworm-slim AS node-runtime
FROM composer:2.10.2 AS composer-runtime

FROM php:8.5.9-cli-bookworm

ARG CODEX_VERSION=0.151.0
ARG DEBIAN_MIRROR=https://deb.debian.org
ARG USERNAME=vscode
ARG USER_UID=1000
ARG USER_GID=1000

ENV DEBIAN_FRONTEND=noninteractive \
    COMPOSER_HOME=/home/vscode/.composer \
    NPM_CONFIG_CACHE=/home/vscode/.npm \
    PATH=/home/vscode/.local/bin:${PATH}

RUN sed -i "s|http://deb.debian.org|${DEBIAN_MIRROR}|g; s|https://deb.debian.org|${DEBIAN_MIRROR}|g" \
        /etc/apt/sources.list.d/debian.sources \
    && apt-get -o Acquire::Retries=5 -o Acquire::https::Timeout=30 update \
    && apt-get install -y --no-install-recommends \
        git \
        unzip \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install -j"$(nproc)" \
        bcmath \
        pcntl \
        pdo_mysql

COPY --from=composer-runtime /usr/bin/composer /usr/local/bin/composer
COPY --from=node-runtime /usr/local/bin/node /usr/local/bin/node
COPY --from=node-runtime /usr/local/lib/node_modules /usr/local/lib/node_modules

RUN ln -s /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm \
    && ln -s /usr/local/lib/node_modules/npm/bin/npx-cli.js /usr/local/bin/npx \
    && groupadd --gid "${USER_GID}" "${USERNAME}" \
    && useradd --uid "${USER_UID}" --gid "${USER_GID}" -m -s /bin/bash "${USERNAME}" \
    && npm install --global --no-audit --no-fund "@openai/codex@${CODEX_VERSION}" \
    && mkdir -p \
        /workspaces/nihonreach \
        /home/vscode/.composer/cache \
        /home/vscode/.npm \
        /home/vscode/.codex \
    && chown -R "${USERNAME}:${USERNAME}" \
        /workspaces/nihonreach \
        /home/vscode

WORKDIR /workspaces/nihonreach

USER vscode

RUN git config --global --add safe.directory /workspaces/nihonreach

CMD ["sleep", "infinity"]
