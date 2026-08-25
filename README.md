# KinoKill

Сайт кинотеатра: каталог фильмов, залы и сеансы, онлайн-бронирование мест с гостевой покупкой и привязкой к аккаунту, роли и права персонала, админ-панель.

Переписанная версия студенческого курсового проекта на современном стеке (исходная legacy-версия — на Laravel 5.7/PHP 7.1).

## Стек

- PHP 8.4, Laravel ^13.17
- MySQL 8.4 (dev/prod), SQLite `:memory:` (тесты)
- `spatie/laravel-permission` — роли и права (с русскими `label`/`description` для админки)
- `spatie/laravel-data` — типизированные DTO между слоями и для Inertia-пропсов
- `dedoc/scramble` — автогенерация OpenAPI-документации по тонкому JSON API
- RabbitMQ (`vladimir-yuldashev/laravel-queue-rabbitmq`) — транспорт очередей, SMTP — доставка почты
- Vue 3 (`<script setup>`, Composition API) + TypeScript, Inertia.js v2, Pinia, Tailwind CSS v4, Vite
- PHPUnit, Vitest + Vue Test Utils, Storybook, Laravel Pint

## Функциональность

- Каталог фильмов, залов и сеансов
- Онлайн-бронирование мест с блокировкой от конкурентного бронирования одного места
- Гостевая покупка по email с последующей привязкой истории бронирований к аккаунту при регистрации/входе
- Письмо-подтверждение бронирования, отправляется в очереди через RabbitMQ
- Роли и права персонала (администратор/менеджер/покупатель из коробки, легко расширяются) через `spatie/laravel-permission`
- Админ-панель: управление фильмами, залами, сеансами, ролями пользователей
- Тонкий read-only JSON API поверх того же бизнес-слоя, с автогенерируемой OpenAPI-документацией

## Быстрый старт (Docker)

Всё выполняется в контейнерах — на машине разработки не нужны локальный PHP/Composer/Node.

```bash
cp .env.example .env
docker compose up -d --build
docker compose exec laravel.test php artisan key:generate
docker compose exec laravel.test php artisan migrate --seed
docker compose exec laravel.test npm install
docker compose exec laravel.test npm run build
```

Приложение: http://localhost:8000. Mailpit (просмотр писем-подтверждений бронирования): http://localhost:8025. Документация JSON API: http://localhost:8000/docs/api.

Windows: если на машине нет WSL2, используйте `docker compose exec ...` напрямую — bash-скрипт `vendor/bin/sail` не работает в Git Bash/MINGW.

### Основные команды разработки

```bash
docker compose exec laravel.test php artisan test              # PHPUnit
docker compose exec laravel.test ./vendor/bin/pint              # автофикс стиля PHP
docker compose exec laravel.test npm run dev                    # Vite dev-сервер (HMR)
docker compose exec laravel.test npm run test:unit              # Vitest
docker compose exec laravel.test npm run storybook              # Storybook, порт 6006
docker compose logs queue -f                                    # логи воркера очереди (RabbitMQ)
docker compose down                                              # остановить контейнеры
```

Подробные конвенции кода, структура директорий и описание архитектурных решений — в [`CLAUDE.md`](CLAUDE.md).

## Лицензия

Проект распространяется под лицензией MIT — см. [`LICENSE`](LICENSE).
