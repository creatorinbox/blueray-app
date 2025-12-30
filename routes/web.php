<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\GrnController;
use App\Http\Controllers\SalesReturnController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\ExpenseSubCategoryController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\DeliveryNoteController;
use App\Http\Controllers\JobCardController;

Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes();

Route::middleware('auth')->group(function () {
                // Delivery Order routes
                // Keep URI as 'delivery-orders' but expose route NAMES using underscores so controllers can use delivery_orders.*
                Route::resource('delivery-orders', App\Http\Controllers\DeliveryOrderController::class)->names([
                    'index' => 'delivery_orders.index',
                    'create' => 'delivery_orders.create',
                    'store' => 'delivery_orders.store',
                    'show' => 'delivery_orders.show',
                    'edit' => 'delivery_orders.edit',
                    'update' => 'delivery_orders.update',
                    'destroy' => 'delivery_orders.destroy',
                ]);
                Route::post('delivery-orders/{deliveryOrder}/complete', [App\Http\Controllers\DeliveryOrderController::class, 'complete'])->name('delivery_orders.complete');
                // Print copies: store and customer
                Route::get('delivery-orders/{delivery_order}/print-store', [App\Http\Controllers\DeliveryOrderController::class, 'printStore'])->name('delivery_orders.print_store');
                Route::get('delivery-orders/{delivery_order}/print-customer', [App\Http\Controllers\DeliveryOrderController::class, 'printCustomer'])->name('delivery_orders.print_customer');
                // Compatibility aliases: some views/controllers may still reference hyphenated route names.
                Route::get('delivery-orders/create', [App\Http\Controllers\DeliveryOrderController::class, 'create'])->name('delivery-orders.create');
                Route::get('delivery-orders', [App\Http\Controllers\DeliveryOrderController::class, 'index'])->name('delivery-orders.index');
                // Compatibility hyphenated names for show/edit/update/destroy and complete
                Route::get('delivery-orders/{delivery_order}', [App\Http\Controllers\DeliveryOrderController::class, 'show'])->name('delivery-orders.show');
                Route::get('delivery-orders/{delivery_order}/edit', [App\Http\Controllers\DeliveryOrderController::class, 'edit'])->name('delivery-orders.edit');
                Route::match(['put','patch'], 'delivery-orders/{delivery_order}', [App\Http\Controllers\DeliveryOrderController::class, 'update'])->name('delivery-orders.update');
                Route::delete('delivery-orders/{delivery_order}', [App\Http\Controllers\DeliveryOrderController::class, 'destroy'])->name('delivery-orders.destroy');
                Route::post('delivery-orders/{deliveryOrder}/complete', [App\Http\Controllers\DeliveryOrderController::class, 'complete'])->name('delivery-orders.complete');
                // Also expose underscored route names for compatibility
                Route::get('delivery-orders', [App\Http\Controllers\DeliveryOrderController::class, 'index'])->name('delivery_orders.index');
                Route::get('delivery-orders/create', [App\Http\Controllers\DeliveryOrderController::class, 'create'])->name('delivery_orders.create');
                Route::post('delivery-orders', [App\Http\Controllers\DeliveryOrderController::class, 'store'])->name('delivery_orders.store');
                Route::get('delivery-orders/{delivery_order}', [App\Http\Controllers\DeliveryOrderController::class, 'show'])->name('delivery_orders.show');
                Route::get('delivery-orders/{delivery_order}/edit', [App\Http\Controllers\DeliveryOrderController::class, 'edit'])->name('delivery_orders.edit');
                Route::match(['put','patch'], 'delivery-orders/{delivery_order}', [App\Http\Controllers\DeliveryOrderController::class, 'update'])->name('delivery_orders.update');
                Route::delete('delivery-orders/{delivery_order}', [App\Http\Controllers\DeliveryOrderController::class, 'destroy'])->name('delivery_orders.destroy');
                Route::post('delivery-orders/{deliveryOrder}/complete', [App\Http\Controllers\DeliveryOrderController::class, 'complete'])->name('delivery_orders.complete');
            // Sales Payment Report (page and AJAX)
            Route::get('reports/sales-payment-report', [ReportsController::class, 'salesPaymentReport'])->name('reports.sales-payment-report');
            Route::get('reports/sales-payment-report/data', [ReportsController::class, 'salesPaymentReportData'])->name('reports.sales-payment-report.data');
        // AMC Service routes
        Route::resource('amc-services', App\Http\Controllers\AmcServiceController::class);
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('dashboard');
    
    // API routes (accessible within auth but for AJAX calls)
    Route::get('api/items', [DeliveryNoteController::class, 'getItems'])->name('api.items');
    Route::get('api/lots', [App\Http\Controllers\ItemController::class, 'apiSearchLots'])->name('api.lots.search');
    Route::get('api/test-items', function() {
        $items = \App\Models\Item::limit(10)->get(['id', 'item_name', 'item_code', 'sale_price']);
        return response()->json(['count' => $items->count(), 'items' => $items]);
    });
    
    // Quotation routes
    Route::resource('quotations', QuotationController::class);
    Route::post('quotations/{quotation}/submit', [QuotationController::class, 'submit'])->name('quotations.submit');
    Route::post('quotations/{quotation}/approve', [QuotationController::class, 'approve'])->name('quotations.approve');
    Route::post('quotations/{quotation}/reject', [QuotationController::class, 'reject'])->name('quotations.reject');
    Route::get('quotations/{quotation}/print', [QuotationController::class, 'print'])->name('quotations.print');
    
    // Invoice/Sales routes
    Route::get('sales/invoice/{quotation_id}', [InvoiceController::class, 'createFromQuotation'])->name('sales.create_from_quotation');
    Route::post('sales/invoice', [InvoiceController::class, 'storeFromQuotation'])->name('sales.store_from_quotation');
    
    // Sales listing routes
    Route::get('sales', [SalesController::class, 'index'])->name('sales.index');
    Route::get('sales/{id}', [SalesController::class, 'show'])->name('sales.show');
    Route::get('sales/{id}/print', [SalesController::class, 'print'])->name('sales.print');
    Route::post('sales/{id}/approve-delivery', [SalesController::class, 'approveDelivery'])->name('sales.approve_delivery');
    Route::delete('sales/{id}', [SalesController::class, 'destroy'])->name('sales.destroy');
    
    // Purchase Order routes
    Route::resource('purchase-orders', PurchaseOrderController::class);
    Route::get('purchase-orders/{id}/print', [PurchaseOrderController::class, 'print'])->name('purchase-orders.print');

    // GRN payment routes
    Route::post('grns/{grn}/payments', [GrnController::class, 'storePayment'])->name('grns.payments.store');
    Route::delete('grns/{grn}/payments/{payment}', [GrnController::class, 'deletePayment'])->name('grns.payments.delete');
    
    // GRN routes
    Route::get('grns', [GrnController::class, 'index'])->name('grns.index');
    Route::get('grns/create-from-po/{purchase_order_id}', [GrnController::class, 'createFromPO'])->name('grns.create_from_po');
    Route::post('grns/store-from-po', [GrnController::class, 'storeFromPO'])->name('grns.store_from_po');
    Route::get('grns/{id}', [GrnController::class, 'show'])->name('grns.show');
    Route::get('grns/{id}/print', [GrnController::class, 'print'])->name('grns.print');
    Route::get('grns/{id}/pdf', [GrnController::class, 'downloadPdf'])->name('grns.pdf');
    Route::delete('grns/{id}', [GrnController::class, 'destroy'])->name('grns.destroy');
    
    // Sales Return routes
    Route::get('sales-return', [SalesReturnController::class, 'index'])->name('sales-return.index');
    Route::get('sales-return/create-from-invoice/{invoice_id}', [SalesReturnController::class, 'createFromInvoice'])->name('sales-return.create_from_invoice');
    Route::post('sales-return', [SalesReturnController::class, 'store'])->name('sales-return.store');
    
    // Expense routes
    Route::resource('expenses', ExpenseController::class);
    Route::get('expenses/subcategories/{category_id}', [ExpenseController::class, 'getSubCategories'])->name('expenses.subcategories');
    
    // Expense Category Management
    Route::resource('expense-categories', ExpenseCategoryController::class);
    
    // Expense Subcategory Management
    Route::resource('expense-sub-categories', ExpenseSubCategoryController::class);
    
    // Expense Subcategory Management
    Route::resource('expense-sub-categories', ExpenseSubCategoryController::class);
    
    // Reports
    Route::get('reports/account-summary', [ReportsController::class, 'accountSummary'])->name('reports.account-summary');
    Route::get('reports/purchase-report', [ReportsController::class, 'purchaseReport'])->name('reports.purchase-report');
    Route::get('reports/purchase-return-report', [ReportsController::class, 'purchaseReturnReport'])->name('reports.purchase-return-report');
    Route::get('reports/purchase-payment-report', [ReportsController::class, 'purchasePaymentReport'])->name('reports.purchase-payment-report');
    Route::get('reports/sales-item-report', [ReportsController::class, 'salesItemReport'])->name('reports.sales-item-report');
    Route::get('reports/item-purchase-report', [ReportsController::class, 'itemPurchaseReport'])->name('reports.item-purchase-report');
    Route::get('reports/sales-report', [ReportsController::class, 'salesReport'])->name('reports.sales-report');
    Route::post('reports/account-summary', [ReportsController::class, 'accountSummary']);

    // Sales Return Report (page and AJAX)
    Route::get('reports/sales-return-report', [ReportsController::class, 'salesReturnReport'])->name('reports.sales-return-report');
    Route::get('reports/sales-return-report/data', [ReportsController::class, 'salesReturnReportData'])->name('reports.sales-return-report.data');
    
    // Item/Stock Management routes
    Route::resource('items', ItemController::class);
    // Units management for item units
    Route::resource('units', App\Http\Controllers\UnitController::class);
    // Services (stored as items with stock_type = 'Service')
    Route::resource('services', App\Http\Controllers\ServiceController::class);
    Route::get('stock/report', [ItemController::class, 'stockReport'])->name('stock.report');
    Route::get('stock/low-stock', [ItemController::class, 'lowStockReport'])->name('stock.low_stock');
        // Lot-based item search
        Route::get('items/lot-search', [App\Http\Controllers\ItemController::class, 'searchByLot'])->name('items.lot_search');
            // API: latest cost for an item
            Route::get('api/items/{id}/latest-cost', [App\Http\Controllers\ItemController::class, 'apiLatestCost'])->name('api.items.latest_cost');

        // Damage stock: mark items as damaged and decrement inventory
        Route::get('inventory/damage', [App\Http\Controllers\DamageStockController::class, 'create'])->name('inventory.damage.create');
        Route::post('inventory/damage', [App\Http\Controllers\DamageStockController::class, 'store'])->name('inventory.damage.store');
        Route::get('inventory/damage-history', [App\Http\Controllers\DamageStockController::class, 'index'])->name('inventory.damage.history');
    
    // Customer routes
    Route::resource('customers', CustomerController::class);
    
    // Supplier routes
    Route::resource('suppliers', SupplierController::class);
    
    // Delivery Note routes
    Route::get('deliveries', [DeliveryNoteController::class, 'index'])->name('deliveries.index');
    Route::get('deliveries/create', [DeliveryNoteController::class, 'create'])->name('deliveries.create');
    Route::post('deliveries', [DeliveryNoteController::class, 'store'])->name('deliveries.store');
    Route::get('deliveries/{id}', [DeliveryNoteController::class, 'show'])->name('deliveries.show');
    Route::get('deliveries/{id}/print', [DeliveryNoteController::class, 'print'])->name('deliveries.print');
    Route::put('deliveries/{id}/status', [DeliveryNoteController::class, 'updateStatus'])->name('deliveries.update_status');
    
    // Job Card routes
    Route::resource('job-cards', JobCardController::class);
    Route::get('job-cards/{id}/duplicate', [App\Http\Controllers\JobCardController::class, 'duplicate'])->name('job-cards.duplicate');
    Route::get('job-cards/{id}/print', [JobCardController::class, 'print'])->name('job-cards.print');
    // Create quotation from job card
    Route::get('job-cards/{jobCard}/create-quote', [App\Http\Controllers\JobCardController::class, 'createQuote'])->name('job-cards.create_quote');
});
