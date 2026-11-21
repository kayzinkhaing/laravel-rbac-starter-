# Laravel User Management & RBAC

A reusable Laravel 11 backend starter with session-based web authentication, database-backed roles and permissions, policies, and a Service–Repository–Interface structure.

## Requirements

- PHP 8.2+
- Composer
- A supported Laravel database (SQLite is the default)

## Setup

```bash
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate
```

Configure the database, mail, session, and cache settings in your local `.env` before deployment. Keep `.env` out of source control.

## Docker

Docker Compose reads container settings and credentials from the project-root `.env`. The Compose file requires the existing `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`, and `DB_ROOT_PASSWORD` values, plus `DOCKER_DB_CONNECTION`, `DOCKER_DB_HOST`, `DOCKER_DB_PORT`, `APP_HTTP_PORT`, `MYSQL_PUBLISHED_PORT`, and `PHPMYADMIN_HTTP_PORT`.

When running Compose from the `docker` directory, its local `.env` entry must be a hard link to the project-root `.env`. On Windows PowerShell, create it once from the project root if it is missing:

```powershell
New-Item -ItemType HardLink -Path .\docker\.env -Target (Resolve-Path .env).Path
```

Then run:

```powershell
Set-Location .\docker
docker compose -p based_code up -d --build
```

The database uses the persistent `based_code_db_data` volume. The Docker setup does not run Laravel migrations automatically.

## Web authentication

The web guard is the default guard. The root route renders the public homepage. After login, registration, email verification, or two-factor verification, ordinary users land on the homepage while users with an administrative permission land in the dashboard. The dashboard also requires a verified email and the `access-admin-dashboard` ability.

Login, registration, logout, email verification, password reset, password confirmation, and profile routes are defined in `routes/web.php` and `routes/auth.php`. Profile viewing, updating, and deletion are authorized by `UserPolicy` and limited to the authenticated user's own profile.

## Roles and permissions

Authorization uses Spatie Laravel Permission v6. Roles and permissions are database records with the `web` guard. Ordinary accounts use the public home and profile pages. Users with `manage users`, `manage roles`, or `manage permissions` can access the administration workspace, and each management route remains protected by backend authorization.

Users may have multiple roles, roles may have multiple permissions, and permission checks work with Laravel's `can` middleware, policies, Blade `@can`, and Spatie's `role`, `permission`, and `role_or_permission` middleware aliases. Permission changes are invalidated by Spatie's permission registrar; the application does not maintain a second permission cache.

Role assignment cannot grant permissions the acting administrator does not already hold. Administrators cannot edit or delete roles containing permissions outside their authority, and only a user with all three administrative permissions can change existing administrative permission names.

### Local sample accounts

`DatabaseSeeder` creates sample RBAC data only when `APP_ENV` is `local` or `testing`. Existing users, passwords, roles, permissions, and assignments are left unchanged; sample permissions and roles are linked only when the seeder creates those roles, and sample roles are assigned only to newly created sample accounts. It does nothing in production or other environments.

Run `php artisan db:seed` against a local or isolated test database to create the sample data. Do not point a local seeder at a production database.

All sample accounts use the password `LocalOnly!2026`:

| Account | Role | Email |
| --- | --- | --- |
| Full administrator | `super-admin` | `admin@example.test` |
| User administrator | `user-administrator` | `users@example.test` |
| Role administrator | `role-administrator` | `roles@example.test` |
| Permission administrator | `permission-administrator` | `permissions@example.test` |
| Standard user | none | `member@example.test` |

These credentials are for local testing only. Change or remove them before using a database outside local development.

## Database migration safety

The RBAC migration upgrades the existing `roles` and `permissions` tables in place, adds the `web` guard, copies legacy user-role and role-permission assignments into Spatie's pivot tables, and then removes the obsolete pivot tables. It does not modify user records.

The legacy domain cleanup migration drops the former domain tables and is intentionally irreversible. Back up the database before applying migrations to an existing installation. Never use `migrate:fresh` against a database containing data you need to keep.

## Architecture

- Controllers handle HTTP input, authorization, and responses.
- Services contain use-case logic.
- Repositories provide data access behind application contracts where appropriate.
- Eloquent models define persistence and relationships.
- Policies handle resource ownership and contextual authorization.

## Tests

```bash
php artisan test
```

Feature tests cover web authentication, protected routes, role/permission assignment, permission inheritance, denied access, and permission-cache invalidation.
