<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function accountSummary(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
        
        // Convert to proper date format
        $startDate = Carbon::parse($startDate)->format('Y-m-d');
        $endDate = Carbon::parse($endDate)->format('Y-m-d');
        
        $companyId = auth()->user()->company_id;
        
        // Calculate various financial metrics
        $data = [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'opening_stock_price' => $this->getOpeningStock($startDate, $endDate),
            'purchase_total' => $this->getPurchaseTotal($startDate, $endDate, $companyId),
            'purchase_tax_amt' => $this->getPurchaseTax($startDate, $endDate, $companyId),
            'purchase_discount_amt' => $this->getPurchaseDiscount($startDate, $endDate, $companyId),
            'purchase_paid_amount' => $this->getPurchasePaid($startDate, $endDate, $companyId),
            'purchase_return_total' => $this->getPurchaseReturnTotal($startDate, $endDate, $companyId),
            'purchase_return_paid_amount' => $this->getPurchaseReturnPaid($startDate, $endDate, $companyId),
            'sales_total' => $this->getSalesTotal($startDate, $endDate, $companyId),
            'sales_tax_amt' => $this->getSalesTax($startDate, $endDate, $companyId),
            'sales_discount_amt' => $this->getSalesDiscount($startDate, $endDate, $companyId),
            'sales_paid_amount' => $this->getSalesPaid($startDate, $endDate, $companyId),
            'sales_return_total' => $this->getSalesReturnTotal($startDate, $endDate, $companyId),
            'sales_return_paid_amount' => $this->getSalesReturnPaid($startDate, $endDate, $companyId),
            'expense_total' => $this->getExpenseTotal($startDate, $endDate, $companyId),
            'purchase_due_total' => $this->getPurchaseDue($companyId),
            'sales_due_total' => $this->getSalesDue($companyId),
            'gross_profit' => 0,
            'net_profit' => 0,
        ];
        
        // Calculate profit
        $data['gross_profit'] = $data['sales_total'] - $data['sales_return_total'] - ($data['purchase_total'] - $data['purchase_return_total']);
        $data['net_profit'] = $data['gross_profit'] - $data['expense_total'];
        
        return view('reports.account-summary', compact('data'));
    }
    
    private function getOpeningStock($startDate, $endDate)
    {
        return DB::table('stock_lots')
            ->where('created_at', '>=', $startDate)
            ->where('created_at', '<=', $endDate)
            ->selectRaw('SUM(qty_available * cost_price) as total_stock_value')
            ->value('total_stock_value') ?? 0;
    }
    
    private function getPurchaseTotal($startDate, $endDate, $companyId)
    {
        return DB::table('purchase_orders')
            ->where('po_date', '>=', $startDate)
            ->where('po_date', '<=', $endDate)
            ->sum('total_amount') ?? 0;
    }
    
    private function getPurchaseTax($startDate, $endDate, $companyId)
    {
        // Purchase orders table doesn't have tax_amount, return 0 for now
        return 0;
    }
    
    private function getPurchaseDiscount($startDate, $endDate, $companyId)
    {
        // Purchase orders table doesn't have discount_amount, return 0 for now
        return 0;
    }
    
    private function getPurchasePaid($startDate, $endDate, $companyId)
    {
        // Purchase orders table doesn't have paid_amount, return 0 for now
        return 0;
    }
    
    private function getPurchaseReturnTotal($startDate, $endDate, $companyId)
    {
        return 0;
    }
    
    private function getPurchaseReturnPaid($startDate, $endDate, $companyId)
    {
        return 0;
    }
    
    private function getSalesTotal($startDate, $endDate, $companyId)
    {
        return DB::table('sales_invoices')
            ->where('company_id', $companyId)
            ->where('invoice_date', '>=', $startDate)
            ->where('invoice_date', '<=', $endDate)
            ->sum('total_amount') ?? 0;
    }
    
    private function getSalesTax($startDate, $endDate, $companyId)
    {
        return DB::table('sales_invoices')
            ->where('company_id', $companyId)
            ->where('invoice_date', '>=', $startDate)
            ->where('invoice_date', '<=', $endDate)
            ->sum('vat_amount') ?? 0;
    }
    
    private function getSalesDiscount($startDate, $endDate, $companyId)
    {
        return DB::table('sales_invoices')
            ->where('company_id', $companyId)
            ->where('invoice_date', '>=', $startDate)
            ->where('invoice_date', '<=', $endDate)
            ->sum('discount') ?? 0;
    }
    
    private function getSalesPaid($startDate, $endDate, $companyId)
    {
        return DB::table('sales_invoices')
            ->where('company_id', $companyId)
            ->where('invoice_date', '>=', $startDate)
            ->where('invoice_date', '<=', $endDate)
            ->sum('paid_amount') ?? 0;
    }
    
    private function getSalesReturnTotal($startDate, $endDate, $companyId)
    {
        return DB::table('sales_returns')
            ->where('company_id', $companyId)
            ->where('return_date', '>=', $startDate)
            ->where('return_date', '<=', $endDate)
            ->sum('total_amount') ?? 0;
    }
    
    private function getSalesReturnPaid($startDate, $endDate, $companyId)
    {
        return DB::table('sales_returns')
            ->where('company_id', $companyId)
            ->where('return_date', '>=', $startDate)
            ->where('return_date', '<=', $endDate)
            ->sum('total_amount') ?? 0;
    }
    
    private function getExpenseTotal($startDate, $endDate, $companyId)
    {
        return DB::table('expenses')
            ->where('company_id', $companyId)
            ->where('expense_date', '>=', $startDate)
            ->where('expense_date', '<=', $endDate)
            ->sum('total_amount') ?? 0;
    }
    
    private function getPurchaseDue($companyId)
    {
        // Suppliers table doesn't have purchase_due column, calculate from purchase_orders
        $totalPurchase = DB::table('purchase_orders')->sum('total_amount') ?? 0;
        // For now, assume all purchases are due (you can adjust this logic)
        return $totalPurchase * 0.3; // Assume 30% is due as placeholder
    }
    
    private function getSalesDue($companyId)
    {
        // Calculate total due from sales_invoices
        $totalSales = DB::table('sales_invoices')
            ->where('company_id', $companyId)
            ->sum('total_amount') ?? 0;
        $totalPaid = DB::table('sales_invoices')
            ->where('company_id', $companyId)
            ->sum('paid_amount') ?? 0;
        
        return $totalSales - $totalPaid;
    }

    public function purchaseReport(Request $request)
    {
        $companyId = auth()->user()->company_id;
        
        // Get suppliers for dropdown
        $suppliers = DB::table('suppliers')
            ->where('company_id', $companyId)
            ->where('is_active', 1)
            ->select('id', 'supplier_name')
            ->orderBy('supplier_name')
            ->get();

        // If it's an AJAX request, return the purchase data
        if ($request->ajax()) {
            $startDate = $request->input('from_date', Carbon::now()->subDays(30)->format('Y-m-d'));
            $endDate = $request->input('to_date', Carbon::now()->format('Y-m-d'));
            $supplierId = $request->input('supplier_id');

            // Convert dates to proper format
            $startDate = Carbon::parse($startDate)->format('Y-m-d');
            $endDate = Carbon::parse($endDate)->format('Y-m-d');


            $query = DB::table('purchase_orders as po')
                ->leftJoin('suppliers as s', 'po.supplier_id', '=', 's.id')
                ->where('po.supplier_id', '!=', null)
                ->where('po.po_date', '>=', $startDate)
                ->where('po.po_date', '<=', $endDate);

            if (!empty($supplierId)) {
                $query->where('po.supplier_id', $supplierId);
            }

            $purchases = $query->select([
                    'po.id',
                    'po.po_no as invoice_no',
                    'po.po_date',
                    'po.supplier_id',
                    's.supplier_name',
                    'po.total_amount as invoice_total'
                ])
                ->orderBy('po.po_date', 'desc')
                ->get();

            // Add dummy values for amount, vat, paid_amount, due_amount for compatibility
            $purchases = $purchases->map(function($row) {
                $row->amount = $row->invoice_total;
                $row->vat = round($row->invoice_total * 0.05, 3); // 5% VAT
                $row->paid_amount = 0;
                $row->due_amount = $row->invoice_total + $row->vat;
                return $row;
            });

            return response()->json([
                'success' => true,
                'data' => $purchases
            ]);
        }

        return view('reports.purchase-report', compact('suppliers'));
    }

    public function purchaseReturnReport(Request $request)
    {
        $companyId = auth()->user()->company_id;
        
        // Get suppliers for dropdown
        $suppliers = DB::table('suppliers')
            ->where('company_id', $companyId)
            ->where('is_active', 1)
            ->select('id', 'supplier_name')
            ->orderBy('supplier_name')
            ->get();

        // If it's an AJAX request, return the purchase return data
        if ($request->ajax()) {
            $startDate = $request->input('from_date', Carbon::now()->subDays(30)->format('Y-m-d'));
            $endDate = $request->input('to_date', Carbon::now()->format('Y-m-d'));
            $supplierId = $request->input('supplier_id');

            // Convert dates to proper format
            $startDate = Carbon::parse($startDate)->format('Y-m-d');
            $endDate = Carbon::parse($endDate)->format('Y-m-d');

            $query = DB::table('purchase_returns as pr')
                ->leftJoin('suppliers as s', 'pr.supplier_id', '=', 's.id')
                ->where('pr.return_date', '>=', $startDate)
                ->where('pr.return_date', '<=', $endDate);

            if (!empty($supplierId)) {
                $query->where('pr.supplier_id', $supplierId);
            }

            $purchaseReturns = $query->select([
                    'pr.id',
                    'pr.return_no as invoice_no',
                    'pr.return_date',
                    'pr.supplier_id',
                    's.supplier_name',
                    'pr.total_amount as amount',
                    DB::raw('0 as vat'), // No VAT field in purchase_returns
                    'pr.total_amount as invoice_total',
                    'pr.total_amount as paid_amount', // Assuming returns are paid
                    DB::raw('0 as due_amount') // No due amount for returns
                ])
                ->orderBy('pr.return_date', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $purchaseReturns
            ]);
        }

        return view('reports.purchase-return-report', compact('suppliers'));
    }

    public function purchasePaymentReport(Request $request)
    {
        $companyId = auth()->user()->company_id;
        
        // Get suppliers for dropdown
        $suppliers = DB::table('suppliers')
            ->where('company_id', $companyId)
            ->where('is_active', 1)
            ->select('id', 'supplier_name')
            ->orderBy('supplier_name')
            ->get();
        
        // Get distinct payment modes from payments table
        $paymentModes = DB::table('payments')
            ->select('mode')
            ->distinct()
            ->whereNotNull('mode')
            ->orderBy('mode')
            ->pluck('mode');

        // If it's an AJAX request, return the payment data
        if ($request->ajax()) {
            $startDate = $request->input('from_date', Carbon::now()->subDays(30)->format('Y-m-d'));
            $endDate = $request->input('to_date', Carbon::now()->format('Y-m-d'));
            $supplierId = $request->input('supplier_id');
            $paymentType = $request->input('payment_type');
            $reportType = $request->input('report_type', 'purchase_payments');

            // Convert dates to proper format
            $startDate = Carbon::parse($startDate)->format('Y-m-d');
            $endDate = Carbon::parse($endDate)->format('Y-m-d');

            if ($reportType === 'purchase_payments') {
                // Purchase payments - payments linked to purchase orders
                $query = DB::table('payments as p')
                    ->leftJoin('purchase_orders as po', function($join) {
                        $join->on('p.reference_id', '=', 'po.id')
                             ->where('p.reference_type', '=', 'purchase_order');
                    })
                    ->leftJoin('suppliers as s', 'po.supplier_id', '=', 's.id')
                    ->where('p.payment_date', '>=', $startDate)
                    ->where('p.payment_date', '<=', $endDate)
                    ->where('p.reference_type', 'purchase_order');

                if (!empty($supplierId)) {
                    $query->where('po.supplier_id', $supplierId);
                }
                if (!empty($paymentType)) {
                    $query->where('p.mode', $paymentType);
                }

                $payments = $query->select([
                        'p.id',
                        'po.po_no as invoice_no',
                        'p.payment_date',
                        'po.supplier_id',
                        's.supplier_name',
                        'po.total_amount as amount',
                        DB::raw('0 as vat'),
                        'p.mode as payment_type',
                        'p.remarks as payment_note',
                        'p.amount as paid_amount'
                    ])
                    ->orderBy('p.payment_date', 'desc')
                    ->get();
            } else {
                // Supplier payments - all payments to suppliers
                $query = DB::table('payments as p')
                    ->leftJoin('purchase_orders as po', function($join) {
                        $join->on('p.reference_id', '=', 'po.id')
                             ->where('p.reference_type', '=', 'purchase_order');
                    })
                    ->leftJoin('suppliers as s', 'po.supplier_id', '=', 's.id')
                    ->where('p.payment_date', '>=', $startDate)
                    ->where('p.payment_date', '<=', $endDate);

                if (!empty($supplierId)) {
                    $query->where('po.supplier_id', $supplierId);
                }
                if (!empty($paymentType)) {
                    $query->where('p.mode', $paymentType);
                }

                $payments = $query->select([
                        'p.id',
                        'p.payment_date',
                        's.supplier_name',
                        'p.mode as payment_type',
                        'p.remarks as payment_note',
                        'p.amount as paid_amount'
                    ])
                    ->orderBy('p.payment_date', 'desc')
                    ->get();
            }

            return response()->json([
                'success' => true,
                'data' => $payments
            ]);
        }

        return view('reports.purchase-payment-report', compact('suppliers', 'paymentModes'));
    }

    public function salesItemReport(Request $request)
    {
        $companyId = auth()->user()->company_id;
        
        // Get items for dropdown
        $items = DB::table('items')
            ->where('company_id', $companyId)
            ->where('is_active', 1)
            ->select('id', 'item_code', 'item_name')
            ->orderBy('item_name')
            ->get();

        // If it's an AJAX request, return the sales data
        if ($request->ajax()) {
            $startDate = $request->input('from_date', Carbon::now()->subDays(30)->format('Y-m-d'));
            $endDate = $request->input('to_date', Carbon::now()->format('Y-m-d'));
            $itemId = $request->input('item_id');

            // Convert dates to proper format
            $startDate = Carbon::parse($startDate)->format('Y-m-d');
            $endDate = Carbon::parse($endDate)->format('Y-m-d');

            $query = DB::table('sales_invoice_items as sii')
                ->leftJoin('sales_invoices as si', 'sii.sales_invoice_id', '=', 'si.id')
                ->leftJoin('customers as c', 'si.customer_id', '=', 'c.id')
                ->leftJoin('items as i', 'sii.item_id', '=', 'i.id')
                ->where('si.company_id', $companyId)
                ->where('si.invoice_date', '>=', $startDate)
                ->where('si.invoice_date', '<=', $endDate);

            if (!empty($itemId)) {
                $query->where('sii.item_id', $itemId);
            }

            $salesItems = $query->select([
                    'sii.id',
                    'si.invoice_no',
                    'si.invoice_date as sales_date',
                    'c.customer_name',
                    'i.item_code',
                    'i.item_name',
                    'sii.qty as item_sales_count',
                    'sii.amount',
                    'sii.vat_amount as vat',
                    DB::raw('(sii.amount + COALESCE(sii.vat_amount, 0)) as sales_amount')
                ])
                ->orderBy('si.invoice_date', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $salesItems
            ]);
        }

        return view('reports.sales-item-report', compact('items'));
    }

    public function itemPurchaseReport(Request $request)
    {
        $companyId = auth()->user()->company_id;
        // Get items for dropdown
        $items = DB::table('items')
            ->where('company_id', $companyId)
            ->where('is_active', 1)
            ->select('id', 'item_code', 'item_name')
            ->orderBy('item_name')
            ->get();

        // If it's an AJAX request, return the purchase data
        if ($request->ajax()) {
            $startDate = $request->input('from_date', Carbon::now()->subDays(30)->format('Y-m-d'));
            $endDate = $request->input('to_date', Carbon::now()->format('Y-m-d'));
            $itemId = $request->input('item_id');

            // Convert dates to proper format
            $startDate = Carbon::parse($startDate)->format('Y-m-d');
            $endDate = Carbon::parse($endDate)->format('Y-m-d');

            $query = DB::table('purchase_order_items as poi')
                ->leftJoin('purchase_orders as po', 'poi.purchase_order_id', '=', 'po.id')
                ->leftJoin('suppliers as s', 'po.supplier_id', '=', 's.id')
                ->leftJoin('items as i', 'poi.item_id', '=', 'i.id')
                ->where('po.company_id', $companyId)
                ->where('po.po_date', '>=', $startDate)
                ->where('po.po_date', '<=', $endDate);

            if (!empty($itemId)) {
                $query->where('poi.item_id', $itemId);
            }

            $purchaseItems = $query->select([
                    'poi.id',
                    'po.po_no as invoice_no',
                    'po.po_date as purchase_date',
                    's.supplier_name',
                    'i.item_code',
                    'i.item_name',
                    'poi.qty as quantity',
                    'poi.amount'
                ])
                ->orderBy('po.po_date', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $purchaseItems
            ]);
        }

        return view('reports.item-purchase-report', compact('items'));
    }

    public function salesReport(Request $request)
    {
        $companyId = auth()->user()->company_id;
        // Get customers for dropdown
        $customers = DB::table('customers')
            ->where('company_id', $companyId)
            ->where('is_active', 1)
            ->select('id', 'customer_name')
            ->orderBy('customer_name')
            ->get();

        // If it's an AJAX request, return the sales data
        if ($request->ajax()) {
            $startDate = $request->input('from_date', Carbon::now()->subDays(30)->format('Y-m-d'));
            $endDate = $request->input('to_date', Carbon::now()->format('Y-m-d'));
            $customerId = $request->input('customer_id');

            // Convert dates to proper format
            $startDate = Carbon::parse($startDate)->format('Y-m-d');
            $endDate = Carbon::parse($endDate)->format('Y-m-d');

            $query = DB::table('sales_invoices as si')
                ->leftJoin('customers as c', 'si.customer_id', '=', 'c.id')
                ->where('si.company_id', $companyId)
                ->where('si.invoice_date', '>=', $startDate)
                ->where('si.invoice_date', '<=', $endDate);

            if (!empty($customerId)) {
                $query->where('si.customer_id', $customerId);
            }

            $sales = $query->select([
                    'si.id',
                    'si.invoice_no',
                    'si.invoice_date as sales_date',
                    'c.id as customer_id',
                    'c.customer_name',
                    'c.tax_number as customer_vat_number',
                    'si.subtotal',
                    'si.vat_amount as vat',
                    'si.total_amount as invoice_total',
                    'si.paid_amount',
                    // Aging buckets
                    DB::raw('IF(DATEDIFF(CURDATE(), si.invoice_date) BETWEEN 1 AND 30, si.total_amount - si.paid_amount, 0) as aging_1_30'),
                    DB::raw('IF(DATEDIFF(CURDATE(), si.invoice_date) BETWEEN 31 AND 60, si.total_amount - si.paid_amount, 0) as aging_31_60'),
                    DB::raw('IF(DATEDIFF(CURDATE(), si.invoice_date) BETWEEN 61 AND 90, si.total_amount - si.paid_amount, 0) as aging_61_90'),
                    DB::raw('IF(DATEDIFF(CURDATE(), si.invoice_date) BETWEEN 91 AND 120, si.total_amount - si.paid_amount, 0) as aging_91_120'),
                    DB::raw('IF(DATEDIFF(CURDATE(), si.invoice_date) > 120, si.total_amount - si.paid_amount, 0) as aging_120_plus')
                ])
                ->orderBy('si.invoice_date', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $sales
            ]);
        }

        return view('reports.sales-report', compact('customers'));
    }

    /**
     * Show the sales return report page.
     */
    public function salesReturnReport(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $customers = \App\Models\Customer::where('company_id', $companyId)->where('is_active', 1)->orderBy('customer_name')->get();
        $currency = optional(\App\Models\Company::find($companyId))->base_currency ?? 'OMR';
        return view('reports.sales-return-report', compact('customers', 'currency'));
    }

    /**
     * Provide AJAX data for the sales return report.
     */
    public function salesReturnReportData(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $startDate = $request->input('from_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('to_date', now()->format('Y-m-d'));
        $customerId = $request->input('customer_id');

        $query = \App\Models\SalesReturn::with('customer')
            ->where('company_id', $companyId)
            ->whereDate('return_date', '>=', $startDate)
            ->whereDate('return_date', '<=', $endDate);
        if (!empty($customerId)) {
            $query->where('customer_id', $customerId);
        }
        $returns = $query->orderBy('return_date', 'desc')->get();
        $data = $returns->map(function($r) {
            return [
                'invoice_no' => $r->sales_invoice_id,
                'return_date' => $r->return_date,
                'sales_code' => $r->sales_code,
                'customer_name' => optional($r->customer)->customer_name,
                'without_vat' => $r->without_vat,
                'vat' => $r->vat,
                'invoice_total' => $r->invoice_total,
                'paid_amount' => $r->paid_amount,
                'due_amount' => $r->due_amount,
            ];
        });
        return response()->json(['success' => true, 'data' => $data]);
    }

    /**
     * Show the sales payment report page.
     */
    public function salesPaymentReport(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $customers = \App\Models\Customer::where('company_id', $companyId)->where('is_active', 1)->orderBy('customer_name')->get();
        $paymentTypes = \App\Models\PaymentType::all();
        $currency = optional(\App\Models\Company::find($companyId))->base_currency ?? 'OMR';
        return view('reports.sales-payment-report', compact('customers', 'paymentTypes', 'currency'));
    }

    /**
     * Provide AJAX data for the sales payment report.
     */
    public function salesPaymentReportData(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $startDate = $request->input('from_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('to_date', now()->format('Y-m-d'));
        $customerId = $request->input('customer_id');
        $paymentType = $request->input('payment_type');

        // Sales Payments
        $salesPaymentsQuery = \DB::table('sales_invoices as si')
            ->leftJoin('customers as c', 'si.customer_id', '=', 'c.id')
            ->leftJoin('sales_payments as sp', 'si.id', '=', 'sp.sales_invoice_id')
            ->where('si.company_id', $companyId)
            ->whereBetween('sp.payment_date', [$startDate, $endDate]);
        if (!empty($customerId)) {
            $salesPaymentsQuery->where('si.customer_id', $customerId);
        }
        if (!empty($paymentType)) {
            $salesPaymentsQuery->where('sp.payment_type', $paymentType);
        }
        $salesPayments = $salesPaymentsQuery->select([
            'si.invoice_no',
            'sp.payment_date',
            'c.id as customer_id',
            'c.customer_name',
            'c.tax_number as customer_vat_number',
            'sp.payment_type',
            'sp.payment_note',
            'si.subtotal',
            'si.vat_amount as vat',
            'sp.paid_amount'
        ])->orderBy('sp.payment_date', 'desc')->get();

        // Customer Payments
        $customerPaymentsQuery = \DB::table('customer_payments as cp')
            ->leftJoin('customers as c', 'cp.customer_id', '=', 'c.id')
            ->where('cp.company_id', $companyId)
            ->whereBetween('cp.payment_date', [$startDate, $endDate]);
        if (!empty($customerId)) {
            $customerPaymentsQuery->where('cp.customer_id', $customerId);
        }
        if (!empty($paymentType)) {
            $customerPaymentsQuery->where('cp.payment_type', $paymentType);
        }
        $customerPayments = $customerPaymentsQuery->select([
            'cp.payment_date',
            'c.customer_name',
            'cp.payment_type',
            'cp.payment_note',
            'cp.paid_amount'
        ])->orderBy('cp.payment_date', 'desc')->get();

        return response()->json([
            'sales_payments' => $salesPayments,
            'customer_payments' => $customerPayments
        ]);
    }
}
