# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project

**MisHábitos** — a daily habit tracker built with Laravel 10 + SQLite. No authentication; all habits are shared/global. UI is entirely in Spanish.

## Commands

```bash
# Start dev server
php artisan serve

# Run all tests
php artisan test

# Run a single test file
php artisan test tests/Feature/ExampleTest.php

# Run tests with a filter
php artisan test --filter=HabitTest

# Run migrations (fresh)
php artisan migrate:fresh

# Code style (Laravel Pint)
./vendor/bin/pint

# Tinker REPL
php artisan tinker
```

> **Windows:** SQLite file must exist before migrating. Create it with `New-Item database/database.sqlite` if not present.

## Architecture

### Data model

Two tables drive the entire app:

- **`habits`** — `name`, `description`, `color` (hex), `icon` (emoji), `active` (bool)
- **`habit_completions`** — `habit_id`, `completed_date` (date); unique constraint on `(habit_id, completed_date)` prevents duplicates

### Streak and stats logic

All business logic lives in `app/Models/Habit.php`:

- `getCurrentStreak()` — counts consecutive days back from today; accepts today *or* yesterday as the anchor so the streak survives if you haven't checked in yet today
- `getLongestStreak()` — full history scan, O(n) over sorted dates
- `getCompletionRateLast30Days()` — returns an integer percentage (0–100) based on a fixed 30-day window

### Controller

`app/Http/Controllers/HabitController.php` handles all routes. The `toggle` action (`POST /habits/{habit}/toggle`) is dual-mode: returns JSON when the request sends `Accept: application/json`, otherwise redirects — this powers the no-reload toggle on the index page.

### Frontend

No build step. Tailwind CSS is loaded from CDN (`cdn.tailwindcss.com`) with an inline config block in `layouts/app.blade.php`. JavaScript is vanilla (Fetch API), inlined per view via `@push('scripts')`. The toggle button on the index page updates the DOM directly from the JSON response without a page reload.

### Routing

```
GET  /                        → redirect to /habits
GET  /habits                  → index (dashboard)
GET  /habits/create           → create form
POST /habits                  → store
GET  /habits/{habit}          → show (30-day calendar)
GET  /habits/{habit}/edit     → edit form
PUT  /habits/{habit}          → update
DELETE /habits/{habit}        → destroy
POST /habits/{habit}/toggle   → toggle completion (JSON or redirect)
```

`Route::resource` registers all standard routes except `show`; `show` is registered separately so it appears after the resource block and doesn't conflict.

### Testing

`phpunit.xml` uses `APP_ENV=testing` but the SQLite in-memory database is commented out — tests currently use the same file-based SQLite as the app. Enable the commented `DB_CONNECTION`/`DB_DATABASE` lines in `phpunit.xml` to isolate tests.

## Git workflow

After every meaningful change (new feature, bug fix, refactor, config update), commit and push to GitHub:

```bash
git add <specific files>
git commit -m "type: short description in Spanish or English"
git push origin master
```

Commit message conventions:
- `feat:` — nueva funcionalidad
- `fix:` — corrección de bug
- `refactor:` — cambio interno sin nueva funcionalidad
- `style:` — cambios de UI/CSS sin lógica
- `docs:` — documentación
- `test:` — tests
- `chore:` — migraciones, dependencias, configuración

Stage specific files rather than `git add .` to avoid committing unintended files (e.g. `database/database.sqlite`, `.env`).
