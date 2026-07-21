# Package Registry

This file tracks reusable Laravel packages that were extracted from this application or should be tested against it before release.

## davsong/laravel-error-logger

- Package repository: `https://github.com/davsong01/-laravel-error-logger.git`
- Package source folder to edit when updating: `/private/tmp/laravel-error-logger`
- Local package path during development: `/private/tmp/laravel-error-logger`
- Parent application: GSF Directory
- Parent application path: `/Applications/MAMP/htdocs/gsf`
- Parent application repository: `https://github.com/davsong01/gsf.git`
- Status: Extracted from GSF error logging implementation

### Test Checklist

- Install/update the package in GSF.
- Run package migrations.
- Confirm database error log routes load.
- Confirm error file routes load.
- Confirm `Log::error()` writes to the `system_logs` table.
- Confirm GSF admin menu links and permissions still point to the correct package routes.
