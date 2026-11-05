# GOFAMINT Students’ Fellowship Platform

The GSF National platform supports the work of the GOFAMINT Students’ Fellowship across its national secretariat, fields, zones and campus chapters. It brings the public fellowship website and the internal management tools together in one Laravel application.

## What the platform supports

- Public campus and people directories, including campus profiles, fellowship contacts, leaders and meeting details.
- Stakeholder workspaces for monthly chapter reports, report review, appraisal and administration.
- Member and alumni records scoped to their campus fellowships.
- GSF news, campus stories, programs and events, with event flyers and guest minister details.
- Conference registration and management, education awards, and supporting administrative workflows.

The application is built with Laravel 10 and PHP 8.1 or later. It uses MySQL, Blade, and Vite for frontend asset builds.

## Local development

### Requirements

- PHP 8.1 or later with the extensions required by Laravel and the installed Composer packages.
- Composer 2.
- MySQL or a compatible database.
- Node.js and npm for compiling frontend assets.

### Setup

1. Install PHP dependencies:

   ```sh
   composer install
   ```

2. Create a local environment file and application key:

   ```sh
   cp .env.example .env
   php artisan key:generate
   ```

3. Set `APP_URL`, database credentials, and any required mail or upload configuration in `.env`.

4. Create the configured database, then apply the migrations:

   ```sh
   php artisan migrate
   ```

5. Install and build frontend assets:

   ```sh
   npm install
   npm run build
   ```

6. Start the local server:

   ```sh
   php artisan serve
   ```

Some application workflows depend on fellowship records and permissions already being present in the database. Use the project’s approved database and seeder for your local environment; avoid destructive commands such as `migrate:fresh` against shared or production data.

## Blog and event module

The newsroom and programs calendar are part of the main application. Public pages are available at `/news` and `/programs-events`; administrators and stakeholders have separate workspaces for creating and managing their content. See [the module notes](app/Modules/Blog/README.md) for its routes and permission capabilities.

## Deployment

The development deployment workflow is defined in [`.github/workflows/deploy.yml`](.github/workflows/deploy.yml). It updates the application, runs migrations, clears Laravel’s optimized caches, and deploys the public site assets. Deployment secrets and server access are managed by the repository’s GitHub Actions configuration.

## Contributing

Keep changes aligned with the fellowship’s public website and administrative workflows. Before opening a pull request, describe the affected user roles and pages, and include screenshots for substantial interface changes.
