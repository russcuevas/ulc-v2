<?php

use App\Http\Controllers\admin\AdminAreaManilaController;
use App\Http\Controllers\admin\AdminClientController;
use App\Http\Controllers\admin\AdminClientRenewalController;
use App\Http\Controllers\admin\AdminCollectorController;
use App\Http\Controllers\admin\AdminDashboardController;
use App\Http\Controllers\admin\AdminSecretaryController;
use App\Http\Controllers\auth\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Auth Routes
Route::get('/', [AuthController::class, 'LoginPage']);
Route::get('/login', [AuthController::class, 'LoginPage'])->name('auth.login.page');


// Admin Routes
// Admin Dashboard Route
Route::get('/admin/dashboard', [AdminDashboardController::class, 'AdminDashboardPage'])->name('admin.dashboard.page');

// Admin Secretary Management Route
Route::get('/admin/secretary', [AdminSecretaryController::class, 'AdminSecretaryPage'])->name('admin.secretary.page');
Route::put('/admin/secretary/{id}', [AdminSecretaryController::class, 'AdminUpdateSecretaryRequest'])->name('admin.secretary.update');

// Admin Collector Management Route
Route::get('/admin/collector', [AdminCollectorController::class, 'AdminCollectorPage'])->name('admin.collector.page');
Route::put('/admin/collector/{id}', [AdminCollectorController::class, 'AdminUpdateCollectorRequest'])->name('admin.collector.update');

// Admin Client Management Route
Route::get('/admin/client', [AdminClientController::class, 'AdminClientPage'])->name('admin.client.page');
Route::post('/admin/add/client', [AdminClientController::class, 'AdminAddClientRequest'])->name('admin.add.client.request');
Route::get('/admin/edit/client/{id}', [AdminClientController::class, 'AdminEditClientPage'])->name('admin.edit.client.page');
Route::put('/admin/update/client/{id}', [AdminClientController::class, 'AdminUpdateClientRequest'])->name('admin.update.client.request');
Route::delete('/admin/delete/client/{id}', [AdminClientController::class, 'AdminDeleteClientRequest'])->name('admin.delete.client.request');

Route::post('/admin/add/renewal', [AdminClientRenewalController::class, 'AdminClientAddRenewalRequest'])->name('admin.add.renewal.client.request');


// Admin Areas Management Route

// Manila Route
Route::get('/admin/areas/manila', [AdminAreaManilaController::class, 'AdminAreaManilaPage'])->name('admin.area.manila.page');
