<?php

use App\Http\Controllers\admin\AdminClientController;
use App\Http\Controllers\admin\AdminClientRenewalController;
use App\Http\Controllers\admin\AdminCollectorController;
use App\Http\Controllers\admin\AdminDashboardController;
use App\Http\Controllers\admin\AdminNotificationsController;
use App\Http\Controllers\admin\AdminProfileController;
use App\Http\Controllers\admin\AdminSecretaryController;
// ADMIN MANILA ROUTE
use App\Http\Controllers\admin\manila\AdminAreaManilaController;
use App\Http\Controllers\admin\manila\AdminAreaManilaClientsController;
use App\Http\Controllers\admin\manila\AdminAreaManilaClientsHistoryController;
use App\Http\Controllers\admin\manila\AdminAreaManilaPaymentsController;
// ADMIN VALENZUELA ROUTE
use App\Http\Controllers\admin\valenzuela\AdminAreaValenzuelaController;
use App\Http\Controllers\admin\valenzuela\AdminAreaValenzuelaClientsController;
use App\Http\Controllers\admin\valenzuela\AdminAreaValenzuelaClientsHistoryController;
use App\Http\Controllers\admin\valenzuela\AdminAreaValenzuelaPaymentsController;
// ADMIN CALOOCAN ROUTE
use App\Http\Controllers\admin\caloocan\AdminAreaCaloocanController;
use App\Http\Controllers\admin\caloocan\AdminAreaCaloocanClientsController;
use App\Http\Controllers\admin\caloocan\AdminAreaCaloocanClientsHistoryController;
use App\Http\Controllers\admin\caloocan\AdminAreaCaloocanPaymentsController;
// ADMIN FC ROUTE
use App\Http\Controllers\admin\fc\AdminAreaFCController;
use App\Http\Controllers\admin\fc\AdminAreaFCClientsController;
use App\Http\Controllers\admin\fc\AdminAreaFCClientsHistoryController;
use App\Http\Controllers\admin\fc\AdminAreaFCPaymentsController;

use App\Http\Controllers\auth\AuthController;
use App\Http\Controllers\secretary\manila\ManilaAreaClientsController;
use App\Http\Controllers\secretary\manila\ManilaAreaClientsHistoryController;
use App\Http\Controllers\secretary\manila\ManilaAreaController;
use App\Http\Controllers\secretary\manila\ManilaAreaPaymentsController;
use App\Http\Controllers\secretary\manila\ManilaClientsController;
use App\Http\Controllers\secretary\manila\ManilaClientsRenewalController;
use App\Http\Controllers\secretary\manila\ManilaDashboardController;
use App\Http\Controllers\secretary\manila\ManilaNotificationsController;
use App\Http\Controllers\secretary\manila\ManilaProfileController;
use App\Http\Controllers\secretary\valenzuela\ValenzuelaDashboardController;
use Illuminate\Support\Facades\Route;

// Auth Routes
Route::get('/', [AuthController::class, 'LoginPage']);
Route::get('/login', [AuthController::class, 'LoginPage'])->name('auth.login.page');
Route::post('/login', [AuthController::class, 'LoginRequest'])->name('auth.login.request');
Route::post('/logout', [AuthController::class, 'Logout'])->name('auth.logout.request');

Route::middleware(['auth', 'admin'])->group(
    function () {
        // Admin Routes
        // Admin Dashboard Route
        Route::get('/admin/dashboard', [AdminDashboardController::class, 'AdminDashboardPage'])->name('admin.dashboard.page');
        Route::get('admin/dashboard/analytics/{location}', [AdminDashboardController::class, 'AnalyticsPage'])->name('admin.dashboard.analytics');

        Route::get('/admin/notifications', [AdminNotificationsController::class, 'AdminNotificationPage'])->name('admin.notification.page');
        Route::post('/admin/notifications/mark-all-read', [AdminNotificationsController::class, 'AdminMarkAllAsReadNotifications'])->name('admin.notifications.mark.all.read');


        Route::get('/admin/notifications/fetch', [AdminNotificationsController::class, 'AdminFetchNotifications'])->name('admin.notifications.fetch');
        Route::post('/admin/notifications/mark-as-read', [AdminNotificationsController::class, 'AdminMarkAsReadNotifications'])->name('admin.notifications.read.notification');

        //Admin Profile Management Route
        Route::get('/admin/profile', [AdminProfileController::class, 'AdminProfilePage'])->name('admin.profile.page');
        Route::post('/profile/update', [AdminProfileController::class, 'UpdateProfile'])
            ->name('admin.profile.update');

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
        // SMS
        Route::post('/admin/areas/manila/collect-payment/{clientPaymentId}', [AdminAreaManilaPaymentsController::class, 'AdminAreaManilaClientCollectPaymentRequest'])->name('admin.manila.payments.clients.collect');
        Route::post('/admin/areas/manila/remind-payment/{clientPaymentId}', [AdminAreaManilaPaymentsController::class, 'AdminAreaManilaClientRemindPaymentRequest'])->name('admin.manila.payments.clients.remind');
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
        // SMS
        Route::post('/admin/areas/valenzuela/collect-payment/{clientPaymentId}', [AdminAreaValenzuelaPaymentsController::class, 'AdminAreaValenzuelaClientCollectPaymentRequest'])->name('admin.valenzuela.payments.clients.collect');
        Route::post('/admin/areas/valenzuela/remind-payment/{clientPaymentId}', [AdminAreaValenzuelaPaymentsController::class, 'AdminAreaValenzuelaClientRemindPaymentRequest'])->name('admin.valenzuela.payments.clients.remind');
        Route::post('/admin/areas/valenzuela/no-payment/{clientPaymentId}', [AdminAreaValenzuelaPaymentsController::class, 'AdminAreaValenzuelaClientNoPaymentRequest'])->name('admin.valenzuela.payments.clients.not.paid');
        Route::get('/admin/areas/valenzuela/payments/{area}/summary/collections/print', [AdminAreaValenzuelaPaymentsController::class, 'AdminAreaValenzuelaPrintSummaryCollections'])->name('admin.area.valenzuela.payments.print.summary.collections');

        // Caloocan Route
        Route::get('/admin/areas/caloocan', [AdminAreaCaloocanController::class, 'AdminAreaCaloocanPage'])->name('admin.area.caloocan.page');
        Route::get('/admin/areas/caloocan/sales/print', [AdminAreaCaloocanController::class, 'AdminAreaCaloocanPrintSalesReports'])->name('admin.area.caloocan.print.sales');

        // Caloocan Clients Route
        Route::get('/admin/areas/caloocan/{area}/clients', [AdminAreaCaloocanClientsController::class, 'AdminAreaCaloocanClientsPage'])->name('admin.area.caloocan.clients.page');
        Route::get('/admin/areas/caloocan/{area}/clients/renewal', [AdminAreaCaloocanClientsController::class, 'AdminAreaCaloocanRenewalClientPage'])->name('admin.area.caloocan.clients.renewal.page');
        Route::get('/admin/areas/caloocan/{area}/clients/lapsed', [AdminAreaCaloocanClientsController::class, 'AdminAreaCaloocanLapsedClientsPage'])->name('admin.area.caloocan.clients.lapsed.page');
        Route::get('/admin/areas/caloocan/{area}/clients/active', [AdminAreaCaloocanClientsController::class, 'AdminAreaCaloocanActiveClientsPage'])->name('admin.area.caloocan.clients.active.page');
        Route::get('/admin/areas/caloocan/{area}/clients/lapsed/print', [AdminAreaCaloocanClientsController::class, 'AdminAreaCaloocanLapsedClientsPrint'])->name('admin.area.caloocan.clients.lapsed.page.print');

        // Caloocan Clients View History
        Route::get('/admin/areas/caloocan/clients/{clientId}', [AdminAreaCaloocanClientsHistoryController::class, 'AdminAreaCaloocanClientsProfilePage'])->name('admin.area.caloocan.clients.profile.page');
        Route::get('/admin/areas/caloocan/clients/{clientId}/loans/print', [AdminAreaCaloocanClientsHistoryController::class, 'AdminAreaCaloocanClientsPrintLoanHistory'])->name('admin.area.caloocan.clients.print.history.page');
        Route::get('/admin/areas/caloocan/clients/loans/{loanId}/payments', [AdminAreaCaloocanClientsHistoryController::class, 'AdminAreaCaloocanClientLoanPaymentsPage'])->name('admin.area.caloocan.clients.loan.payments');


        // Caloocan Payments Route
        Route::get('/admin/areas/caloocan/{area}/payments', [AdminAreaCaloocanPaymentsController::class, 'AdminAreaCaloocanClientPaymentsPage'])->name('admin.area.caloocan.payments');
        Route::post('/admin/areas/caloocan/{id}/create', [AdminAreaCaloocanPaymentsController::class, 'AdminAreaCaloocanClientPaymentsRequest'])->name('areas.area.caloocan.payments.request');
        Route::post('/admin/areas/caloocan/{id}/update-collection', [AdminAreaCaloocanPaymentsController::class, 'AdminAreaCaloocanClientUpdateCollection'])->name('admin.area.caloocan.update.collection');

        Route::get('/admin/areas/caloocan/payments/{referenceNumber}/clients', [AdminAreaCaloocanPaymentsController::class, 'AdminAreaCaloocanClientDailyPaymentsPage'])->name('admin.area.caloocan.payments.clients');
        Route::get('/admin/areas/caloocan/payments/{referenceNumber}/print', [AdminAreaCaloocanPaymentsController::class, 'AdminAreaCaloocanClientPrintDailyPayments'])->name('admin.area.caloocan.payments.print');
        // SMS
        Route::post('/admin/areas/caloocan/collect-payment/{clientPaymentId}', [AdminAreaCaloocanPaymentsController::class, 'AdminAreaCaloocanClientCollectPaymentRequest'])->name('admin.caloocan.payments.clients.collect');
        Route::post('/admin/areas/caloocan/remind-payment/{clientPaymentId}', [AdminAreaCaloocanPaymentsController::class, 'AdminAreaCaloocanClientRemindPaymentRequest'])->name('admin.caloocan.payments.clients.remind');
        Route::post('/admin/areas/caloocan/no-payment/{clientPaymentId}', [AdminAreaCaloocanPaymentsController::class, 'AdminAreaCaloocanClientNoPaymentRequest'])->name('admin.caloocan.payments.clients.not.paid');
        Route::get('/admin/areas/caloocan/payments/{area}/summary/collections/print', [AdminAreaCaloocanPaymentsController::class, 'AdminAreaCaloocanPrintSummaryCollections'])->name('admin.area.caloocan.payments.print.summary.collections');

        // FC Route
        Route::get('/admin/areas/fc', [AdminAreaFCController::class, 'AdminAreaFCPage'])->name('admin.area.fc.page');
        Route::get('/admin/areas/fc/sales/print', [AdminAreaFCController::class, 'AdminAreaFCPrintSalesReports'])->name('admin.area.fc.print.sales');

        // FC Clients Route
        Route::get('/admin/areas/fc/{area}/clients', [AdminAreaFCClientsController::class, 'AdminAreaFCClientsPage'])->name('admin.area.fc.clients.page');
        Route::get('/admin/areas/fc/{area}/clients/renewal', [AdminAreaFCClientsController::class, 'AdminAreaFCRenewalClientPage'])->name('admin.area.fc.clients.renewal.page');
        Route::get('/admin/areas/fc/{area}/clients/lapsed', [AdminAreaFCClientsController::class, 'AdminAreaFCLapsedClientsPage'])->name('admin.area.fc.clients.lapsed.page');
        Route::get('/admin/areas/fc/{area}/clients/active', [AdminAreaFCClientsController::class, 'AdminAreaFCActiveClientsPage'])->name('admin.area.fc.clients.active.page');
        Route::get('/admin/areas/fc/{area}/clients/lapsed/print', [AdminAreaFCClientsController::class, 'AdminAreaFCLapsedClientsPrint'])->name('admin.area.fc.clients.lapsed.page.print');

        // FC Clients View History
        Route::get('/admin/areas/fc/clients/{clientId}', [AdminAreaFCClientsHistoryController::class, 'AdminAreaFCClientsProfilePage'])->name('admin.area.fc.clients.profile.page');
        Route::get('/admin/areas/fc/clients/{clientId}/loans/print', [AdminAreaFCClientsHistoryController::class, 'AdminAreaFCClientsPrintLoanHistory'])->name('admin.area.fc.clients.print.history.page');
        Route::get('/admin/areas/fc/clients/loans/{loanId}/payments', [AdminAreaFCClientsHistoryController::class, 'AdminAreaFCClientLoanPaymentsPage'])->name('admin.area.fc.clients.loan.payments');


        // FC Payments Route
        Route::get('/admin/areas/fc/{area}/payments', [AdminAreaFCPaymentsController::class, 'AdminAreaFCClientPaymentsPage'])->name('admin.area.fc.payments');
        Route::post('/admin/areas/fc/{id}/create', [AdminAreaFCPaymentsController::class, 'AdminAreaFCClientPaymentsRequest'])->name('areas.area.fc.payments.request');
        Route::post('/admin/areas/fc/{id}/update-collection', [AdminAreaFCPaymentsController::class, 'AdminAreaFCClientUpdateCollection'])->name('admin.area.fc.update.collection');

        Route::get('/admin/areas/fc/payments/{referenceNumber}/clients', [AdminAreaFCPaymentsController::class, 'AdminAreaFCClientDailyPaymentsPage'])->name('admin.area.fc.payments.clients');
        Route::get('/admin/areas/fc/payments/{referenceNumber}/print', [AdminAreaFCPaymentsController::class, 'AdminAreaFCClientPrintDailyPayments'])->name('admin.area.fc.payments.print');
        // SMS
        Route::post('/admin/areas/fc/collect-payment/{clientPaymentId}', [AdminAreaFCPaymentsController::class, 'AdminAreaFCClientCollectPaymentRequest'])->name('admin.fc.payments.clients.collect');
        Route::post('/admin/areas/fc/remind-payment/{clientPaymentId}', [AdminAreaFCPaymentsController::class, 'AdminAreaFCClientRemindPaymentRequest'])->name('admin.fc.payments.clients.remind');
        Route::post('/admin/areas/fc/no-payment/{clientPaymentId}', [AdminAreaFCPaymentsController::class, 'AdminAreaFCClientNoPaymentRequest'])->name('admin.fc.payments.clients.not.paid');
        Route::get('/admin/areas/fc/payments/{area}/summary/collections/print', [AdminAreaFCPaymentsController::class, 'AdminAreaFCPrintSummaryCollections'])->name('admin.area.fc.payments.print.summary.collections');
    }
);


Route::prefix('secretary')->middleware(['auth'])->group(function () {
    // Secretary Manila Area Route
    // Secretary Manila Dashboard Route
    Route::get('/manila/dashboard', [ManilaDashboardController::class, 'ManilaDashboardPage'])->middleware('secretary.area:manila')->name('secretary.manila.dashboard.page');
    Route::get('/manila/areas/breakdown/analytics', [ManilaDashboardController::class, 'ManilaAreasBreakdownSummary'])->middleware('secretary.area:manila')->name('secretary.manila.analytics.page');
    Route::get('/manila/notifications/fetch', [ManilaNotificationsController::class, 'ManilaFetchNotifications'])->name('secretary.manila.fetch_notifications');
    Route::get('/manila/notifications', [ManilaNotificationsController::class, 'ManilaNotificationsPage'])->name('secretary.manila.notification.page');
    Route::post('/manila/notifications/mark-all-read', [ManilaNotificationsController::class, 'ManilaMarkAllAsReadNotifications'])->name('secretary.manila.notifications.mark.all.read');


    //Secretary Manila Profile Management Route
    Route::get('/manila/profile', [ManilaProfileController::class, 'ManilaProfilePage'])->name('secretary.manila.profile.page');
    Route::post('/manila/profile/update', [ManilaProfileController::class, 'ManilaUpdateProfile'])
        ->name('secretary.profile.update');

    // Secretary Clients Route
    Route::get('/manila/clients', [ManilaClientsController::class, 'ManilaClientsPage'])->middleware('secretary.area:manila')->name('secretary.manila.clients.page');
    Route::post('/manila/add/clients', [ManilaClientsController::class, 'ManilaAddClientRequest'])->middleware('secretary.area:manila')->name('secretary.manila.add.clients.request');
    Route::get('/manila/edit/clients/{id}', [ManilaClientsController::class, 'ManilaEditClientPage'])->middleware('secretary.area:manila')->name('secretary.manila.edit.clients.page');
    Route::put('/manila/update/clients/{id}', [ManilaClientsController::class, 'ManilaUpdateClientRequest'])->middleware('secretary.area:manila')->name('secretary.manila.update.clients.request');
    Route::delete('/manila/delete/clients/{id}', [ManilaClientsController::class, 'ManilaDeleteClientRequest'])->middleware('secretary.area:manila')->name('secretary.manila.delete.clients.request');
    Route::post('/manila/add/renewal', [ManilaClientsRenewalController::class, 'ManilaClientAddRenewalRequest'])->middleware('secretary.area:manila')->name('secretary.manila.add.renewal.clients.request');

    // Secretary List of Areas Route
    Route::get('/manila/areas/', [ManilaAreaController::class, 'ManilaAreaPage'])->middleware('secretary.area:manila')->name('secretary.manila.area.page');
    Route::get('/manila/areas/sales/print', [ManilaAreaController::class, 'ManilaAreaPrintSalesReports'])->middleware('secretary.area:manila')->name('secretary.area.manila.print.sales');

    //Secretary Clients Accounts
    Route::get('/manila/{area}/clients', [ManilaAreaClientsController::class, 'ManilaAreaClientsPage'])->middleware('secretary.area:manila')->name('secretary.area.manila.clients.page');
    Route::get('/manila/{area}/clients/lapsed', [ManilaAreaClientsController::class, 'ManilaAreaLapsedClientsPage'])->name('secretary.area.manila.clients.lapsed.page');
    Route::get('/manila/{area}/clients/renewal', [ManilaAreaClientsController::class, 'ManilaAreaRenewalClientPage'])->name('secretary.area.manila.clients.renewal.page');
    Route::get('/manila/{area}/clients/active', [ManilaAreaClientsController::class, 'ManilaAreaActiveClientsPage'])->name('secretary.area.manila.clients.active.page');
    Route::get('/manila/{area}/clients/lapsed/print', [ManilaAreaClientsController::class, 'ManilaAreaLapsedClientsPrint'])->middleware('secretary.area:manila')->name('secretary.area.manila.clients.lapsed.page.print');

    //Secretary Clients History
    Route::get('/manila/clients/{clientId}', [ManilaAreaClientsHistoryController::class, 'ManilaAreaClientsProfilePage'])->middleware('secretary.area:manila')->name('secretary.area.manila.clients.profile.page');
    Route::get('/manila/clients/{clientId}/loans/print', [ManilaAreaClientsHistoryController::class, 'ManilaAreaClientsPrintLoanHistory'])->middleware('secretary.area:manila')->name('secretary.area.manila.clients.print.history.page');
    Route::get('/manila/clients/loans/{loanId}/payments', [ManilaAreaClientsHistoryController::class, 'ManilaAreaClientLoanPaymentsPage'])->middleware('secretary.area:manila')->name('secretary.area.manila.clients.loan.payments');

    // Secretary Areas Payments Route
    Route::get('/manila/{area}/payments', [ManilaAreaPaymentsController::class, 'ManilaClientPaymentsPage'])->middleware('secretary.area:manila')->name('secretary.area.manila.payments');
    Route::post('/manila/{id}/create/payments', [ManilaAreaPaymentsController::class, 'ManilaClientPaymentsRequest'])->middleware('secretary.area:manila')->name('secretary.area.manila.payments.request');
    Route::get('/manila/payments/{area}/summary/collections/print', [ManilaAreaPaymentsController::class, 'ManilaPrintSummaryCollections'])->middleware('secretary.area:manila')->name('secretary.area.manila.payments.print.summary.collections');
    Route::get('/manila/payments/{referenceNumber}/clients', [ManilaAreaPaymentsController::class, 'ManilaClientDailyPaymentsPage'])->middleware('secretary.area:manila')->name('secretary.area.manila.payments.clients');
    Route::post('/manila/payments/{id}/update-collection', [ManilaAreaPaymentsController::class, 'ManilaClientUpdateCollection'])->middleware('secretary.area:manila')->name('secretary.area.manila.update.collection');
    Route::get('/manila/payments/{referenceNumber}/print', [ManilaAreaPaymentsController::class, 'ManilaClientPrintDailyPayments'])->middleware('secretary.area:manila')->name('secretary.area.manila.payments.print');

    // NEED SMS
    Route::post('/manila/collect-payment/{clientPaymentId}', [ManilaAreaPaymentsController::class, 'ManilaClientCollectPaymentRequest'])->middleware('secretary.area:manila')->name('secretary.manila.payments.clients.collect');
    Route::post('/manila/no-payment/{clientPaymentId}', [ManilaAreaPaymentsController::class, 'ManilaClientNoPaymentRequest'])->middleware('secretary.area:manila')->name('secretary.area.manila.payments.clients.not.paid');

    // Secretary Valenzuela Area Route
    // Secretary Valenzuela Dashboard Route
    Route::get('/valenzuela/dashboard', [ValenzuelaDashboardController::class, 'ValenzuelaDashboardPage'])->middleware('secretary.area:valenzuela')->name('secretary.valenzuela.dashboard.page');
});
