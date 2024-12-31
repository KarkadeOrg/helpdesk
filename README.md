# Karkade Helpdesk

## Курс по Docker

1. Клонируемся
2. Заходим `cd production-docker`
3. Копируем `cp .env.example .env`
4. Запускаемся через `docker compose up --build`
5. Стучимся на http://localhost:8080/
6. Видим главную страницу, на которой нет функционала, но она берёт данные из БД, иначе будет
   `500 Internal Server Error` (можно рубануть сервис `postgres` и убедиться)
7. Заодно можно глянуть `production-docker/log`, где будет лог с ошибкой подключения к БД (это через `volumes`)

### Куда глянуть
- `/Dockerfile`
- `/.dockerignore`
- `/server.sh`
- `/production-docker/docker-compose.yaml`
