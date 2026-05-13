<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::get('/track', [App\Http\Controllers\TrackingController::class, 'index'])->name('tracking.index');
Route::get('/track/result', [App\Http\Controllers\TrackingController::class, 'track'])->name('tracking.result');

Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

    // ─── Users ───────────────────────────────────────────────
    // Create routes first (must be before {user} to avoid route conflict)
    Route::middleware(['permission:create users'])->group(function () {
        Route::get('users/create', [App\Http\Controllers\UserController::class, 'create'])->name('users.create');
        Route::post('users', [App\Http\Controllers\UserController::class, 'store'])->name('users.store');
    });
    Route::middleware(['permission:view users'])->group(function () {
        Route::get('users', [App\Http\Controllers\UserController::class, 'index'])->name('users.index');
        Route::get('users/{user}', [App\Http\Controllers\UserController::class, 'show'])->name('users.show');
    });
    Route::middleware(['permission:edit users'])->group(function () {
        Route::get('users/{user}/edit', [App\Http\Controllers\UserController::class, 'edit'])->name('users.edit');
        Route::put('users/{user}', [App\Http\Controllers\UserController::class, 'update'])->name('users.update');
    });
    Route::middleware(['permission:delete users'])->group(function () {
        Route::delete('users/{user}', [App\Http\Controllers\UserController::class, 'destroy'])->name('users.destroy');
    });

    // ─── Roles ───────────────────────────────────────────────
    Route::middleware(['permission:create roles'])->group(function () {
        Route::get('roles/create', [App\Http\Controllers\RoleController::class, 'create'])->name('roles.create');
        Route::post('roles', [App\Http\Controllers\RoleController::class, 'store'])->name('roles.store');
    });
    Route::middleware(['permission:view roles'])->group(function () {
        Route::get('roles', [App\Http\Controllers\RoleController::class, 'index'])->name('roles.index');
        Route::get('roles/{role}', [App\Http\Controllers\RoleController::class, 'show'])->name('roles.show');
    });
    Route::middleware(['permission:edit roles'])->group(function () {
        Route::get('roles/{role}/edit', [App\Http\Controllers\RoleController::class, 'edit'])->name('roles.edit');
        Route::put('roles/{role}', [App\Http\Controllers\RoleController::class, 'update'])->name('roles.update');
    });
    Route::middleware(['permission:delete roles'])->group(function () {
        Route::delete('roles/{role}', [App\Http\Controllers\RoleController::class, 'destroy'])->name('roles.destroy');
    });

    // ─── Settings ────────────────────────────────────────────
    Route::middleware(['permission:manage settings'])->group(function () {
        Route::get('settings', [App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');
        Route::put('settings', [App\Http\Controllers\SettingController::class, 'update'])->name('settings.update');
    });

    // ─── Clients ─────────────────────────────────────────────
    Route::middleware(['permission:create clients'])->group(function () {
        Route::get('clients/create', [App\Http\Controllers\ClientController::class, 'create'])->name('clients.create');
        Route::post('clients', [App\Http\Controllers\ClientController::class, 'store'])->name('clients.store');
        Route::post('clients/import', [App\Http\Controllers\ClientImportController::class, 'import'])->name('clients.import.process');
        Route::get('clients/import/template', [App\Http\Controllers\ClientImportController::class, 'downloadTemplate'])->name('clients.import.template');
        Route::post('clients/quick-add', [App\Http\Controllers\ClientController::class, 'quickStore'])->name('clients.quick-store');
    });
    Route::middleware(['permission:view clients'])->group(function () {
        Route::get('clients', [App\Http\Controllers\ClientController::class, 'index'])->name('clients.index');
        Route::get('clients/import', [App\Http\Controllers\ClientImportController::class, 'showImportForm'])->name('clients.import');
        Route::get('clients/search', [App\Http\Controllers\ClientController::class, 'search'])->name('clients.search');
        Route::get('clients/{client}', [App\Http\Controllers\ClientController::class, 'show'])->name('clients.show');
    });
    Route::middleware(['permission:edit clients'])->group(function () {
        Route::get('clients/{client}/edit', [App\Http\Controllers\ClientController::class, 'edit'])->name('clients.edit');
        Route::put('clients/{client}', [App\Http\Controllers\ClientController::class, 'update'])->name('clients.update');
    });
    Route::middleware(['permission:delete clients'])->group(function () {
        Route::delete('clients/{client}', [App\Http\Controllers\ClientController::class, 'destroy'])->name('clients.destroy');
    });

    // ─── Air Cargo ───────────────────────────────────────────
    Route::middleware(['permission:view air cargo dashboard'])->group(function () {
        Route::get('air-cargo/dashboard', [App\Http\Controllers\AirCargoController::class, 'dashboard'])->name('air-cargo.dashboard');
    });
    Route::middleware(['permission:view air cargo'])->group(function () {
        Route::get('air-cargo', [App\Http\Controllers\AirCargoController::class, 'index'])->name('air-cargo.index');
        Route::get('air-cargo/{air_cargo}', [App\Http\Controllers\AirCargoController::class, 'show'])->name('air-cargo.show');
    });
    Route::middleware(['permission:create air cargo'])->group(function () {
        Route::get('air-cargo/create', [App\Http\Controllers\AirCargoController::class, 'create'])->name('air-cargo.create');
        Route::post('air-cargo', [App\Http\Controllers\AirCargoController::class, 'store'])->name('air-cargo.store');
    });
    Route::middleware(['permission:edit air cargo'])->group(function () {
        Route::get('air-cargo/{air_cargo}/edit', [App\Http\Controllers\AirCargoController::class, 'edit'])->name('air-cargo.edit');
        Route::put('air-cargo/{air_cargo}', [App\Http\Controllers\AirCargoController::class, 'update'])->name('air-cargo.update');
    });
    Route::middleware(['permission:delete air cargo'])->group(function () {
        Route::delete('air-cargo/{air_cargo}', [App\Http\Controllers\AirCargoController::class, 'destroy'])->name('air-cargo.destroy');
    });

    // ─── Sea Cargo ───────────────────────────────────────────
    Route::middleware(['permission:view sea cargo dashboard'])->group(function () {
        Route::get('sea-cargo/dashboard', [App\Http\Controllers\SeaCargoController::class, 'dashboard'])->name('sea-cargo.dashboard');
    });
    Route::middleware(['permission:view sea cargo'])->group(function () {
        Route::get('sea-cargo', [App\Http\Controllers\SeaCargoController::class, 'index'])->name('sea-cargo.index');
        Route::get('sea-cargo/{sea_cargo}', [App\Http\Controllers\SeaCargoController::class, 'show'])->name('sea-cargo.show');
    });
    Route::middleware(['permission:create sea cargo'])->group(function () {
        Route::get('sea-cargo/create', [App\Http\Controllers\SeaCargoController::class, 'create'])->name('sea-cargo.create');
        Route::post('sea-cargo', [App\Http\Controllers\SeaCargoController::class, 'store'])->name('sea-cargo.store');
    });
    Route::middleware(['permission:edit sea cargo'])->group(function () {
        Route::get('sea-cargo/{sea_cargo}/edit', [App\Http\Controllers\SeaCargoController::class, 'edit'])->name('sea-cargo.edit');
        Route::put('sea-cargo/{sea_cargo}', [App\Http\Controllers\SeaCargoController::class, 'update'])->name('sea-cargo.update');
    });
    Route::middleware(['permission:delete sea cargo'])->group(function () {
        Route::delete('sea-cargo/{sea_cargo}', [App\Http\Controllers\SeaCargoController::class, 'destroy'])->name('sea-cargo.destroy');
    });

    // ─── Shipments ───────────────────────────────────────────
    Route::middleware(['permission:create shipments'])->group(function () {
        Route::get('shipments/create', [App\Http\Controllers\ShipmentController::class, 'create'])->name('shipments.create');
        Route::post('shipments', [App\Http\Controllers\ShipmentController::class, 'store'])->name('shipments.store');
    });
    Route::middleware(['permission:view shipments'])->group(function () {
        Route::get('shipments', [App\Http\Controllers\ShipmentController::class, 'index'])->name('shipments.index');
        Route::get('shipments/{shipment}', [App\Http\Controllers\ShipmentController::class, 'show'])->name('shipments.show');
        Route::get('shipments/{shipment}/label', [App\Http\Controllers\ShipmentController::class, 'label'])->name('shipments.label');
        Route::get('shipments/{shipment}/invoice', [App\Http\Controllers\ShipmentController::class, 'invoice'])->name('shipments.invoice');
    });
    Route::middleware(['permission:edit shipments'])->group(function () {
        Route::get('shipments/{shipment}/edit', [App\Http\Controllers\ShipmentController::class, 'edit'])->name('shipments.edit');
        Route::put('shipments/{shipment}', [App\Http\Controllers\ShipmentController::class, 'update'])->name('shipments.update');
    });
    Route::middleware(['permission:delete shipments'])->group(function () {
        Route::delete('shipments/{shipment}', [App\Http\Controllers\ShipmentController::class, 'destroy'])->name('shipments.destroy');
    });

    // ─── Invoices ────────────────────────────────────────────
    Route::middleware(['permission:create invoices'])->group(function () {
        Route::get('invoices/create', [App\Http\Controllers\InvoiceController::class, 'create'])->name('invoices.create');
        Route::post('invoices', [App\Http\Controllers\InvoiceController::class, 'store'])->name('invoices.store');
        Route::post('invoices/{invoice}/storage-fee', [App\Http\Controllers\InvoiceController::class, 'addStorageFee'])->name('invoices.storage-fee');
    });
    Route::middleware(['permission:view invoices'])->group(function () {
        Route::get('invoices', [App\Http\Controllers\InvoiceController::class, 'index'])->name('invoices.index');
        Route::get('invoices/{invoice}', [App\Http\Controllers\InvoiceController::class, 'show'])->name('invoices.show');
        Route::get('invoices/{invoice}/pdf', [App\Http\Controllers\InvoiceController::class, 'generatePDF'])->name('invoices.pdf');
        Route::post('invoices/{invoice}/send', [App\Http\Controllers\InvoiceController::class, 'sendInvoice'])->name('invoices.send');
    });
    Route::middleware(['permission:edit invoices'])->group(function () {
        Route::get('invoices/{invoice}/edit', [App\Http\Controllers\InvoiceController::class, 'edit'])->name('invoices.edit');
        Route::put('invoices/{invoice}', [App\Http\Controllers\InvoiceController::class, 'update'])->name('invoices.update');
    });
    Route::middleware(['permission:delete invoices'])->group(function () {
        Route::delete('invoices/{invoice}', [App\Http\Controllers\InvoiceController::class, 'destroy'])->name('invoices.destroy');
    });

    // ─── Payments ───────────────────────────────────────────
    Route::middleware(['permission:create payments'])->group(function () {
        Route::post('payments', [App\Http\Controllers\PaymentController::class, 'store'])->name('payments.store');
        Route::post('payments/{payment}/send', [App\Http\Controllers\PaymentController::class, 'sendReceipt'])->name('payments.send');
        Route::post('invoices/{invoice}/payments', [App\Http\Controllers\PaymentController::class, 'store'])->name('invoices.payments.store');
    });
    Route::middleware(['permission:view payments'])->group(function () {
        Route::get('payments', [App\Http\Controllers\PaymentController::class, 'index'])->name('payments.index');
        Route::get('payments/{payment}', [App\Http\Controllers\PaymentController::class, 'show'])->name('payments.show');
        Route::get('payments/{payment}/receipt', [App\Http\Controllers\PaymentController::class, 'generateReceipt'])->name('payments.receipt');
    });
    Route::middleware(['permission:edit payments'])->group(function () {
        Route::get('payments/{payment}/edit', [App\Http\Controllers\PaymentController::class, 'edit'])->name('payments.edit');
        Route::put('payments/{payment}', [App\Http\Controllers\PaymentController::class, 'update'])->name('payments.update');
    });
    Route::middleware(['permission:delete payments'])->group(function () {
        Route::delete('payments/{payment}', [App\Http\Controllers\PaymentController::class, 'destroy'])->name('payments.destroy');
    });

    // ─── Transactions ───────────────────────────────────────
    Route::middleware(['permission:create transactions'])->group(function () {
        Route::get('transactions/create', [App\Http\Controllers\TransactionController::class, 'create'])->name('transactions.create');
        Route::post('transactions', [App\Http\Controllers\TransactionController::class, 'store'])->name('transactions.store');
    });
    Route::middleware(['permission:view transactions'])->group(function () {
        Route::get('transactions', [App\Http\Controllers\TransactionController::class, 'index'])->name('transactions.index');
        Route::get('transactions/{transaction}', [App\Http\Controllers\TransactionController::class, 'show'])->name('transactions.show');
    });
    Route::middleware(['permission:delete transactions'])->group(function () {
        Route::delete('transactions/{transaction}', [App\Http\Controllers\TransactionController::class, 'destroy'])->name('transactions.destroy');
    });

    // ─── Shipment Status Updates ─────────────────────────────
    Route::middleware(['permission:manage status updates'])->group(function () {
        Route::post('shipment-status-updates', [App\Http\Controllers\ShipmentStatusUpdateController::class, 'store'])->name('shipment-status-updates.store');
    });

    // ─── Broadcast ───────────────────────────────────────────
    Route::middleware(['permission:send broadcast'])->group(function () {
        Route::get('broadcast', [App\Http\Controllers\BroadcastController::class, 'index'])->name('broadcast.index');
        Route::post('broadcast/send', [App\Http\Controllers\BroadcastController::class, 'send'])->name('broadcast.send');
    });

    // ─── Activity Logs ─────────────────────────────────────────
    Route::middleware(['permission:view activity logs'])->group(function () {
        Route::get('activity-logs', [App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('activity-logs.index');
        Route::get('activity-logs/{activity_log}', [App\Http\Controllers\Admin\ActivityLogController::class, 'show'])->name('activity-logs.show');
    });

    // ─── Notifications ────────────────────────────────────────
    Route::middleware(['auth'])->group(function () {
        Route::get('notifications', [App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('notifications.index');
        Route::get('notifications/unread', [App\Http\Controllers\Admin\NotificationController::class, 'getUnread'])->name('notifications.unread');
        Route::post('notifications/{notification}/read', [App\Http\Controllers\Admin\NotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::post('notifications/mark-all-read', [App\Http\Controllers\Admin\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
        Route::delete('notifications/clear-all', [App\Http\Controllers\Admin\NotificationController::class, 'clearAll'])->name('notifications.clear-all');
        Route::delete('notifications/{notification}', [App\Http\Controllers\Admin\NotificationController::class, 'destroy'])->name('notifications.destroy');
    });

    // ─── Reports ─────────────────────────────────────────────
    Route::middleware(['permission:view reports'])->group(function () {
        Route::get('reports', [App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/shipments', [App\Http\Controllers\ReportController::class, 'shipments'])->name('reports.shipments');
        Route::get('reports/clients', [App\Http\Controllers\ReportController::class, 'clients'])->name('reports.clients');
        Route::get('reports/analytics', [App\Http\Controllers\ReportController::class, 'analytics'])->name('reports.analytics');
        Route::get('reports/expenses', [App\Http\Controllers\ReportController::class, 'expenses'])->name('reports.expenses');
        Route::get('reports/shipments/pdf', [App\Http\Controllers\ReportController::class, 'exportShipmentsPdf'])->name('reports.shipments.pdf');
        Route::get('reports/clients/pdf', [App\Http\Controllers\ReportController::class, 'exportClientsPdf'])->name('reports.clients.pdf');
        Route::get('reports/revenue', [App\Http\Controllers\ReportController::class, 'revenue'])->name('reports.revenue');
        Route::get('reports/batch-revenue', [App\Http\Controllers\ReportController::class, 'batchRevenue'])->name('reports.batch-revenue');
        Route::get('reports/payments', [App\Http\Controllers\ReportController::class, 'payments'])->name('reports.payments');
        Route::get('reports/outstanding', [App\Http\Controllers\ReportController::class, 'outstanding'])->name('reports.outstanding');
    });

    // ─── Batches ─────────────────────────────────────────────
    Route::middleware(['permission:create batches'])->group(function () {
        Route::get('batches/create', [App\Http\Controllers\ShipmentBatchController::class, 'create'])->name('batches.create');
        Route::post('batches', [App\Http\Controllers\ShipmentBatchController::class, 'store'])->name('batches.store');
        Route::post('batches/save-draft', [App\Http\Controllers\ShipmentBatchController::class, 'saveDraft'])->name('batches.save-draft');
        Route::delete('batches/clear-draft', [App\Http\Controllers\ShipmentBatchController::class, 'clearDraft'])->name('batches.clear-draft');
    });
    Route::middleware(['permission:view batches'])->group(function () {
        Route::get('batches', [App\Http\Controllers\ShipmentBatchController::class, 'index'])->name('batches.index');
        Route::get('batches/{batch}', [App\Http\Controllers\ShipmentBatchController::class, 'show'])->name('batches.show');
        Route::get('batches/{batch}/packing-list', [App\Http\Controllers\ShipmentBatchController::class, 'generatePackingList'])->name('batches.packing-list');
        Route::get('batches/load-draft', [App\Http\Controllers\ShipmentBatchController::class, 'loadDraft'])->name('batches.load-draft');
    });
    Route::middleware(['permission:edit batches'])->group(function () {
        Route::get('batches/{batch}/edit', [App\Http\Controllers\ShipmentBatchController::class, 'edit'])->name('batches.edit');
        Route::put('batches/{batch}', [App\Http\Controllers\ShipmentBatchController::class, 'update'])->name('batches.update');
        Route::post('batches/{batch}/update-status', [App\Http\Controllers\ShipmentBatchController::class, 'updateStatus'])->name('batches.update-status');
        Route::post('batches/{batch}/add-shipment', [App\Http\Controllers\ShipmentBatchController::class, 'addShipment'])->name('batches.add-shipment');
        Route::post('batches/{batch}/expenses', [App\Http\Controllers\Admin\BatchExpenseController::class, 'store'])->name('batches.expenses.store');
    });
    Route::middleware(['permission:delete batches'])->group(function () {
        Route::delete('batches/{batch}', [App\Http\Controllers\ShipmentBatchController::class, 'destroy'])->name('batches.destroy');
        Route::delete('batches/{batch}/shipments/{shipment}', [App\Http\Controllers\ShipmentBatchController::class, 'removeShipment'])->name('batches.remove-shipment');
        Route::delete('batches/{batch}/storage-fees', [App\Http\Controllers\ShipmentBatchController::class, 'removeStorageFees'])->name('batches.remove-storage-fees');
        Route::delete('batches/{batch}/expenses/{expense}', [App\Http\Controllers\Admin\BatchExpenseController::class, 'destroy'])->name('batches.expenses.destroy');
    });

    // ─── Expense Categories ──────────────────────────────────
    Route::middleware(['permission:create expense categories'])->group(function () {
        Route::get('expense-categories/create', [App\Http\Controllers\ExpenseCategoryController::class, 'create'])->name('expense-categories.create');
        Route::post('expense-categories', [App\Http\Controllers\ExpenseCategoryController::class, 'store'])->name('expense-categories.store');
    });
    Route::middleware(['permission:view expense categories'])->group(function () {
        Route::get('expense-categories', [App\Http\Controllers\ExpenseCategoryController::class, 'index'])->name('expense-categories.index');
        Route::get('expense-categories/{expense_category}', [App\Http\Controllers\ExpenseCategoryController::class, 'show'])->name('expense-categories.show');
    });
    Route::middleware(['permission:edit expense categories'])->group(function () {
        Route::get('expense-categories/{expense_category}/edit', [App\Http\Controllers\ExpenseCategoryController::class, 'edit'])->name('expense-categories.edit');
        Route::put('expense-categories/{expense_category}', [App\Http\Controllers\ExpenseCategoryController::class, 'update'])->name('expense-categories.update');
    });
    Route::middleware(['permission:delete expense categories'])->group(function () {
        Route::delete('expense-categories/{expense_category}', [App\Http\Controllers\ExpenseCategoryController::class, 'destroy'])->name('expense-categories.destroy');
    });

    // ─── Expenses ───────────────────────────────────────────
    Route::middleware(['permission:create expenses'])->group(function () {
        Route::get('expenses/create', [App\Http\Controllers\ExpenseController::class, 'create'])->name('expenses.create');
        Route::post('expenses', [App\Http\Controllers\ExpenseController::class, 'store'])->name('expenses.store');
    });
    Route::middleware(['permission:view expenses'])->group(function () {
        Route::get('expenses', [App\Http\Controllers\ExpenseController::class, 'index'])->name('expenses.index');
        Route::get('expenses/{expense}', [App\Http\Controllers\ExpenseController::class, 'show'])->name('expenses.show');
        Route::get('expenses/{expense}/receipt', [App\Http\Controllers\ExpenseController::class, 'generateReceipt'])->name('expenses.receipt');
    });
    Route::middleware(['permission:edit expenses'])->group(function () {
        Route::get('expenses/{expense}/edit', [App\Http\Controllers\ExpenseController::class, 'edit'])->name('expenses.edit');
        Route::put('expenses/{expense}', [App\Http\Controllers\ExpenseController::class, 'update'])->name('expenses.update');
    });
    Route::middleware(['permission:delete expenses'])->group(function () {
        Route::delete('expenses/{expense}', [App\Http\Controllers\ExpenseController::class, 'destroy'])->name('expenses.destroy');
    });
    Route::middleware(['permission:approve expenses'])->group(function () {
        Route::post('expenses/{expense}/approve', [App\Http\Controllers\ExpenseController::class, 'approve'])->name('expenses.approve');
        Route::post('expenses/{expense}/reject', [App\Http\Controllers\ExpenseController::class, 'reject'])->name('expenses.reject');
        Route::post('expenses/{expense}/mark-paid', [App\Http\Controllers\ExpenseController::class, 'markAsPaid'])->name('expenses.mark-paid');
    });
});

    // ─── Chat (Outside Admin Group) ─────────────────────────────
    Route::middleware(['auth'])->prefix('chat')->name('chat.')->group(function () {
        Route::get('/', [App\Http\Controllers\ChatController::class, 'index'])->name('index');
        Route::get('/users', [App\Http\Controllers\ChatController::class, 'getUsers'])->name('users');
        Route::get('/unread-count', [App\Http\Controllers\ChatController::class, 'getUnreadCount'])->name('unread-count');
        Route::get('/conversations', [App\Http\Controllers\ChatController::class, 'getConversations'])->name('conversations');
        Route::get('/{conversation:uuid}', [App\Http\Controllers\ChatController::class, 'show'])->name('show');
        Route::get('/{conversation:uuid}/messages', [App\Http\Controllers\ChatController::class, 'getMessages'])->name('messages');
        Route::post('/{conversation:uuid}/send', [App\Http\Controllers\ChatController::class, 'sendMessage'])->name('send');
        Route::post('/{conversation:uuid}/read', [App\Http\Controllers\ChatController::class, 'markAsRead'])->name('read');
        Route::get('/user/{user}', [App\Http\Controllers\ChatController::class, 'startConversation'])->name('user');
    });

require __DIR__.'/auth.php';
