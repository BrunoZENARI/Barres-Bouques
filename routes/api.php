<?php

use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    $info = $request->user();
    
    $stack = json_decode($info, true);
    $stack["role"] = json_decode($request->user()->role, true);
    $stack["role"]["permissions"] = json_decode($request->user()->role->permissions, true);
    return json_encode($stack);
});


Route::group(['prefix' => 'admin', 'middleware' => ['auth:sanctum','permission:can_use_admin']], function () {
    Route::group(['prefix' => 'user', 'middleware' => ['auth:sanctum','permission:can_use_admin_users_page']], function () {
        Route::middleware('permission:can_see_admin_users')->post('/getusers', [UserController::class, 'getusers']);
        Route::middleware('permission:can_see_admin_users')->get('/getroles', [UserController::class, 'getroles']);
        Route::middleware('permission:can_create_admin_users')->post('/createuser', [UserController::class, 'createuser']);
        Route::middleware('permission:can_update_admin_users')->post('/updateuser', [UserController::class, 'updateuser']);
        Route::middleware('permission:can_update_admin_users')->post('/updatepassworduser', [UserController::class, 'updatepassworduser']);
        Route::middleware('permission:can_delete_admin_users')->post('/deleteuser', [UserController::class, 'deleteuser']);
    });

    Route::group(['prefix' => 'permission', 'middleware' => ['auth:sanctum','permission:can_use_admin_permissions_page']], function () {
        Route::middleware('permission:can_see_admin_permissions')->post('/getpermissions', [PermissionController::class, 'getpermissions']);
        Route::middleware('permission:can_create_admin_permissions')->post('/createpermission', [PermissionController::class, 'createpermission']);
        Route::middleware('permission:can_update_admin_permissions')->post('/updatepermission', [PermissionController::class, 'updatepermission']);
        Route::middleware('permission:can_delete_admin_permissions')->post('/deletepermission', [PermissionController::class, 'deletepermission']);
    });

    Route::group(['prefix' => 'role', 'middleware' => ['auth:sanctum','permission:can_use_admin_roles_page']], function () {
        Route::middleware('permission:can_see_admin_roles')->post('/getroles', [RoleController::class, 'getroles']);
        Route::middleware('permission:can_see_admin_roles')->get('/getpermissions', [RoleController::class, 'getpermissions']);
        Route::middleware('permission:can_create_admin_roles')->post('/createrole', [RoleController::class, 'createrole']);
        Route::middleware('permission:can_update_admin_roles')->post('/updaterole', [RoleController::class, 'updaterole']);
        Route::middleware('permission:can_delete_admin_roles')->post('/deleterole', [RoleController::class, 'deleterole']);
    });
});
