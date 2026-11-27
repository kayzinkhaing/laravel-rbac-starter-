<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TwoFactorController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\MessageController;
use App\Http\Controllers\Web\PaymentController;
use App\Http\Controllers\Web\PermissionController;
use App\Http\Controllers\Web\RoleController;
use App\Http\Controllers\Web\UserController;
use App\Support\AdminDashboardAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AddressController;
use App\Http\Controllers\Web\AddressLocationController;

Route::view('/', 'home')->name('home');

Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified', 'can:access-admin-dashboard'])
    ->name('dashboard');

Route::post('/active-role', function (Request $request) {
    $validated = $request->validate([
        'role_id' => ['required', 'integer'],
    ]);
    $role = AdminDashboardAccess::assignedRoles($request->user())
        ->firstWhere('id', (int) $validated['role_id']);

    abort_unless($role, 403);

    $request->session()->put('active_role_id', $role->getKey());

    return redirect()->route('role.dashboard', [
        'roleSlug' => AdminDashboardAccess::slugFor($role),
    ]);
})->middleware('auth')->name('active-role.update');

Route::middleware(['auth', 'resolve.role.dashboard', 'share.active.role'])
    ->prefix('{roleSlug}')
    ->where(['roleSlug' => '[A-Za-z0-9][A-Za-z0-9-]*'])
    ->name('role.')
    ->group(function () {
        Route::get('/dashboard', DashboardController::class)
            ->middleware('verified')
            ->name('dashboard');

        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::post('/profile/image', [ProfileController::class, 'updateImage'])->name('profile.image.update');
        Route::delete('/profile/image', [ProfileController::class, 'deleteImage'])->name('profile.image.delete');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        Route::prefix('two-factor')
            ->name('twofactor.')
            ->group(function () {
                Route::get('/setup', [TwoFactorController::class, 'setup'])->name('setup');
                Route::post('/enable', [TwoFactorController::class, 'enable'])->name('enable');
                Route::post('/disable', [TwoFactorController::class, 'disable'])->name('disable');
            });

        Route::prefix('users')
            ->name('users.')
            ->middleware('can:manage users')
            ->group(function () {
                Route::get('/', [UserController::class, 'index'])->name('index');
                Route::get('/create', [UserController::class, 'create'])->name('create');
                Route::post('/', [UserController::class, 'store'])->name('store');
                Route::get('/{user}', [UserController::class, 'show'])->name('show');
                Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
                Route::put('/{user}', [UserController::class, 'update'])->name('update');
                Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
            });

        Route::prefix('roles')
            ->name('roles.')
            ->middleware('can:manage roles')
            ->group(function () {
                Route::get('/', [RoleController::class, 'index'])->name('index');
                Route::get('/create', [RoleController::class, 'create'])->name('create');
                Route::post('/', [RoleController::class, 'store'])->name('store');
                Route::get('/{role}/edit', [RoleController::class, 'edit'])->name('edit');
                Route::put('/{role}', [RoleController::class, 'update'])->name('update');
                Route::delete('/{role}', [RoleController::class, 'destroy'])->name('destroy');
            });

        Route::prefix('permissions')
            ->name('permissions.')
            ->middleware('can:manage permissions')
            ->group(function () {
                Route::get('/', [PermissionController::class, 'index'])->name('index');
                Route::get('/create', [PermissionController::class, 'create'])->name('create');
                Route::post('/', [PermissionController::class, 'store'])->name('store');
                Route::get('/{permission}/edit', [PermissionController::class, 'edit'])->name('edit');
                Route::put('/{permission}', [PermissionController::class, 'update'])->name('update');
                Route::delete('/{permission}', [PermissionController::class, 'destroy'])->name('destroy');
            });
    });

Route::get('/profile/image/{user}', [ProfileController::class, 'image'])
    ->middleware('auth')
    ->name('profile.image');

Route::middleware(['auth', 'share.active.role'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/image', [ProfileController::class, 'updateImage'])->name('profile.image.update');
    Route::delete('/profile/image', [ProfileController::class, 'deleteImage'])->name('profile.image.delete');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

foreach ([
    'users' => 'manage users',
    'roles' => 'manage roles',
    'permissions' => 'manage permissions',
] as $resource => $permission) {
    Route::any("/{$resource}/{path?}", function (Request $request, ?string $path = null) use ($resource) {
        $role = AdminDashboardAccess::activeRole($request->user());
        abort_unless($role, 403);

        $suffix = $path === null || $path === ''
            ? ''
            : '/'.implode('/', array_map('rawurlencode', explode('/', trim($path, '/'))));
        $target = '/'.AdminDashboardAccess::slugFor($role)."/{$resource}{$suffix}";
        $query = $request->getQueryString();

        return redirect()->to($query ? "{$target}?{$query}" : $target, 307);
    })
        ->where('path', '.*')
        ->middleware(['auth', 'share.active.role', "can:{$permission}"])
        ->name("legacy.{$resource}");
}

Route::prefix('messages')
    ->name('messages.')
    ->middleware(['auth', 'can:manage messages'])
    ->group(function () {
        Route::get('/', [MessageController::class, 'index'])->name('index');
        Route::get('/create', [MessageController::class, 'create'])->name('create');
        Route::post('/', [MessageController::class, 'store'])->name('store');
        Route::get('/{message}/edit', [MessageController::class, 'edit'])->name('edit');
        Route::put('/{message}', [MessageController::class, 'update'])->name('update');
        Route::delete('/{message}/{key}', [MessageController::class, 'destroy'])->name('destroy');
    });

Route::middleware('auth')->group(function () {
    Route::get('/payment/select', [PaymentController::class, 'showPaymentForm'])->name('payment.select');
    Route::post('/payment/process', [PaymentController::class, 'processPayment'])->name('payment.process');
    Route::get('/payment/success', [PaymentController::class, 'paymentSuccess'])->name('payment.success');
    Route::get('/payment/cancel', [PaymentController::class, 'paymentCancel'])->name('payment.cancel');
});

Route::middleware('auth')->group(function () {
    Route::resource('addresses', AddressController::class)
        ->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

    Route::prefix('address-locations')->name('address-locations.')->group(function () {
        Route::get('/countries/{country}/states', [AddressLocationController::class, 'states'])
            ->name('states');

        Route::get('/states/{state}/districts', [AddressLocationController::class, 'districts'])
            ->name('districts');

        Route::get('/districts/{district}/townships', [AddressLocationController::class, 'townships'])
            ->name('townships');

        Route::get('/townships/{township}/wards', [AddressLocationController::class, 'wards'])
            ->name('wards');
    });
});

require __DIR__.'/auth.php';
