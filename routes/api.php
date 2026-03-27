<?php

use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\LoanController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return response()->json($request->user()->load('role.permissions'));
});

Route::group(['prefix' => 'admin', 'middleware' => ['auth:sanctum', 'permission:can_use_admin']], function () {

    // Gestion des utilisateurs
    Route::group(['prefix' => 'users', 'middleware' => ['auth:sanctum', 'permission:can_use_admin_users_page']], function () {
        Route::middleware('permission:can_see_admin_users')->post('/search', [UserController::class, 'index'])->name('admin.users.index');
        Route::middleware('permission:can_see_admin_users')->get('/roles', [UserController::class, 'getRoles'])->name('admin.users.roles');
        Route::middleware('permission:can_create_admin_users')->post('/', [UserController::class, 'store'])->name('admin.users.store');
        Route::middleware('permission:can_update_admin_users')->put('/{id}', [UserController::class, 'update'])->name('admin.users.update');
        Route::middleware('permission:can_update_admin_users')->patch('/{id}/password', [UserController::class, 'updatePassword'])->name('admin.users.update-password');
        Route::middleware('permission:can_delete_admin_users')->delete('/{id}', [UserController::class, 'destroy'])->name('admin.users.destroy');
    });

    // Gestion des permissions
    Route::group(['prefix' => 'permissions', 'middleware' => ['auth:sanctum', 'permission:can_use_admin_permissions_page']], function () {
        Route::middleware('permission:can_see_admin_permissions')->post('/search', [PermissionController::class, 'index'])->name('admin.permissions.index');
        Route::middleware('permission:can_create_admin_permissions')->post('/', [PermissionController::class, 'store'])->name('admin.permissions.store');
        Route::middleware('permission:can_update_admin_permissions')->put('/{id}', [PermissionController::class, 'update'])->name('admin.permissions.update');
        Route::middleware('permission:can_delete_admin_permissions')->delete('/{id}', [PermissionController::class, 'destroy'])->name('admin.permissions.destroy');
    });

    // Gestion des rôles
    Route::group(['prefix' => 'roles', 'middleware' => ['auth:sanctum', 'permission:can_use_admin_roles_page']], function () {
        Route::middleware('permission:can_see_admin_roles')->post('/search', [RoleController::class, 'index'])->name('admin.roles.index');
        Route::middleware('permission:can_see_admin_roles')->get('/permissions', [RoleController::class, 'getPermissions'])->name('admin.roles.permissions');
        Route::middleware('permission:can_create_admin_roles')->post('/', [RoleController::class, 'store'])->name('admin.roles.store');
        Route::middleware('permission:can_update_admin_roles')->put('/{id}', [RoleController::class, 'update'])->name('admin.roles.update');
        Route::middleware('permission:can_delete_admin_roles')->delete('/{id}', [RoleController::class, 'destroy'])->name('admin.roles.destroy');
    });
});

// Gestion des ouvrages
Route::group(['prefix' => 'books', 'middleware' => ['auth:sanctum']], function () {
    Route::post('/search', [BookController::class, 'index'])->name('books.index');
    Route::get('/{id}', [BookController::class, 'show'])->name('books.show');
    Route::post('/', [BookController::class, 'store'])->name('books.store');
    Route::put('/{id}', [BookController::class, 'update'])->name('books.update');
    Route::post('/{id}/cover', [BookController::class, 'uploadCover'])->name('books.cover');
    Route::delete('/{id}', [BookController::class, 'destroy'])->name('books.destroy');
});

// Gestion des emprunts
Route::group(['prefix' => 'loans', 'middleware' => ['auth:sanctum']], function () {
    Route::get('/stats', [LoanController::class, 'stats'])->name('loans.stats');
    Route::post('/search', [LoanController::class, 'index'])->name('loans.index');
    Route::get('/{id}', [LoanController::class, 'show'])->name('loans.show');
    Route::post('/', [LoanController::class, 'store'])->name('loans.store');
    Route::put('/{id}', [LoanController::class, 'update'])->name('loans.update');
    Route::delete('/{id}', [LoanController::class, 'destroy'])->name('loans.destroy');
});
