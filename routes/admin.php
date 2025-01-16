<?php

use App\Http\Controllers\AdminController;
use App\Models\Admin;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ServiceController;

Route::post('/login', [AdminController::class, 'login'])->name('AdminLogin');
Route::get('/logout', [AdminController::class, 'logout'])->name('admin.logout');

Route::middleware(['auth:sanctum', 'abilities:admin', 'checkRole:super admin'])->group(function () {
  Route::post('/register', [AdminController::class, 'register'])->name('admin.register');
});

Route::middleware(['auth:sanctum', 'checkRole:super admin'])->group(function () {
  Route::get('/topsecret', function () {
    return response()->json(['message' => 'Hello from the top secret page!', 'status' => 'success']);
  });
});

Route::middleware(['auth:sanctum', 'checkPermission:manage providers'])->group(function () {
  Route::get('/providers', function () {
    return response()->json(['message' => 'Welcome to the providers management page!', 'status' => 'success']);
  });
});

Route::middleware(['auth:sanctum', 'abilities:admin'])->group(function () {
  Route::get('/allRoles', [AdminController::class, 'allRoles'])->name('admin.allRoles');
});

Route::middleware(['auth:sanctum', 'abilities:admin', 'checkPermission:create role'])->group(function () {
  Route::post('/createRole', [AdminController::class, 'createRole'])->name('admin.createRole');
  Route::delete('/deleteRole/{id}', [AdminController::class, 'deleteRole'])->name('admin.deleteRole');
});


Route::middleware(['auth:sanctum', 'abilities:admin'])->group(function () {
  Route::get('/allPermissions', [AdminController::class, 'allPermissions'])->name('admin.allPermissions');
  Route::post('/assignPermission', [AdminController::class, 'assignPermission'])->name('admin.assignPermission');
  Route::post('/createPermission', [AdminController::class, 'createPermission'])->name('admin.createPermission');
  Route::delete('/deletePermission/{id}', [AdminController::class, 'deletePermission'])->name('admin.deletePermission');
});

Route::middleware(['auth:sanctum', 'abilities:admin'])->group(function () {
  Route::get('/allAdmins', [AdminController::class, 'allAdmins'])->name('admin.allAdmins');
});


Route::resource('services', ServiceController::class)
  ->missing(function (Request $request) {
    return response(
      content: 'Not Found',
      status: 404,
    );
  });
