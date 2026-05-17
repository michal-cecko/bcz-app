# BCZ Club

SaaS platform for sports clubs — athlete management, event registrations, memberships, online payments and a Filament-based admin.

> Built for [Boj-cross Žilina](https://bcz.club), with multi-tenant architecture so other clubs can be onboarded.

## What it does

- **Athletes & disciplines** — categories, profiles, judge assignments
- **Events** — public sign-up flow, paid/free events, capacity limits, waitlists
- **Memberships** — seasonal billing periods, member-only pricing, role-based perks
- **Payments** — GoPay integration (SK card processor), invoices, payment history
- **CMS** — pages, banners, FAQ, inquiries inbox, menu builder, media library
- **Public site + admin** — single Laravel app serves both `/` (Blade pages) and `/admin` (Filament panel)

## Stack

| Layer | Tech |
|---|---|
| Backend | **Laravel 12** on PHP 8.5, **Octane** + RoadRunner |
| Admin | **Filament v5** + Shield (RBAC), Apex Charts, Google Maps, Mason design system, RicherEditor |
| Media | Spatie MediaLibrary → AWS S3 |
| Payments | GoPay SDK |
| Errors | Sentry |
| Tests | PHPUnit + ParaTest |
| Code style | Laravel Pint |
| Build | Vite (Tailwind 4) |
| Deploy | Docker → Dokploy |

## Local dev

```bash
cp .env.example .env
docker compose up -d                  # Postgres + Redis + the app via Sail
vendor/bin/sail composer install
vendor/bin/sail npm install
vendor/bin/sail artisan key:generate
vendor/bin/sail artisan migrate --seed
vendor/bin/sail npm run dev
```

App at `http://localhost`, admin at `/admin`.

### Composer auth

This project pulls Filament Pro packages from `packages.filamentphp.com` and needs a Filament Pro license. Locally, create an `auth.json` (gitignored):

```json
{ "http-basic": { "packages.filamentphp.com": { "username": "<email>", "password": "<token>" } } }
```

CI and Dokploy receive the same value via a `COMPOSER_AUTH` env var (build arg).

## CI

GitHub Actions `ci.yml` runs on push to `main`:

1. **test** — Pint style check + PHPUnit feature tests against a real Postgres service
2. **deploy-worker** — re-deploys the queue worker on Dokploy via `application.deploy` API
3. **notify** — Telegram bot pings on failure

Secrets required: `COMPOSER_AUTH`, `DOKPLOY_API_KEY`, `DOKPLOY_URL`, `TELEGRAM_BOT_TOKEN`, `TELEGRAM_CHAT_ID`.

## Deploy

Two-stage `Dockerfile` (build stage installs deps + builds assets; runtime stage is a lean PHP 8.4 alpine with Octane). Dokploy pulls on git push.

Worker, scheduler and web are separate services in `docker-compose.prod.yml`.

## License

[MIT](LICENSE) © Michal Čečko
