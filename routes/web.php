<?php

use App\Http\Controllers\admin\AdminClientController;
use App\Http\Controllers\admin\AdminClientRenewalController;
use App\Http\Controllers\admin\AdminCollectorController;
use App\Http\Controllers\admin\AdminDashboardController;
use App\Http\Controllers\admin\AdminSecretaryController;
use App\Http\Controllers\admin\manila\AdminAreaManilaController;
use App\Http\Controllers\admin\manila\AdminAreaManilaClientsController;
use App\Http\Controllers\admin\manila\AdminAreaManilaClientsHistoryController;
use App\Http\Controllers\admin\manila\AdminAreaManilaPaymentsController;

use App\Http\Controllers\admin\valenzuela\AdminAreaValenzuelaController;
use App\Http\Controllers\admin\valenzuela\AdminAreaValenzuelaClientsController;
use App\Http\Controllers\admin\valenzuela\AdminAreaValenzuelaClientsHistoryController;
use App\Http\Controllers\admin\valenzuela\AdminAreaValenzuelaPaymentsController;
use App\Http\Controllers\auth\AuthController;
use Illuminate\Support\Facades\Route;

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
Route::get('/admin/areas/manila/sales/print', [AdminAreaManilaController::class, 'AdminAreaManilaPrintSalesReports'])->name('admin.area.manila.print.sales');

// Manila Clients Route
Route::get('/admin/areas/manila/{area}/clients', [AdminAreaManilaClientsController::class, 'AdminAreaManilaClientsPage'])->name('admin.area.manila.clients.page');
Route::get('/admin/areas/manila/{area}/clients/renewal', [AdminAreaManilaClientsController::class, 'AdminAreaManilaRenewalClientPage'])->name('admin.area.manila.clients.renewal.page');
Route::get('/admin/areas/manila/{area}/clients/lapsed', [AdminAreaManilaClientsController::class, 'AdminAreaManilaLapsedClientsPage'])->name('admin.area.manila.clients.lapsed.page');
Route::get('/admin/areas/manila/{area}/clients/active', [AdminAreaManilaClientsController::class, 'AdminAreaManilaActiveClientsPage'])->name('admin.area.manila.clients.active.page');
Route::get('/admin/areas/manila/{area}/clients/lapsed/print', [AdminAreaManilaClientsController::class, 'AdminAreaManilaLapsedClientsPrint'])->name('admin.area.manila.clients.lapsed.page.print');

// Manila Clients View History
Route::get('/admin/areas/manila/clients/{clientId}', [AdminAreaManilaClientsHistoryController::class, 'AdminAreaManilaClientsProfilePage'])->name('admin.area.manila.clients.profile.page');
Route::get('/admin/areas/manila/clients/{clientId}/loans/print', [AdminAreaManilaClientsHistoryController::class, 'AdminAreaManilaClientsPrintLoanHistory'])->name('admin.area.manila.clients.print.history.page');
Route::get('/admin/areas/manila/clients/loans/{loanId}/payments', [AdminAreaManilaClientsHistoryController::class, 'AdminAreaManilaClientLoanPaymentsPage'])->name('admin.area.manila.clients.loan.payments');


// Manila Payments Route
Route::get('/admin/areas/manila/{area}/payments', [AdminAreaManilaPaymentsController::class, 'AdminAreaManilaClientPaymentsPage'])->name('admin.area.manila.payments');
Route::post('/admin/areas/manila/{id}/create', [AdminAreaManilaPaymentsController::class, 'AdminAreaManilaClientPaymentsRequest'])->name('areas.area.manila.payments.request');
Route::post('/admin/areas/manila/{id}/update-collection', [AdminAreaManilaPaymentsController::class, 'AdminAreaManilaClientUpdateCollection'])->name('admin.area.manila.update.collection');
Route::get('/admin/areas/manila/payments/{referenceNumber}/clients', [AdminAreaManilaPaymentsController::class, 'AdminAreaManilaClientDailyPaymentsPage'])->name('admin.area.manila.payments.clients');
Route::get('/admin/areas/manila/payments/{referenceNumber}/print', [AdminAreaManilaPaymentsController::class, 'AdminAreaManilaClientPrintDailyPayments'])->name('admin.area.manila.payments.print');
Route::post('/admin/areas/manila/collect-payment/{clientPaymentId}', [AdminAreaManilaPaymentsController::class, 'AdminAreaManilaClientCollectPaymentRequest'])->name('admin.manila.payments.clients.collect');
Route::post('/admin/areas/manila/no-payment/{clientPaymentId}', [AdminAreaManilaPaymentsController::class, 'AdminAreaManilaClientNoPaymentRequest'])->name('admin.manila.payments.clients.not.paid');
Route::get('/admin/areas/manila/payments/{area}/summary/collections/print', [AdminAreaManilaPaymentsController::class, 'AdminAreaManilaPrintSummaryCollections'])->name('admin.area.manila.payments.print.summary.collections');



// Valenzuela Route
Route::get('/admin/areas/valenzuela', [AdminAreaValenzuelaController::class, 'AdminAreaValenzuelaPage'])->name('admin.area.valenzuela.page');
Route::get('/admin/areas/valenzuela/sales/print', [AdminAreaValenzuelaController::class, 'AdminAreaValenzuelaPrintSalesReports'])->name('admin.area.valenzuela.print.sales');

// Valenzuela Clients Route
Route::get('/admin/areas/valenzuela/{area}/clients', [AdminAreaValenzuelaClientsController::class, 'AdminAreaValenzuelaClientsPage'])->name('admin.area.valenzuela.clients.page');
Route::get('/admin/areas/valenzuela/{area}/clients/renewal', [AdminAreaValenzuelaClientsController::class, 'AdminAreaValenzuelaRenewalClientPage'])->name('admin.area.valenzuela.clients.renewal.page');
Route::get('/admin/areas/valenzuela/{area}/clients/lapsed', [AdminAreaValenzuelaClientsController::class, 'AdminAreaValenzuelaLapsedClientsPage'])->name('admin.area.valenzuela.clients.lapsed.page');
Route::get('/admin/areas/valenzuela/{area}/clients/active', [AdminAreaValenzuelaClientsController::class, 'AdminAreaValenzuelaActiveClientsPage'])->name('admin.area.valenzuela.clients.active.page');
Route::get('/admin/areas/valenzuela/{area}/clients/lapsed/print', [AdminAreaValenzuelaClientsController::class, 'AdminAreaValenzuelaLapsedClientsPrint'])->name('admin.area.valenzuela.clients.lapsed.page.print');

// Valenzuela Clients View History
Route::get('/admin/areas/valenzuela/clients/{clientId}', [AdminAreaValenzuelaClientsHistoryController::class, 'AdminAreaValenzuelaClientsProfilePage'])->name('admin.area.valenzuela.clients.profile.page');
Route::get('/admin/areas/valenzuela/clients/{clientId}/loans/print', [AdminAreaValenzuelaClientsHistoryController::class, 'AdminAreaValenzuelaClientsPrintLoanHistory'])->name('admin.area.valenzuela.clients.print.history.page');
Route::get('/admin/areas/valenzuela/clients/loans/{loanId}/payments', [AdminAreaValenzuelaClientsHistoryController::class, 'AdminAreaValenzuelaClientLoanPaymentsPage'])->name('admin.area.valenzuela.clients.loan.payments');


// Valenzuela Payments Route
Route::get('/admin/areas/valenzuela/{area}/payments', [AdminAreaValenzuelaPaymentsController::class, 'AdminAreaValenzuelaClientPaymentsPage'])->name('admin.area.valenzuela.payments');
Route::post('/admin/areas/valenzuela/{id}/create', [AdminAreaValenzuelaPaymentsController::class, 'AdminAreaValenzuelaClientPaymentsRequest'])->name('areas.area.valenzuela.payments.request');
Route::post('/admin/areas/valenzuela/{id}/update-collection', [AdminAreaValenzuelaPaymentsController::class, 'AdminAreaValenzuelaClientUpdateCollection'])->name('admin.area.valenzuela.update.collection');

Route::get('/admin/areas/valenzuela/payments/{referenceNumber}/clients', [AdminAreaValenzuelaPaymentsController::class, 'AdminAreaValenzuelaClientDailyPaymentsPage'])->name('admin.area.valenzuela.payments.clients');
Route::get('/admin/areas/valenzuela/payments/{referenceNumber}/print', [AdminAreaValenzuelaPaymentsController::class, 'AdminAreaValenzuelaClientPrintDailyPayments'])->name('admin.area.valenzuela.payments.print');
Route::post('/admin/areas/valenzuela/collect-payment/{clientPaymentId}', [AdminAreaValenzuelaPaymentsController::class, 'AdminAreaValenzuelaClientCollectPaymentRequest'])->name('admin.valenzuela.payments.clients.collect');
Route::post('/admin/areas/valenzuela/no-payment/{clientPaymentId}', [AdminAreaValenzuelaPaymentsController::class, 'AdminAreaValenzuelaClientNoPaymentRequest'])->name('admin.valenzuela.payments.clients.not.paid');
Route::get('/admin/areas/valenzuela/payments/{area}/summary/collections/print', [AdminAreaValenzuelaPaymentsController::class, 'AdminAreaValenzuelaPrintSummaryCollections'])->name('admin.area.valenzuela.payments.print.summary.collections');
