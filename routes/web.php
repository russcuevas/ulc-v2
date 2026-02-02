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
use App\Http\Controllers\auth\ForgotPasswordController;
use App\Http\Controllers\collector\caloocan\CaloocanCollectorDashboardController;
use App\Http\Controllers\collector\caloocan\CaloocanCollectorPaymentController;
use App\Http\Controllers\collector\fc\FCCollectorDashboardController;
use App\Http\Controllers\collector\fc\FCCollectorPaymentController;
use App\Http\Controllers\secretary\manila\ManilaAreaClientsController;
use App\Http\Controllers\secretary\manila\ManilaAreaClientsHistoryController;
use App\Http\Controllers\secretary\manila\ManilaAreaController;
use App\Http\Controllers\secretary\manila\ManilaAreaPaymentsController;
use App\Http\Controllers\secretary\manila\ManilaClientsController;
use App\Http\Controllers\secretary\manila\ManilaClientsRenewalController;
use App\Http\Controllers\secretary\manila\ManilaDashboardController;
use App\Http\Controllers\secretary\manila\ManilaNotificationsController;
use App\Http\Controllers\secretary\manila\ManilaProfileController;

use App\Http\Controllers\secretary\valenzuela\ValenzuelaAreaClientsController;
use App\Http\Controllers\secretary\valenzuela\ValenzuelaAreaClientsHistoryController;
use App\Http\Controllers\secretary\valenzuela\ValenzuelaAreaController;
use App\Http\Controllers\secretary\valenzuela\ValenzuelaAreaPaymentsController;
use App\Http\Controllers\secretary\valenzuela\ValenzuelaClientsController;
use App\Http\Controllers\secretary\valenzuela\ValenzuelaClientsRenewalController;
use App\Http\Controllers\secretary\valenzuela\ValenzuelaDashboardController;
use App\Http\Controllers\secretary\valenzuela\ValenzuelaNotificationsController;
use App\Http\Controllers\secretary\valenzuela\ValenzuelaProfileController;

use App\Http\Controllers\secretary\caloocan\CaloocanAreaClientsController;
use App\Http\Controllers\secretary\caloocan\CaloocanAreaClientsHistoryController;
use App\Http\Controllers\secretary\caloocan\CaloocanAreaController;
use App\Http\Controllers\secretary\caloocan\CaloocanAreaPaymentsController;
use App\Http\Controllers\secretary\caloocan\CaloocanClientsController;
use App\Http\Controllers\secretary\caloocan\CaloocanClientsRenewalController;
use App\Http\Controllers\secretary\caloocan\CaloocanDashboardController;
use App\Http\Controllers\secretary\caloocan\CaloocanNotificationsController;
use App\Http\Controllers\secretary\caloocan\CaloocanProfileController;

use App\Http\Controllers\secretary\fc\FCAreaClientsController;
use App\Http\Controllers\secretary\fc\FCAreaClientsHistoryController;
use App\Http\Controllers\secretary\fc\FCAreaController;
use App\Http\Controllers\secretary\fc\FCAreaPaymentsController;
use App\Http\Controllers\secretary\fc\FCClientsController;
use App\Http\Controllers\secretary\fc\FCClientsRenewalController;
use App\Http\Controllers\secretary\fc\FCDashboardController;
use App\Http\Controllers\secretary\fc\FCNotificationsController;
use App\Http\Controllers\secretary\fc\FCProfileController;


use App\Http\Controllers\collector\manila\ManilaCollectorDashboardController;
use App\Http\Controllers\collector\manila\ManilaCollectorPaymentController;
use App\Http\Controllers\collector\valenzuela\ValenzuelaCollectorDashboardController;
use App\Http\Controllers\collector\valenzuela\ValenzuelaCollectorPaymentController;
use Illuminate\Support\Facades\Route;

// Auth Routes
Route::get('/', [AuthController::class, 'LoginPage']);
Route::get('/login', [AuthController::class, 'LoginPage'])->name('auth.login.page');
Route::post('/login', [AuthController::class, 'LoginRequest'])->name('auth.login.request');
Route::post('/logout', [AuthController::class, 'Logout'])->name('auth.logout.request');

Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetCode'])
    ->name('password.send.code');

Route::get('/reset-password', [ForgotPasswordController::class, 'showResetForm'])
    ->name('password.reset.form');

Route::post('/verify-reset-code', [ForgotPasswordController::class, 'verifyCode'])
    ->name('password.verify.code');

Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword'])
    ->name('password.reset');


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
        Route::post('/admin/areas/manila/payments/{reference}/collect-all', [AdminAreaManilaPaymentsController::class, 'AdminAreaManilaCollectAllPayments'])->name('admin.manila.collect.all');
        Route::post('/admin/manila/no-payment-all/{reference}', [AdminAreaManilaPaymentsController::class, 'AdminAreaManilaNoPaymentAll'])->name('admin.manila.no-payment.all');
        Route::post('/admin/areas/manila/remind-payments/{reference}', [AdminAreaManilaPaymentsController::class, 'AdminAreaManilaRemindPaymentsByReference'])->name('admin.manila.payments.remind.by.reference');

        // Route::post('/admin/areas/manila/collect-payment/{clientPaymentId}', [AdminAreaManilaPaymentsController::class, 'AdminAreaManilaClientCollectPaymentRequest'])->name('admin.manila.payments.clients.collect');
        // Route::post('/admin/areas/manila/no-payment/{clientPaymentId}', [AdminAreaManilaPaymentsController::class, 'AdminAreaManilaClientNoPaymentRequest'])->name('admin.manila.payments.clients.not.paid');

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
        Route::post('/admin/areas/valenzuela/payments/{reference}/collect-all', [AdminAreaValenzuelaPaymentsController::class, 'AdminAreaValenzuelaCollectAllPayments'])->name('admin.valenzuela.collect.all');
        Route::post('/admin/valenzuela/no-payment-all/{reference}', [AdminAreaValenzuelaPaymentsController::class, 'AdminAreaValenzuelaNoPaymentAll'])->name('admin.valenzuela.no-payment.all');
        Route::post('/admin/areas/valenzuela/remind-payments/{reference}', [AdminAreaValenzuelaPaymentsController::class, 'AdminAreaValenzuelaRemindPaymentsByReference'])->name('admin.valenzuela.payments.remind.by.reference');

        // Route::post('/admin/areas/valenzuela/collect-payment/{clientPaymentId}', [AdminAreaValenzuelaPaymentsController::class, 'AdminAreaValenzuelaClientCollectPaymentRequest'])->name('admin.valenzuela.payments.clients.collect');
        // Route::post('/admin/areas/valenzuela/remind-payment/{clientPaymentId}', [AdminAreaValenzuelaPaymentsController::class, 'AdminAreaValenzuelaClientRemindPaymentRequest'])->name('admin.valenzuela.payments.clients.remind');
        // Route::post('/admin/areas/valenzuela/no-payment/{clientPaymentId}', [AdminAreaValenzuelaPaymentsController::class, 'AdminAreaValenzuelaClientNoPaymentRequest'])->name('admin.valenzuela.payments.clients.not.paid');
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
        Route::post('/admin/areas/caloocan/payments/{reference}/collect-all', [AdminAreaCaloocanPaymentsController::class, 'AdminAreaCaloocanCollectAllPayments'])->name('admin.caloocan.collect.all');
        Route::post('/admin/caloocan/no-payment-all/{reference}', [AdminAreaCaloocanPaymentsController::class, 'AdminAreaCaloocanNoPaymentAll'])->name('admin.caloocan.no-payment.all');
        Route::post('/admin/areas/caloocan/remind-payments/{reference}', [AdminAreaCaloocanPaymentsController::class, 'AdminAreaCaloocanRemindPaymentsByReference'])->name('admin.caloocan.payments.remind.by.reference');

        // Route::post('/admin/areas/caloocan/collect-payment/{clientPaymentId}', [AdminAreaCaloocanPaymentsController::class, 'AdminAreaCaloocanClientCollectPaymentRequest'])->name('admin.caloocan.payments.clients.collect');
        // Route::post('/admin/areas/caloocan/remind-payment/{clientPaymentId}', [AdminAreaCaloocanPaymentsController::class, 'AdminAreaCaloocanClientRemindPaymentRequest'])->name('admin.caloocan.payments.clients.remind');
        // Route::post('/admin/areas/caloocan/no-payment/{clientPaymentId}', [AdminAreaCaloocanPaymentsController::class, 'AdminAreaCaloocanClientNoPaymentRequest'])->name('admin.caloocan.payments.clients.not.paid');

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
        Route::post('/admin/areas/fc/payments/{reference}/collect-all', [AdminAreaFCPaymentsController::class, 'AdminAreaFCCollectAllPayments'])->name('admin.fc.collect.all');
        Route::post('/admin/fc/no-payment-all/{reference}', [AdminAreaFCPaymentsController::class, 'AdminAreaFCNoPaymentAll'])->name('admin.fc.no-payment.all');
        Route::post('/admin/areas/fc/remind-payments/{reference}', [AdminAreaFCPaymentsController::class, 'AdminAreaFCRemindPaymentsByReference'])->name('admin.fc.payments.remind.by.reference');

        // Route::post('/admin/areas/fc/collect-payment/{clientPaymentId}', [AdminAreaFCPaymentsController::class, 'AdminAreaFCClientCollectPaymentRequest'])->name('admin.fc.payments.clients.collect');
        // Route::post('/admin/areas/fc/remind-payment/{clientPaymentId}', [AdminAreaFCPaymentsController::class, 'AdminAreaFCClientRemindPaymentRequest'])->name('admin.fc.payments.clients.remind');
        // Route::post('/admin/areas/fc/no-payment/{clientPaymentId}', [AdminAreaFCPaymentsController::class, 'AdminAreaFCClientNoPaymentRequest'])->name('admin.fc.payments.clients.not.paid');

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
    Route::get('/manila/{area}/clients/lapsed', [ManilaAreaClientsController::class, 'ManilaAreaLapsedClientsPage'])->middleware('secretary.area:manila')->name('secretary.area.manila.clients.lapsed.page');
    Route::get('/manila/{area}/clients/renewal', [ManilaAreaClientsController::class, 'ManilaAreaRenewalClientPage'])->middleware('secretary.area:manila')->name('secretary.area.manila.clients.renewal.page');
    Route::get('/manila/{area}/clients/active', [ManilaAreaClientsController::class, 'ManilaAreaActiveClientsPage'])->middleware('secretary.area:manila')->name('secretary.area.manila.clients.active.page');
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

    // SMS
    Route::post('/manila/collect-all-payments/{reference}',[ManilaAreaPaymentsController::class, 'ManilaSecretaryCollectAllPayments'])->middleware('secretary.area:manila')->name('secretary.manila.payments.collect.all');
    Route::post('/manila/remind-payments/{reference}',[ManilaAreaPaymentsController::class, 'ManilaSecretaryRemindPaymentsByReference'])->middleware('secretary.area:manila')->name('secretary.manila.payments.remind.reference');
    Route::post('/manila/no-payment/{reference}',[ManilaAreaPaymentsController::class, 'ManilaSecretaryNoPaymentAll'])->middleware('secretary.area:manila')->name('secretary.manila.payments.no-payment.all');

    // Route::post('/manila/collect-payment/{clientPaymentId}', [ManilaAreaPaymentsController::class, 'ManilaClientCollectPaymentRequest'])->middleware('secretary.area:manila')->name('secretary.manila.payments.clients.collect');
    // Route::post('/manila/remind-payment/{clientPaymentId}', [ManilaAreaPaymentsController::class, 'ManilaClientRemindPaymentRequest'])->middleware('secretary.area:manila')->name('secretary.manila.payments.clients.remind');
    // Route::post('/manila/no-payment/{clientPaymentId}', [ManilaAreaPaymentsController::class, 'ManilaClientNoPaymentRequest'])->middleware('secretary.area:manila')->name('secretary.area.manila.payments.clients.not.paid');

    // Secretary Valenzuela Area Route
    // Secretary Valenzuela Dashboard Route
    Route::get('/valenzuela/dashboard', [ValenzuelaDashboardController::class, 'ValenzuelaDashboardPage'])->middleware('secretary.area:valenzuela')->name('secretary.valenzuela.dashboard.page');
    Route::get('/valenzuela/areas/breakdown/analytics', [ValenzuelaDashboardController::class, 'ValenzuelaAreasBreakdownSummary'])->middleware('secretary.area:valenzuela')->name('secretary.valenzuela.analytics.page');
    Route::get('/valenzuela/notifications/fetch', [ValenzuelaNotificationsController::class, 'ValenzuelaFetchNotifications'])->name('secretary.valenzuela.fetch_notifications');
    Route::get('/valenzuela/notifications', [ValenzuelaNotificationsController::class, 'ValenzuelaNotificationsPage'])->name('secretary.valenzuela.notification.page');
    Route::post('/valenzuela/notifications/mark-all-read', [ValenzuelaNotificationsController::class, 'ValenzuelaMarkAllAsReadNotifications'])->name('secretary.valenzuela.notifications.mark.all.read');


    //Secretary Valenzuela Profile Management Route
    Route::get('/valenzuela/profile', [ValenzuelaProfileController::class, 'ValenzuelaProfilePage'])->name('secretary.valenzuela.profile.page');
    Route::post('/valenzuela/profile/update', [ValenzuelaProfileController::class, 'ValenzuelaUpdateProfile'])->name('secretary.profile.update');

    // Secretary Clients Route
    Route::get('/valenzuela/clients', [ValenzuelaClientsController::class, 'ValenzuelaClientsPage'])->middleware('secretary.area:valenzuela')->name('secretary.valenzuela.clients.page');
    Route::post('/valenzuela/add/clients', [ValenzuelaClientsController::class, 'ValenzuelaAddClientRequest'])->middleware('secretary.area:valenzuela')->name('secretary.valenzuela.add.clients.request');
    Route::get('/valenzuela/edit/clients/{id}', [ValenzuelaClientsController::class, 'ValenzuelaEditClientPage'])->middleware('secretary.area:valenzuela')->name('secretary.valenzuela.edit.clients.page');
    Route::put('/valenzuela/update/clients/{id}', [ValenzuelaClientsController::class, 'ValenzuelaUpdateClientRequest'])->middleware('secretary.area:valenzuela')->name('secretary.valenzuela.update.clients.request');
    Route::delete('/valenzuela/delete/clients/{id}', [ValenzuelaClientsController::class, 'ValenzuelaDeleteClientRequest'])->middleware('secretary.area:valenzuela')->name('secretary.valenzuela.delete.clients.request');
    Route::post('/valenzuela/add/renewal', [ValenzuelaClientsRenewalController::class, 'ValenzuelaClientAddRenewalRequest'])->middleware('secretary.area:valenzuela')->name('secretary.valenzuela.add.renewal.clients.request');

    // Secretary List of Areas Route
    Route::get('/valenzuela/areas/', [ValenzuelaAreaController::class, 'ValenzuelaAreaPage'])->middleware('secretary.area:valenzuela')->name('secretary.valenzuela.area.page');
    Route::get('/valenzuela/areas/sales/print', [ValenzuelaAreaController::class, 'ValenzuelaAreaPrintSalesReports'])->middleware('secretary.area:valenzuela')->name('secretary.area.valenzuela.print.sales');

    //Secretary Clients Accounts
    Route::get('/valenzuela/{area}/clients', [ValenzuelaAreaClientsController::class, 'ValenzuelaAreaClientsPage'])->middleware('secretary.area:valenzuela')->name('secretary.area.valenzuela.clients.page');
    Route::get('/valenzuela/{area}/clients/lapsed', [ValenzuelaAreaClientsController::class, 'ValenzuelaAreaLapsedClientsPage'])->middleware('secretary.area:valenzuela')->name('secretary.area.valenzuela.clients.lapsed.page');
    Route::get('/valenzuela/{area}/clients/renewal', [ValenzuelaAreaClientsController::class, 'ValenzuelaAreaRenewalClientPage'])->middleware('secretary.area:valenzuela')->name('secretary.area.valenzuela.clients.renewal.page');
    Route::get('/valenzuela/{area}/clients/active', [ValenzuelaAreaClientsController::class, 'ValenzuelaAreaActiveClientsPage'])->middleware('secretary.area:valenzuela')->name('secretary.area.valenzuela.clients.active.page');
    Route::get('/valenzuela/{area}/clients/lapsed/print', [ValenzuelaAreaClientsController::class, 'ValenzuelaAreaLapsedClientsPrint'])->middleware('secretary.area:valenzuela')->name('secretary.area.valenzuela.clients.lapsed.page.print');

    //Secretary Clients History
    Route::get('/valenzuela/clients/{clientId}', [ValenzuelaAreaClientsHistoryController::class, 'ValenzuelaAreaClientsProfilePage'])->middleware('secretary.area:valenzuela')->name('secretary.area.valenzuela.clients.profile.page');
    Route::get('/valenzuela/clients/{clientId}/loans/print', [ValenzuelaAreaClientsHistoryController::class, 'ValenzuelaAreaClientsPrintLoanHistory'])->middleware('secretary.area:valenzuela')->name('secretary.area.valenzuela.clients.print.history.page');
    Route::get('/valenzuela/clients/loans/{loanId}/payments', [ValenzuelaAreaClientsHistoryController::class, 'ValenzuelaAreaClientLoanPaymentsPage'])->middleware('secretary.area:valenzuela')->name('secretary.area.valenzuela.clients.loan.payments');

    // Secretary Areas Payments Route
    Route::get('/valenzuela/{area}/payments', [ValenzuelaAreaPaymentsController::class, 'ValenzuelaClientPaymentsPage'])->middleware('secretary.area:valenzuela')->name('secretary.area.valenzuela.payments');
    Route::post('/valenzuela/{id}/create/payments', [ValenzuelaAreaPaymentsController::class, 'ValenzuelaClientPaymentsRequest'])->middleware('secretary.area:valenzuela')->name('secretary.area.valenzuela.payments.request');
    Route::get('/valenzuela/payments/{area}/summary/collections/print', [ValenzuelaAreaPaymentsController::class, 'ValenzuelaPrintSummaryCollections'])->middleware('secretary.area:valenzuela')->name('secretary.area.valenzuela.payments.print.summary.collections');
    Route::get('/valenzuela/payments/{referenceNumber}/clients', [ValenzuelaAreaPaymentsController::class, 'ValenzuelaClientDailyPaymentsPage'])->middleware('secretary.area:valenzuela')->name('secretary.area.valenzuela.payments.clients');
    Route::post('/valenzuela/payments/{id}/update-collection', [ValenzuelaAreaPaymentsController::class, 'ValenzuelaClientUpdateCollection'])->middleware('secretary.area:valenzuela')->name('secretary.area.valenzuela.update.collection');
    Route::get('/valenzuela/payments/{referenceNumber}/print', [ValenzuelaAreaPaymentsController::class, 'ValenzuelaClientPrintDailyPayments'])->middleware('secretary.area:valenzuela')->name('secretary.area.valenzuela.payments.print');

    // SMS
    Route::post('/valenzuela/collect-all-payments/{reference}',[ValenzuelaAreaPaymentsController::class, 'ValenzuelaSecretaryCollectAllPayments'])->middleware('secretary.area:valenzuela')->name('secretary.valenzuela.payments.collect.all');
    Route::post('/valenzuela/remind-payments/{reference}',[ValenzuelaAreaPaymentsController::class, 'ValenzuelaSecretaryRemindPaymentsByReference'])->middleware('secretary.area:valenzuela')->name('secretary.valenzuela.payments.remind.reference');
    Route::post('/valenzuela/no-payment/{reference}',[ValenzuelaAreaPaymentsController::class, 'ValenzuelaSecretaryNoPaymentAll'])->middleware('secretary.area:valenzuela')->name('secretary.valenzuela.payments.no-payment.all');

    // Route::post('/valenzuela/collect-payment/{clientPaymentId}', [ValenzuelaAreaPaymentsController::class, 'ValenzuelaClientCollectPaymentRequest'])->middleware('secretary.area:valenzuela')->name('secretary.valenzuela.payments.clients.collect');
    // Route::post('/valenzuela/remind-payment/{clientPaymentId}', [ValenzuelaAreaPaymentsController::class, 'ValenzuelaClientRemindPaymentRequest'])->middleware('secretary.area:valenzuela')->name('secretary.valenzuela.payments.clients.remind');
    // Route::post('/valenzuela/no-payment/{clientPaymentId}', [ValenzuelaAreaPaymentsController::class, 'ValenzuelaClientNoPaymentRequest'])->middleware('secretary.area:valenzuela')->name('secretary.area.valenzuela.payments.clients.not.paid');


    // Secretary Caloocan Area Route
    // Secretary Caloocan Dashboard Route
    Route::get('/caloocan/dashboard', [CaloocanDashboardController::class, 'CaloocanDashboardPage'])->middleware('secretary.area:caloocan')->name('secretary.caloocan.dashboard.page');
    Route::get('/caloocan/areas/breakdown/analytics', [CaloocanDashboardController::class, 'CaloocanAreasBreakdownSummary'])->middleware('secretary.area:caloocan')->name('secretary.caloocan.analytics.page');
    Route::get('/caloocan/notifications/fetch', [CaloocanNotificationsController::class, 'CaloocanFetchNotifications'])->name('secretary.caloocan.fetch_notifications');
    Route::get('/caloocan/notifications', [CaloocanNotificationsController::class, 'CaloocanNotificationsPage'])->name('secretary.caloocan.notification.page');
    Route::post('/caloocan/notifications/mark-all-read', [CaloocanNotificationsController::class, 'CaloocanMarkAllAsReadNotifications'])->name('secretary.caloocan.notifications.mark.all.read');


    //Secretary Caloocan Profile Management Route
    Route::get('/caloocan/profile', [CaloocanProfileController::class, 'CaloocanProfilePage'])->name('secretary.caloocan.profile.page');
    Route::post('/caloocan/profile/update', [CaloocanProfileController::class, 'CaloocanUpdateProfile'])->name('secretary.profile.update');

    // Secretary Clients Route
    Route::get('/caloocan/clients', [CaloocanClientsController::class, 'CaloocanClientsPage'])->middleware('secretary.area:caloocan')->name('secretary.caloocan.clients.page');
    Route::post('/caloocan/add/clients', [CaloocanClientsController::class, 'CaloocanAddClientRequest'])->middleware('secretary.area:caloocan')->name('secretary.caloocan.add.clients.request');
    Route::get('/caloocan/edit/clients/{id}', [CaloocanClientsController::class, 'CaloocanEditClientPage'])->middleware('secretary.area:caloocan')->name('secretary.caloocan.edit.clients.page');
    Route::put('/caloocan/update/clients/{id}', [CaloocanClientsController::class, 'CaloocanUpdateClientRequest'])->middleware('secretary.area:caloocan')->name('secretary.caloocan.update.clients.request');
    Route::delete('/caloocan/delete/clients/{id}', [CaloocanClientsController::class, 'CaloocanDeleteClientRequest'])->middleware('secretary.area:caloocan')->name('secretary.caloocan.delete.clients.request');
    Route::post('/caloocan/add/renewal', [CaloocanClientsRenewalController::class, 'CaloocanClientAddRenewalRequest'])->middleware('secretary.area:caloocan')->name('secretary.caloocan.add.renewal.clients.request');

    // Secretary List of Areas Route
    Route::get('/caloocan/areas/', [CaloocanAreaController::class, 'CaloocanAreaPage'])->middleware('secretary.area:caloocan')->name('secretary.caloocan.area.page');
    Route::get('/caloocan/areas/sales/print', [CaloocanAreaController::class, 'CaloocanAreaPrintSalesReports'])->middleware('secretary.area:caloocan')->name('secretary.area.caloocan.print.sales');

    //Secretary Clients Accounts
    Route::get('/caloocan/{area}/clients', [CaloocanAreaClientsController::class, 'CaloocanAreaClientsPage'])->middleware('secretary.area:caloocan')->name('secretary.area.caloocan.clients.page');
    Route::get('/caloocan/{area}/clients/lapsed', [CaloocanAreaClientsController::class, 'CaloocanAreaLapsedClientsPage'])->middleware('secretary.area:caloocan')->name('secretary.area.caloocan.clients.lapsed.page');
    Route::get('/caloocan/{area}/clients/renewal', [CaloocanAreaClientsController::class, 'CaloocanAreaRenewalClientPage'])->middleware('secretary.area:caloocan')->name('secretary.area.caloocan.clients.renewal.page');
    Route::get('/caloocan/{area}/clients/active', [CaloocanAreaClientsController::class, 'CaloocanAreaActiveClientsPage'])->middleware('secretary.area:caloocan')->name('secretary.area.caloocan.clients.active.page');
    Route::get('/caloocan/{area}/clients/lapsed/print', [CaloocanAreaClientsController::class, 'CaloocanAreaLapsedClientsPrint'])->middleware('secretary.area:caloocan')->name('secretary.area.caloocan.clients.lapsed.page.print');

    //Secretary Clients History
    Route::get('/caloocan/clients/{clientId}', [CaloocanAreaClientsHistoryController::class, 'CaloocanAreaClientsProfilePage'])->middleware('secretary.area:caloocan')->name('secretary.area.caloocan.clients.profile.page');
    Route::get('/caloocan/clients/{clientId}/loans/print', [CaloocanAreaClientsHistoryController::class, 'CaloocanAreaClientsPrintLoanHistory'])->middleware('secretary.area:caloocan')->name('secretary.area.caloocan.clients.print.history.page');
    Route::get('/caloocan/clients/loans/{loanId}/payments', [CaloocanAreaClientsHistoryController::class, 'CaloocanAreaClientLoanPaymentsPage'])->middleware('secretary.area:caloocan')->name('secretary.area.caloocan.clients.loan.payments');

    // Secretary Areas Payments Route
    Route::get('/caloocan/{area}/payments', [CaloocanAreaPaymentsController::class, 'CaloocanClientPaymentsPage'])->middleware('secretary.area:caloocan')->name('secretary.area.caloocan.payments');
    Route::post('/caloocan/{id}/create/payments', [CaloocanAreaPaymentsController::class, 'CaloocanClientPaymentsRequest'])->middleware('secretary.area:caloocan')->name('secretary.area.caloocan.payments.request');
    Route::get('/caloocan/payments/{area}/summary/collections/print', [CaloocanAreaPaymentsController::class, 'CaloocanPrintSummaryCollections'])->middleware('secretary.area:caloocan')->name('secretary.area.caloocan.payments.print.summary.collections');
    Route::get('/caloocan/payments/{referenceNumber}/clients', [CaloocanAreaPaymentsController::class, 'CaloocanClientDailyPaymentsPage'])->middleware('secretary.area:caloocan')->name('secretary.area.caloocan.payments.clients');
    Route::post('/caloocan/payments/{id}/update-collection', [CaloocanAreaPaymentsController::class, 'CaloocanClientUpdateCollection'])->middleware('secretary.area:caloocan')->name('secretary.area.caloocan.update.collection');
    Route::get('/caloocan/payments/{referenceNumber}/print', [CaloocanAreaPaymentsController::class, 'CaloocanClientPrintDailyPayments'])->middleware('secretary.area:caloocan')->name('secretary.area.caloocan.payments.print');

    // SMS
    Route::post('/caloocan/collect-all-payments/{reference}',[CaloocanAreaPaymentsController::class, 'CaloocanSecretaryCollectAllPayments'])->middleware('secretary.area:caloocan')->name('secretary.caloocan.payments.collect.all');
    Route::post('/caloocan/remind-payments/{reference}',[CaloocanAreaPaymentsController::class, 'CaloocanSecretaryRemindPaymentsByReference'])->middleware('secretary.area:caloocan')->name('secretary.caloocan.payments.remind.reference');
    Route::post('/caloocan/no-payment/{reference}',[CaloocanAreaPaymentsController::class, 'CaloocanSecretaryNoPaymentAll'])->middleware('secretary.area:caloocan')->name('secretary.caloocan.payments.no-payment.all');

    
    // Route::post('/caloocan/collect-payment/{clientPaymentId}', [CaloocanAreaPaymentsController::class, 'CaloocanClientCollectPaymentRequest'])->middleware('secretary.area:caloocan')->name('secretary.caloocan.payments.clients.collect');
    // Route::post('/caloocan/remind-payment/{clientPaymentId}', [CaloocanAreaPaymentsController::class, 'CaloocanClientRemindPaymentRequest'])->middleware('secretary.area:caloocan')->name('secretary.caloocan.payments.clients.remind');
    // Route::post('/caloocan/no-payment/{clientPaymentId}', [CaloocanAreaPaymentsController::class, 'CaloocanClientNoPaymentRequest'])->middleware('secretary.area:caloocan')->name('secretary.area.caloocan.payments.clients.not.paid');

    // Secretary FC Area Route
    // Secretary FC Dashboard Route
    Route::get('/fc/dashboard', [FCDashboardController::class, 'FCDashboardPage'])->middleware('secretary.area:fc')->name('secretary.fc.dashboard.page');
    Route::get('/fc/areas/breakdown/analytics', [FCDashboardController::class, 'FCAreasBreakdownSummary'])->middleware('secretary.area:fc')->name('secretary.fc.analytics.page');
    Route::get('/fc/notifications/fetch', [FCNotificationsController::class, 'FCFetchNotifications'])->name('secretary.fc.fetch_notifications');
    Route::get('/fc/notifications', [FCNotificationsController::class, 'FCNotificationsPage'])->name('secretary.fc.notification.page');
    Route::post('/fc/notifications/mark-all-read', [FCNotificationsController::class, 'FCMarkAllAsReadNotifications'])->name('secretary.fc.notifications.mark.all.read');


    //Secretary FC Profile Management Route
    Route::get('/fc/profile', [FCProfileController::class, 'FCProfilePage'])->name('secretary.fc.profile.page');
    Route::post('/fc/profile/update', [FCProfileController::class, 'FCUpdateProfile'])->name('secretary.profile.update');

    // Secretary Clients Route
    Route::get('/fc/clients', [FCClientsController::class, 'FCClientsPage'])->middleware('secretary.area:fc')->name('secretary.fc.clients.page');
    Route::post('/fc/add/clients', [FCClientsController::class, 'FCAddClientRequest'])->middleware('secretary.area:fc')->name('secretary.fc.add.clients.request');
    Route::get('/fc/edit/clients/{id}', [FCClientsController::class, 'FCEditClientPage'])->middleware('secretary.area:fc')->name('secretary.fc.edit.clients.page');
    Route::put('/fc/update/clients/{id}', [FCClientsController::class, 'FCUpdateClientRequest'])->middleware('secretary.area:fc')->name('secretary.fc.update.clients.request');
    Route::delete('/fc/delete/clients/{id}', [FCClientsController::class, 'FCDeleteClientRequest'])->middleware('secretary.area:fc')->name('secretary.fc.delete.clients.request');
    Route::post('/fc/add/renewal', [FCClientsRenewalController::class, 'FCClientAddRenewalRequest'])->middleware('secretary.area:fc')->name('secretary.fc.add.renewal.clients.request');

    // Secretary List of Areas Route
    Route::get('/fc/areas/', [FCAreaController::class, 'FCAreaPage'])->middleware('secretary.area:fc')->name('secretary.fc.area.page');
    Route::get('/fc/areas/sales/print', [FCAreaController::class, 'FCAreaPrintSalesReports'])->middleware('secretary.area:fc')->name('secretary.area.fc.print.sales');

    //Secretary Clients Accounts
    Route::get('/fc/{area}/clients', [FCAreaClientsController::class, 'FCAreaClientsPage'])->middleware('secretary.area:fc')->name('secretary.area.fc.clients.page');
    Route::get('/fc/{area}/clients/lapsed', [FCAreaClientsController::class, 'FCAreaLapsedClientsPage'])->middleware('secretary.area:fc')->name('secretary.area.fc.clients.lapsed.page');
    Route::get('/fc/{area}/clients/renewal', [FCAreaClientsController::class, 'FCAreaRenewalClientPage'])->middleware('secretary.area:fc')->name('secretary.area.fc.clients.renewal.page');
    Route::get('/fc/{area}/clients/active', [FCAreaClientsController::class, 'FCAreaActiveClientsPage'])->middleware('secretary.area:fc')->name('secretary.area.fc.clients.active.page');
    Route::get('/fc/{area}/clients/lapsed/print', [FCAreaClientsController::class, 'FCAreaLapsedClientsPrint'])->middleware('secretary.area:fc')->name('secretary.area.fc.clients.lapsed.page.print');

    //Secretary Clients History
    Route::get('/fc/clients/{clientId}', [FCAreaClientsHistoryController::class, 'FCAreaClientsProfilePage'])->middleware('secretary.area:fc')->name('secretary.area.fc.clients.profile.page');
    Route::get('/fc/clients/{clientId}/loans/print', [FCAreaClientsHistoryController::class, 'FCAreaClientsPrintLoanHistory'])->middleware('secretary.area:fc')->name('secretary.area.fc.clients.print.history.page');
    Route::get('/fc/clients/loans/{loanId}/payments', [FCAreaClientsHistoryController::class, 'FCAreaClientLoanPaymentsPage'])->middleware('secretary.area:fc')->name('secretary.area.fc.clients.loan.payments');

    // Secretary Areas Payments Route
    Route::get('/fc/{area}/payments', [FCAreaPaymentsController::class, 'FCClientPaymentsPage'])->middleware('secretary.area:fc')->name('secretary.area.fc.payments');
    Route::post('/fc/{id}/create/payments', [FCAreaPaymentsController::class, 'FCClientPaymentsRequest'])->middleware('secretary.area:fc')->name('secretary.area.fc.payments.request');
    Route::get('/fc/payments/{area}/summary/collections/print', [FCAreaPaymentsController::class, 'FCPrintSummaryCollections'])->middleware('secretary.area:fc')->name('secretary.area.fc.payments.print.summary.collections');
    Route::get('/fc/payments/{referenceNumber}/clients', [FCAreaPaymentsController::class, 'FCClientDailyPaymentsPage'])->middleware('secretary.area:fc')->name('secretary.area.fc.payments.clients');
    Route::post('/fc/payments/{id}/update-collection', [FCAreaPaymentsController::class, 'FCClientUpdateCollection'])->middleware('secretary.area:fc')->name('secretary.area.fc.update.collection');
    Route::get('/fc/payments/{referenceNumber}/print', [FCAreaPaymentsController::class, 'FCClientPrintDailyPayments'])->middleware('secretary.area:fc')->name('secretary.area.fc.payments.print');

    // SMS
    Route::post('/fc/collect-all-payments/{reference}',[FCAreaPaymentsController::class, 'FCSecretaryCollectAllPayments'])->middleware('secretary.area:fc')->name('secretary.fc.payments.collect.all');
    Route::post('/fc/remind-payments/{reference}',[FCAreaPaymentsController::class, 'FCSecretaryRemindPaymentsByReference'])->middleware('secretary.area:fc')->name('secretary.fc.payments.remind.reference');
    Route::post('/fc/no-payment/{reference}',[FCAreaPaymentsController::class, 'FCSecretaryNoPaymentAll'])->middleware('secretary.area:fc')->name('secretary.fc.payments.no-payment.all');


    // Route::post('/fc/collect-payment/{clientPaymentId}', [FCAreaPaymentsController::class, 'FCClientCollectPaymentRequest'])->middleware('secretary.area:fc')->name('secretary.fc.payments.clients.collect');
    // Route::post('/fc/remind-payment/{clientPaymentId}', [FCAreaPaymentsController::class, 'FCClientRemindPaymentRequest'])->middleware('secretary.area:fc')->name('secretary.fc.payments.clients.remind');
    // Route::post('/fc/no-payment/{clientPaymentId}', [FCAreaPaymentsController::class, 'FCClientNoPaymentRequest'])->middleware('secretary.area:fc')->name('secretary.area.fc.payments.clients.not.paid');
});



// Collector Route
Route::middleware(['auth:collector'])->group(function () {

    Route::get('/collector/manila', [ManilaCollectorDashboardController::class, 'ManilaCollectorDashboardPage'])->middleware('collector.area:manila')->name('collector.manila.dashboard.page');
    Route::get('/collector/manila/collections/{area}', [ManilaCollectorPaymentController::class, 'ManilaCollectorCollectionsPage'])->middleware('collector.area:manila')->name('collector.manila.collections.page');
    Route::get('/collector/manila/collections/reference/{referenceNumber}', [ManilaCollectorPaymentController::class, 'ManilaCollectorClientPaymentPage'])->middleware('collector.area:manila')->name('collector.manila.collections.payments');
    Route::post('/collector/manila/collections/collect-payment/{clientPaymendId}', [ManilaCollectorPaymentController::class, 'ManilaCollectorCollectRequest'])->middleware('collector.area:manila')->name('collector.manila.collections.payments.collect.request');

    Route::get('/collector/valenzuela', [ValenzuelaCollectorDashboardController::class, 'ValenzuelaCollectorDashboardPage'])->middleware('collector.area:valenzuela')->name('collector.valenzuela.dashboard.page');
    Route::get('/collector/valenzuela/collections/{area}', [ValenzuelaCollectorPaymentController::class, 'ValenzuelaCollectorCollectionsPage'])->middleware('collector.area:valenzuela')->name('collector.valenzuela.collections.page');
    Route::get('/collector/valenzuela/collections/reference/{referenceNumber}', [ValenzuelaCollectorPaymentController::class, 'ValenzuelaCollectorClientPaymentPage'])->middleware('collector.area:valenzuela')->name('collector.valenzuela.collections.payments');
    Route::post('/collector/valenzuela/collections/collect-payment/{clientPaymendId}', [ValenzuelaCollectorPaymentController::class, 'ValenzuelaCollectorCollectRequest'])->middleware('collector.area:valenzuela')->name('collector.valenzuela.collections.payments.collect.request');

    Route::get('/collector/caloocan', [CaloocanCollectorDashboardController::class, 'CaloocanCollectorDashboardPage'])->middleware('collector.area:caloocan')->name('collector.caloocan.dashboard.page');
    Route::get('/collector/caloocan/collections/{area}', [CaloocanCollectorPaymentController::class, 'CaloocanCollectorCollectionsPage'])->middleware('collector.area:caloocan')->name('collector.caloocan.collections.page');
    Route::get('/collector/caloocan/collections/reference/{referenceNumber}', [CaloocanCollectorPaymentController::class, 'CaloocanCollectorClientPaymentPage'])->middleware('collector.area:caloocan')->name('collector.caloocan.collections.payments');
    Route::post('/collector/caloocan/collections/collect-payment/{clientPaymendId}', [CaloocanCollectorPaymentController::class, 'CaloocanCollectorCollectRequest'])->middleware('collector.area:caloocan')->name('collector.caloocan.collections.payments.collect.request');

    Route::get('/collector/fc', [FCCollectorDashboardController::class, 'FCCollectorDashboardPage'])->middleware('collector.area:fc')->name('collector.fc.dashboard.page');
    Route::get('/collector/fc/collections/{area}', [FCCollectorPaymentController::class, 'FCCollectorCollectionsPage'])->middleware('collector.area:fc')->name('collector.fc.collections.page');
    Route::get('/collector/fc/collections/reference/{referenceNumber}', [FCCollectorPaymentController::class, 'FCCollectorClientPaymentPage'])->middleware('collector.area:fc')->name('collector.fc.collections.payments');
    Route::post('/collector/fc/collections/collect-payment/{clientPaymendId}', [FCCollectorPaymentController::class, 'FCCollectorCollectRequest'])->middleware('collector.area:fc')->name('collector.fc.collections.payments.collect.request');
});
