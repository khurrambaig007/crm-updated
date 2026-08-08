# AGENTS.md

## Project Overview

Laravel 13 (PHP 8.3) CRM application. Uses Laravel Tinker, Tailwind CSS 4, and Vite for the frontend.

## Environment

- OS: Windows (win32), shell: PowerShell 5.1
- PHP is not on PATH by default — use full path or Laragon's PHP (`C:\laragon\bin\php\php-*\php.exe`) when running PHP/artisan commands.

## Commands

- **Run tests:** `composer test` (clears config, then runs `php artisan test`)
- **Lint / fix style:** `composer exec pint` (Laravel Pint, code style follows Laravel presets)
- **Build frontend:** `npm run build`
- **Frontend dev server:** `npm run dev`
- **Full dev stack:** `composer dev` (serves app, queue listener, logs via Pail, and Vite concurrently)
- **Setup from scratch:** `composer setup`
- **Add packages:** `composer require <pkg>` / `composer require --dev <pkg>` (sort-packages is enabled)

## Code Conventions

- Follow Laravel 13 conventions and PSR-4 autoloading (`App\` → `app/`, `Tests\` → `tests/`).
- Match existing code style; run Pint before finishing changes.
- Never add code comments unless asked.

## Testing

- Do not create any test files. Verify changes by running the existing suite with `composer test` or `php artisan test` when needed.

## Notes

- `post-update-cmd` republishes Laravel assets with `--force` on composer update.
- Do not commit unless explicitly asked.
- **Amount/Currency fields:** Always use `numeric` validation and `html()->number()` input type for amount and currency fields to ensure only numbers are allowed.
