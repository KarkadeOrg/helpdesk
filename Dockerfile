FROM ghcr.io/roadrunner-server/roadrunner:2024 AS roadrunner
FROM php:8.4-alpine AS server

# Добавим в образ установщик расширений PHP, который устанавливает все необходимые для сборки расширений
# системные библиотеки самостоятельно, а потом ещё и удаляет их
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

# Установим менеджер зависимостей (Composer) и минимальный набор расширений
# необходимый для Symfony и среде исполнения PHP - Roadrunner
RUN install-php-extensions @composer-2 opcache zip intl sockets protobuf pdo_pgsql redis

# Скопируем из слоя с Roadrunner сам бинарник
COPY --from=roadrunner /usr/bin/rr /usr/local/bin/rr

# Сервер будет работать на порте 8080, поэтому явно пропишем EXPOSE
EXPOSE 8080/tcp

# Создадим пользователя и группу из под которых будут выполняться команды и само приложение
RUN addgroup -S app && adduser -S app -G app
# Создадим рабочую директорию и назначим правильные права
RUN mkdir -p /app && chown app:app /app && chmod 700 /app
WORKDIR /app
ENV APP_ENV="prod"
ENV APP_DEBUG="0"

FROM node:22-alpine AS node_build
# Переключимся в контекст сборки JS
# Скопируем package.json, который декларирует зависимости для NPM
WORKDIR /app
COPY ./package*.json .
# Да, мы тут устанавливаемся полностью, потому что тут только сборка статики
# и все зависимости уйдут в любом случае
RUN npm ci
COPY webpack.config.js ./
COPY assets/ ./assets
RUN mkdir -p public/build
RUN npm run build
RUN rm -rf node_modules/ assets/

FROM server
# Скопируем готовый JS и другие ассеты из сборки
COPY --from=node_build --chown=app:app /app/public/build ./public/build

# Скопируем оставшееся приложение. Некоторые файлы не попадут из-за .dockerignore
COPY --chown=app:app . .
USER app
RUN mkdir -p var/cache var/log
# Устанавливаем зависимости
RUN composer install --optimize-autoloader --no-dev

CMD ["sh", "./server.sh"]
