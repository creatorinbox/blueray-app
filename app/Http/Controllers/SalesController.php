<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of sales invoices
     */
    public function index()
    {
        // Get statistics
        $stats = [
            'total_invoices' => DB::table('sales_invoices')
                ->where('company_id', auth()->user()->company_id)
                ->count(),
            
            'total_amount' => DB::table('sales_invoices')
                ->where('company_id', auth()->user()->company_id)
                ->sum('total_amount'),
            
            'total_paid' => DB::table('sales_invoices')
                ->where('company_id', auth()->user()->company_id)
                ->sum('paid_amount'),
            
            'total_due' => DB::table('sales_invoices')
                ->where('company_id', auth()->user()->company_id)
                ->sum(DB::raw('total_amount - paid_amount')),
        ];
        
        // Get invoices with relationships
        $invoices = DB::table('sales_invoices')
            ->leftJoin('customers', 'sales_invoices.customer_id', '=', 'customers.id')
            ->leftJoin('users', 'sales_invoices.created_by', '=', 'users.id')
            ->select(
                'sales_invoices.*',
                'customers.customer_name',
                'users.name as creator_name'
            )
            ->where('sales_invoices.company_id', auth()->user()->company_id)
            ->orderBy('sales_invoices.invoice_date', 'desc')
            ->orderBy('sales_invoices.id', 'desc')
            ->paginate(25);
        
        // Convert to collection with proper date casting
        $invoices->getCollection()->transform(function ($invoice) {
            $invoice->invoice_date = \Carbon\Carbon::parse($invoice->invoice_date);
            $invoice->customer = (object)['customer_name' => $invoice->customer_name];
            $invoice->creator = (object)['name' => $invoice->creator_name];
            return $invoice;
        });
        
        return view('sales.index', compact('invoices', 'stats'));
    }

    /**
     * Display the specified invoice
     */
    public function show($id)
    {
        $invoice = DB::table('sales_invoices')
            ->leftJoin('customers', 'sales_invoices.customer_id', '=', 'customers.id')
            ->leftJoin('users', 'sales_invoices.created_by', '=', 'users.id')
            ->select(
                'sales_invoices.*',
                'customers.customer_name',
                'customers.address',
                'customers.phone',
                'customers.email',
                'users.name as creator_name'
            )
            ->where('sales_invoices.id', $id)
            ->first();
        
        if (!$invoice) {
            return redirect()->route('sales.index')
                ->with('error', 'Invoice not found.');
        }
        
        // Get invoice items
        $items = DB::table('sales_invoice_items')
            ->leftJoin('items', 'sales_invoice_items.item_id', '=', 'items.id')
            ->select(
                'sales_invoice_items.*',
                'items.item_name',
                'items.oem_part_no',
                'items.unit'
            )
            ->where('sales_invoice_items.sales_invoice_id', $id)
            ->get();
        
        // Get payments
        $payments = DB::table('payments')
            ->where('reference_type', 'Sales')
            ->where('reference_id', $id)
            ->orderBy('payment_date', 'desc')
            ->get();
        
        return view('sales.show', compact('invoice', 'items', 'payments'));
    }

    /**
     * Print invoice
     */
    public function print($id)
    {
        $invoice = DB::table('sales_invoices')
            ->leftJoin('customers', 'sales_invoices.customer_id', '=', 'customers.id')
            ->leftJoin('users', 'sales_invoices.created_by', '=', 'users.id')
            ->select(
                'sales_invoices.*',
                'customers.customer_name',
                'customers.address',
                'customers.phone',
                'customers.email',
                'users.name as creator_name'
            )
            ->where('sales_invoices.id', $id)
            ->first();
        
        if (!$invoice) {
            abort(404, 'Invoice not found');
        }
        
        // Get invoice items
        $items = DB::table('sales_invoice_items')
            ->leftJoin('items', 'sales_invoice_items.item_id', '=', 'items.id')
            ->select(
                'sales_invoice_items.*',
                'items.item_name',
                'items.oem_part_no',
                'items.unit'
            )
            ->where('sales_invoice_items.sales_invoice_id', $id)
            ->get();
        
        return view('sales.print', compact('invoice', 'items'));
    }

    /**
     * Remove the specified invoice from storage
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            
            // Delete invoice items
            DB::table('sales_invoice_items')
                ->where('sales_invoice_id', $id)
                ->delete();
            
            // Delete payments
            DB::table('payments')
                ->where('reference_type', 'Sales')
                ->where('reference_id', $id)
                ->delete();
            
            // Delete invoice
            DB::table('sales_invoices')
                ->where('id', $id)
                ->delete();
            
            DB::commit();
            
            return redirect()->route('sales.index')
                ->with('success', 'Invoice deleted successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error deleting invoice: ' . $e->getMessage());
        }
    }

    /**
     * Approve delivery for invoice
     */
    public function approveDelivery($id)
    {
        try {
            $invoice = \App\Models\SalesInvoice::with('customer')->find($id);
            if (!$invoice) {
                return redirect()->route('sales.index')
                    ->with('error', 'Invoice not found.');
            }
            // Reduce stock for each item in the invoice
            $items = \App\Models\SalesInvoiceItem::where('sales_invoice_id', $invoice->id)->get();
            foreach ($items as $itemRow) {
                $item = \App\Models\Item::find($itemRow->item_id);
                if ($item) {
                    $item->current_stock = max(0, $item->current_stock - $itemRow->qty);
                    $item->save();
                }
            }
            // Update delivery status
            $invoice->delivery_status = 'Approved';
            $invoice->delivery_approved_by = auth()->id();
            $invoice->delivery_approved_at = now();
            $invoice->updated_at = now();
            $invoice->save();
            return redirect()->route('sales.index')
                ->with('success', 'Delivery approved and stock reduced for Invoice ' . $invoice->invoice_no);
        } catch (\Exception $e) {
            return back()->with('error', 'Error approving delivery: ' . $e->getMessage());
        }
    }
}
