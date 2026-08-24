# KinoKill v2 — сайт кинотеатра

Переписанная версия студенческого курсового проекта "кинотеатр" (см. `../KinoKill` — legacy-версия на Laravel 5.7/PHP 7.1, её `CLAUDE.md` документирует старый функционал и известные баги). Домен: каталог фильмов, залы и сеансы, онлайн-бронирование мест (с гостевой покупкой и последующей привязкой к аккаунту), роли/права персонала, админка.

Ключевые сущности: `Film`, `Genre`, `Hall`, `Seat`, `CinemaSession`, `Booking`, `BookingSeat`, `Customer`, `User` (+роли/права через spatie/laravel-permission).

## Стек

- PHP 8.4, Laravel ^13.17
- MySQL 8.4 (dev/prod через Docker), SQLite `:memory:` (тесты)
- `spatie/laravel-permission` — роли и права вместо самодельного флага
- `spatie/laravel-data` — типизированные DTO между слоями и для Inertia-пропсов
- PHPUnit (не Pest), Laravel Pint
- Vue 3 (`<script setup>`, Composition API) + TypeScript, Inertia.js v2, Pinia, Tailwind CSS v4, Vite
- Laravel Breeze (`vue` стек, `--typescript --eslint`) как стартовый скаффолдинг auth-страниц

## Окружение — только Docker

**На машине разработки нет локального PHP/Composer/Node.** Всё выполняется в контейнерах через `docker compose` (Laravel Sail сгенерировал `compose.yaml`, но сам bash-скрипт `vendor/bin/sail` **не поддерживает Git Bash/MINGW на Windows** — используем `docker compose` напрямую, не сам wrapper).

Сервисы (`compose.yaml`): `laravel.test` (PHP 8.4 + Node, порт `8000`→80, Vite dev-сервер на `5173`), `mysql` (MySQL 8.4, порт `3306`), `mailpit` (SMTP-заглушка + веб-интерфейс на `8025`).

### Команды разработки

```bash
docker compose up -d                    # поднять контейнеры (первый раз — с --build)
docker compose exec laravel.test php artisan migrate --seed
docker compose exec laravel.test php artisan test
docker compose exec laravel.test php artisan test --filter=BookingTest
docker compose exec laravel.test ./vendor/bin/pint            # автофикс стиля
docker compose exec laravel.test ./vendor/bin/pint --test     # проверка без изменений
docker compose exec laravel.test composer require <package>
docker compose exec laravel.test npm install
docker compose exec laravel.test npm run dev                  # Vite dev-сервер (HMR)
docker compose exec laravel.test npm run build                # прод-сборка (tsc + vite build)
docker compose down                     # остановить контейнеры
```

Приложение: http://localhost:8000. Mailpit UI (просмотр писем-подтверждений бронирования): http://localhost:8025. Если на машине уже установлен WSL2 с bash-окружением, `./vendor/bin/sail` тоже будет работать как более короткий алиас — команды выше эквивалентны `sail ...`.

## Структура директорий

```
app/
  Data/            # spatie/laravel-data DTO: Film/, Session/, Booking/, Customer/
  Enums/           # BookingStatus, SeatType — нативные PHP enum
  Http/
    Requests/      # FormRequest — только валидация
    Controllers/   # тонкие контроллеры: Request -> Data -> Service -> Inertia::render
    Controllers/Admin/
  Services/        # вся бизнес-логика, транзакции
  Repositories/
    Contracts/     # интерфейсы, биндятся в AppServiceProvider
    Eloquent/
  Models/
  Policies/
resources/js/                # Vue3+TS+Inertia, см. ниже — постепенно переводится на FSD
resources/views/app.blade.php  # единственный root-blade для Inertia
database/
  migrations/, factories/, seeders/
tests/
  Feature/, Unit/
```

Целевая FSD-структура фронтенда (см. план миграции — вводится по мере реализации страниц, Breeze-скаффолдинг с `Pages/`, `Components/`, `Layouts/` в PascalCase — стартовая точка, из которой постранично переезжаем в `app/pages/widgets/features/entities/shared`):
```
resources/js/
  app/            main.ts, layouts/
  pages/          соответствуют Inertia::render('X/Y')
  widgets/        film-catalog-grid, seat-map, site-navbar, admin-sidebar, ...
  features/       select-seat (Pinia store), create-booking, film-crud, ...
  entities/       film, session, seat, booking, customer, user
  shared/         ui, api, lib, config
```
Правило импортов: только "вниз" (`pages → widgets → features → entities → shared`), публичный API слайса — `index.ts`.

## Конвенции кода

- БД — `snake_case`, явные `foreignId()->constrained()` с политикой удаления (`cascade`/`restrict`/`nullOnDelete`), никаких нестандартных первичных ключей.
- Контроллер не содержит бизнес-логики: `FormRequest` (валидация) → `Data::from()` (DTO) → один вызов метода `Service` → `Inertia::render()`/`redirect()`.
- Один публичный метод `Service` = один юзкейс (`BookingService::createBooking()`, не generic CRUD-заглушки).
- `Repository` — только доступ к данным (Eloquent/DB), без бизнес-правил; интерфейс в `Contracts/`, реализация в `Eloquent/`.
- Права — только через `spatie/laravel-permission` (`role:`/`permission:` middleware, `@can`/Policy), никаких `if ($user->role == 1)`.
- Ответы для Inertia — через `spatie/laravel-data` DTO (`FilmData::from($film)`), не `toArray()`/сырые массивы.
- Каждый новый Service-метод и каждый новый эндпоинт покрывается тестом (Definition of Done) — Feature-тест на сценарий, Unit-тест на Service при нетривиальной логике.
- Курсовая, не продакшн SaaS: реальной оплаты нет (`payment_method`: `cash`/`card_stub`), не переусложнять сверх того, что описано в плане миграции.

## Известные решения по домену (см. полный план миграции)

- **Customer vs User**: бронь всегда привязана к `Customer` (email/имя/телефон, без пароля — покупатель), а не напрямую к `User`. Гостевая покупка создаёт/находит `Customer` по email. При регистрации/входе с тем же email — `Customer` привязывается к новому `User` (`customers.user_id`), история бронирований становится видна в личном кабинете. См. `CustomerService::resolveForGuestCheckout()` / логику связывания в auth-потоке.
- Цена бронирования всегда считается на сервере (`BookingService`), клиентские данные о цене не принимаются.
- Конкурентное бронирование одного места блокируется транзакцией + `lockForUpdate()` в `SeatAvailabilityService` (тест — `ConcurrentBookingTest`).
- Полный список закрываемых legacy-багов — чек-лист в исходном плане миграции (баги: сломанные роуты, эскалация привилегий через `/regAdmin`, отсутствие FK, `genres`-миграция и т.д.) — не повторять их здесь.
