# Myanmar address feature setup

## What is included

- Relational country, state/region, district, township, ward/village-tract, street and address tables.
- Eloquent models.
- An idempotent seeder that imports the official bilingual administrative hierarchy.
- Authenticated Blade address CRUD pages for the signed-in user.
- Cascading selectors use routes in `routes/web.php` (not `routes/api.php`).

## Real data source

Open Admin Data — Myanmar Administrative Divisions, CC BY 4.0:
https://github.com/open-admin-data/myanmar-administrative-divisions

The source currently documents 15 states/regions, 77 districts, 491 townships and 17,043 wards/village tracts. It does not provide a complete street-name gazetteer or individual building addresses. Street names are therefore user-maintained and are not fabricated by this seeder.

## Download data

From the Laravel project root, run in Git Bash:

```bash
mkdir -p database/data
curl -L --fail --retry 3 \
  https://api.openadmindata.org/api/v1/countries/mm.json \
  -o database/data/myanmar-administrative-divisions.json
```

The seeder validates all four expected record counts before writing any rows. The JSON file is large; keep it out of source control if your repository does not want bundled reference datasets.

## Required manual integration

1. Add `public function addresses()` to `app/Models/User.php`:

```php
public function addresses()
{
    return $this->morphMany(\App\Models\Address::class, 'addressable');
}
```

2. Add these imports to `routes/web.php`:

```php
use App\Http\Controllers\Web\AddressController;
use App\Http\Controllers\Web\AddressLocationController;
```

3. Add these routes near the end of `routes/web.php`, before `require __DIR__.'/auth.php';`:

```php
Route::middleware('auth')->group(function () {
    Route::resource('addresses', AddressController::class)
        ->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

    Route::prefix('address-locations')->name('address-locations.')->group(function () {
        Route::get('/countries/{country}/states', [AddressLocationController::class, 'states'])->name('states');
        Route::get('/states/{state}/districts', [AddressLocationController::class, 'districts'])->name('districts');
        Route::get('/districts/{district}/townships', [AddressLocationController::class, 'townships'])->name('townships');
        Route::get('/townships/{township}/wards', [AddressLocationController::class, 'wards'])->name('wards');
    });
});
```

4. Add the `addresses()` relation to `User` as shown above.

## Run

```bash
php artisan migrate
php artisan db:seed --class=MyanmarAdministrativeDivisionSeeder
php artisan route:list --name=addresses
php artisan test
```

Do not run `migrate:fresh` on an existing database. Review and back up the database before applying migrations.

## Notes before production

- This package intentionally does not auto-register the seeder in `DatabaseSeeder`.
- Address ownership is polymorphic and the supplied controller scopes all CRUD operations to the authenticated user's own addresses.
- Country/state/district/township/ward relationships are checked before saving.
- This is a starter implementation; if addresses should belong to `ClientProfile` or `Organization` instead of `User`, change the morph owner in the controller and expose the relation on that model.
