# KinoKill v2 — сайт кинотеатра

Переписанная версия студенческого курсового проекта "кинотеатр" (см. `../KinoKill` — legacy-версия на Laravel 5.7/PHP 7.1, её `CLAUDE.md` документирует старый функционал и известные баги). Домен: каталог фильмов, залы и сеансы, онлайн-бронирование мест (с гостевой покупкой и последующей привязкой к аккаунту), роли/права персонала, админка.

Ключевые сущности: `Film`, `Genre`, `Hall`, `Seat`, `CinemaSession`, `Booking`, `BookingSeat`, `Customer`, `User` (+роли/права через spatie/laravel-permission).

## Стек

- PHP 8.4, Laravel ^13.17
- MySQL 8.4 (dev/prod через Docker), SQLite `:memory:` (тесты)
- Redis (`phpredis`) — `CACHE_STORE`/`SESSION_DRIVER=redis`, отдельный сервис `redis` в `compose.yaml`
- `spatie/laravel-permission` — роли и права вместо самодельного флага (см. "Роли и права" ниже)
- `spatie/laravel-data` — типизированные DTO между слоями и для Inertia-пропсов
- `dedoc/scramble` — автогенерация OpenAPI-документации по тонкому JSON API (`routes/api.php`, `/docs/api`)
- RabbitMQ (`vladimir-yuldashev/laravel-queue-rabbitmq`) — транспорт очередей (`QUEUE_CONNECTION=rabbitmq`), см. "Очереди и почта" ниже
- PHPUnit (не Pest), Laravel Pint
- Vue 3 (`<script setup>`, Composition API) + TypeScript, Inertia.js v2, Pinia, Tailwind CSS v4, Vite
- Vitest + Vue Test Utils — unit/component-тесты фронтенда
- Storybook (`@storybook/vue3-vite`) — витрина презентационных компонентов `entities`/`shared/ui`
- Laravel Breeze (`vue` стек, `--typescript --eslint`) как стартовый скаффолдинг auth-страниц

## Окружение — только Docker

**На машине разработки нет локального PHP/Composer/Node.** Всё выполняется в контейнерах через `docker compose` (Laravel Sail сгенерировал `compose.yaml`, но сам bash-скрипт `vendor/bin/sail` **не поддерживает Git Bash/MINGW на Windows** — используем `docker compose` напрямую, не сам wrapper).

Сервисы (`compose.yaml`): `laravel.test` (PHP 8.4 + Node, порт `8000`→80, Vite dev-сервер на `5173`), `mysql` (MySQL 8.4, порт `3306`), `redis` (порт `6379`, `CACHE_STORE`/`SESSION_DRIVER`), `mailpit` (SMTP-заглушка + веб-интерфейс на `8025`), `rabbitmq` (порт `5672`, management UI на `15672`), `queue` (тот же образ, что `laravel.test`, но вместо веб-сервера запускает `php artisan queue:work rabbitmq` — реальный воркер, без него письма останутся в очереди непрочитанными).

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
docker compose exec laravel.test npm run test:unit            # Vitest — unit/component-тесты фронта
docker compose exec laravel.test npm run storybook            # Storybook dev-сервер, порт 6006
docker compose exec laravel.test npm run build-storybook      # статическая сборка Storybook
docker compose logs queue -f                                  # логи воркера очереди (RabbitMQ)
docker compose exec rabbitmq rabbitmqctl list_queues name messages   # что скопилось в очереди
docker compose down                     # остановить контейнеры
```

Мутирующие команды (устанавливающие пакеты, генерирующие файлы) выполнять с `--user sail`, иначе созданные файлы окажутся `root`-владельцем и веб-процесс контейнера (который работает от `sail`) не сможет их перекомпилировать — см. `docker compose exec --user sail laravel.test ...`.

Приложение: http://localhost:8000. Mailpit UI (просмотр писем-подтверждений бронирования): http://localhost:8025. Если на машине уже установлен WSL2 с bash-окружением, `./vendor/bin/sail` тоже будет работать как более короткий алиас — команды выше эквивалентны `sail ...`.

## Структура директорий

```
app/
  Data/            # spatie/laravel-data DTO: Film/, Session/, Booking/, Customer/, Role/, Permission/
  Enums/           # BookingStatus, SeatType — нативные PHP enum
  Http/
    Requests/      # FormRequest — только валидация
    Controllers/   # тонкие контроллеры: Request -> Data -> Service -> Inertia::render
    Controllers/Admin/
    Controllers/Api/   # JSON-зеркало для dedoc/scramble, см. "JSON API и документация"
  Services/        # вся бизнес-логика, транзакции
  Repositories/
    Contracts/     # интерфейсы, биндятся в AppServiceProvider
    Eloquent/
  Models/          # + Role/Permission (extends spatie, добавляют label/description)
  Policies/
resources/js/                # Vue3+TS+Inertia, см. ниже — постепенно переводится на FSD
resources/views/app.blade.php  # единственный root-blade для Inertia
.storybook/                    # main.ts/preview.ts — Storybook конфиг (Tailwind + Link-стаб для Inertia)
database/
  migrations/, factories/, seeders/
routes/
  web.php   # Inertia-страницы
  api.php   # тонкий JSON-слой, см. "JSON API и документация"
tests/
  Feature/, Unit/     # PHPUnit
vitest.config.ts      # Vitest — *.test.ts рядом с компонентами в resources/js/
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
- Права — только через `spatie/laravel-permission` (`permission:` middleware, `@can`/Policy). Бизнес-логика и валидация никогда не сравнивают имя роли (`if ($role->name === 'admin')`) — только `->can('permission.name')`/`$role->hasPermissionTo(...)`. Роль — это данные (группа прав с русским `label`/`description`), а не ветвление в коде; см. "Роли и права" ниже.
- Ответы для Inertia — через `spatie/laravel-data` DTO (`FilmData::from($film)`), не `toArray()`/сырые массивы.
- Каждый новый Service-метод и каждый новый эндпоинт покрывается тестом (Definition of Done) — Feature-тест на сценарий, Unit-тест на Service при нетривиальной логике.
- Курсовая, не продакшн SaaS: реальной оплаты нет (`payment_method`: `cash`/`card_stub`), не переусложнять сверх того, что описано в плане миграции.

## Роли и права

`roles`/`permissions` (родные таблицы spatie) расширены колонками `label`/`description` (см. `App\Models\Role`/`App\Models\Permission`, прописаны в `config/permission.php`) — каждая роль и каждое право самодокументируются на русском для админки, а не только несут технический `name`-слаг. `RoleAndPermissionSeeder` — источник данных (какие роли существуют и что в них входит), а не бизнес-логика.

Приложение никогда не проверяет доступ по имени роли — только по правам (`$user->can('films.manage')`, `$role->hasPermissionTo(...)`). Пример — `UserRoleService::assignRole()` запрещает пользователю назначить самому себе роль, лишающую его права `users.manage-roles`, а не роль с именем не `admin`; это работает для любой будущей роли, а не только для трёх сидируемых по умолчанию (`admin`/`manager`/`customer`). Дефолтная роль саморегистрации — `config('roles.default_customer_role')`, а не литерал в `RegistrationService`.

## JSON API и документация (dedoc/scramble)

`routes/api.php` — тонкий read-only (+создание брони) JSON-слой поверх тех же `Service`/`Data`-классов, что и Inertia-контроллеры в `routes/web.php` (`Api\FilmController`, `Api\SessionController`, `Api\BookingController`). Он существует прежде всего как повод для OpenAPI-документации: Inertia-страницы — не осмысленная цель для OpenAPI, а этот JSON-слой — да. Схема генерируется автоматически dedoc/scramble из типов `FormRequest`/`Data`-классов и PHPDoc — без ручных аннотаций. UI-документация: http://localhost:8000/docs/api, сырой JSON: `/docs/api.json`.

Если сложную цепочку `Collection`-методов (`groupBy`→`map`→`values`→`all`) Scramble не может статически типизировать (типичный симптом — поле в схеме внезапно оказывается `string` вместо ожидаемого массива/объекта), проще всего переписать её на обычный `foreach` с явным `/** @var array<int, XData> $var */` перед накоплением результата — так и было решено для `Api\SessionController::index()`. После правки контроллера обязательно `php artisan scramble:clear` — документ кешируется.

## Очереди и почта (RabbitMQ)

`QUEUE_CONNECTION=rabbitmq` (см. `config/queue.php`, соединение `rabbitmq`; `.env`/`.env.example` — `RABBITMQ_*`). Job'ы кладутся в RabbitMQ (`compose.yaml`: сервис `rabbitmq`), разбирает их отдельный контейнер `queue` (`php artisan queue:work rabbitmq --tries=3 --backoff=5`) — это не `laravel.test`, у него своя команда запуска в том же образе. Если после `docker compose up -d` письма не доходят — первым делом проверить, что контейнер `queue` вообще поднялся (`docker compose ps`), а не только `rabbitmq`.

Единственный пример пока — `App\Mail\BookingConfirmationMail` (`ShouldQueue`), письмо-чек с местами/ценой/залом, дублирующее то, что видно на `Booking/Confirmation.vue`. Отправляется из `BookingService::createBooking()` **после** `DB::transaction()` (не изнутри неё) — иначе воркер теоретически может забрать джобу раньше коммита транзакции. Штатные Auth-уведомления (подтверждение email, сброс пароля) — уже `ShouldQueue` из коробки Breeze, поэтому автоматически поехали через RabbitMQ вместе со сменой `QUEUE_CONNECTION`, без правок кода.

В тестах `QUEUE_CONNECTION=sync` и `MAIL_MAILER=array` (см. `phpunit.xml`) — джобы выполняются синхронно, RabbitMQ для тестов не нужен; для проверки конкретно факта постановки в очередь — `Mail::fake()` + `Mail::assertQueued(...)` (см. `CreateBookingTest`).

## Локализация (ru/en)

`APP_LOCALE=ru` по умолчанию, `APP_FALLBACK_LOCALE=en`. `lang/ru/{validation,auth,passwords,pagination}.php` и `lang/en/*` (сгенерированы `php artisan lang:publish`) — переводы валидации и стандартных auth-сообщений Breeze; `lang/ru/validation.php` также содержит `attributes` с человеко-читаемыми именами полей (`guest_email` → «email» и т.д.), чтобы сообщения об ошибках не показывали технические имена полей. Работает автоматически для *любого* `FormRequest` в приложении — отдельно переводить каждую форму не нужно.

Текущая локаль хранится в сессии (`SetLocale` middleware, `app/Http/Middleware/SetLocale.php`, добавлен в `web`-группу перед `HandleInertiaRequests`) и переключается через `GET /locale/{ru|en}` (`LocaleController`). `HandleInertiaRequests::share()` прокидывает `locale` во все Inertia-страницы.

На фронтенде через словари `t()`/`useI18n()` (`resources/js/shared/i18n/`) переключаются только Auth-страницы (`resources/js/pages/Auth/*`, `GuestLayout.vue`). Остальной сайт (каталог, бронирование, админка, `Profile`/`Dashboard`) написан с русским текстом напрямую в шаблонах — стилистически везде одна и та же тёмная тема (`resources/js/shared/ui/*` — `TextInput`/`InputLabel`/`PrimaryButton`/`SecondaryButton`/`DangerButton`/`Checkbox`/`Modal`/`InputError` — все под `bg-neutral-950`/`red-600`-акцент, никаких светлых Breeze-остатков), но не переведён на en; если понадобится — тот же `useI18n()` расширяется новыми ключами. `AuthenticatedLayout.vue` — тонкая обёртка над `MainLayout`/`SiteNavbar` (как и `AdminLayout.vue`), отдельной шапки/логотипа не держит.

## Известные решения по домену (см. полный план миграции)

- **Customer vs User**: бронь всегда привязана к `Customer` (email/имя/телефон, без пароля — покупатель), а не напрямую к `User`. Гостевая покупка создаёт/находит `Customer` по email. При регистрации/входе с тем же email — `Customer` привязывается к новому `User` (`customers.user_id`), история бронирований становится видна в личном кабинете. См. `CustomerService::resolveForGuestCheckout()` / логику связывания в auth-потоке.
- Цена бронирования всегда считается на сервере (`BookingService`), клиентские данные о цене не принимаются.
- Конкурентное бронирование одного места блокируется транзакцией + `lockForUpdate()` в `SeatAvailabilityService` (тест — `ConcurrentBookingTest`).
- Полный список закрываемых legacy-багов — чек-лист в исходном плане миграции (баги: сломанные роуты, эскалация привилегий через `/regAdmin`, отсутствие FK, `genres`-миграция и т.д.) — не повторять их здесь.
