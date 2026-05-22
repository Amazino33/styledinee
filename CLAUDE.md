# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**Styledinee** is a Laravel 13 admin panel for a Nigerian tailoring/dry-cleaning business. It uses Filament v5 for the entire UI — there are no custom controllers or Blade views beyond the default welcome page. All business logic lives in Filament Resources.

## Commands

```bash
# Full dev environment (PHP server + queue worker + Pail log viewer + Vite)
composer run dev

# Run all tests
composer run test
# or
php artisan test

# Run a single test
php artisan test --filter ExampleTest

# Fresh setup (install deps, migrate, build assets)
composer run setup

# Code formatting
vendor/bin/pint

# Build frontend assets
npm run build
```

## Database & Seeding

Uses SQLite by default (`database/database.sqlite`). Tests run against in-memory SQLite.

```bash
php artisan migrate
php artisan db:seed --class=RolesAndPermissionsSeeder
```

The `RolesAndPermissionsSeeder` must be run before logging into the admin panel — it creates the five roles (`admin`, `cashier`, `tailor`, `dry_cleaner`, `delivery`) and their permission sets.

## Architecture

### Admin Panel (Filament v5)

All CRUD lives under `app/Filament/Resources/`. The panel is accessible at `/admin`. There are no API routes or custom controllers.

Navigation groups:
- **Operations**: Orders, Order Status Logs
- (ungrouped): Services, Products, Gallery, Enquiries

### Role-Based Access

Roles and permissions are managed with `spatie/laravel-permission`. Access is enforced at the Filament Resource level via static methods (`canAccess`, `canCreate`, `canEdit`, `canDelete`) — not via middleware or gates elsewhere.

| Role | Key Capabilities |
|---|---|
| `admin` | Full access to everything |
| `cashier` | Create/edit orders, manage payments, respond to enquiries |
| `tailor` | View and update status on `tailoring`/`alteration` orders only |
| `dry_cleaner` | View and update status on `dry_cleaning` orders only |
| `delivery` | View and update status on `pickup_delivery` orders only |

Staff roles (`tailor`, `dry_cleaner`, `delivery`) see the order form in read-only mode for most fields; they can only change `status` and `delivery_date`.

### Core Domain Models

- **Order** — Central entity. Auto-generates a `STD-XXXXXXXX` reference on create. Types: `tailoring`, `dry_cleaning`, `alteration`, `pickup_delivery`. Statuses: `pending` → `confirmed` → `in_progress` → `ready` → `delivered` (or `cancelled`). Payment statuses: `unpaid`, `partial`, `paid`. Currency is NGN (₦).
- **OrderItem** — Line items on an order; can link to a `Product` or `Service`.
- **OrderStatusLog** — Immutable audit trail; every status change records `changed_by` (user FK) and optional notes. Created inline from `OrderResource` via the "Update Status" table action.
- **Service** — Offered services (tailoring, dry cleaning, etc.). Auto-generates `slug` from `name`.
- **Product** — Physical products for sale. Auto-generates `slug` from `name`.
- **Gallery** — Portfolio images, categorized and sortable.
- **Enquiry** — Customer contact form submissions with `status` tracking.

### Frontend

Tailwind CSS v4 via `@tailwindcss/vite`. Entry points: `resources/css/app.css` and `resources/js/app.js`. Vite config uses `laravel-vite-plugin`.
