<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Customer;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the form for creating an invoice from quotation
     */
    public function createFromQuotation($quotation_id)
    {
        $statusq = request('statusq', 'final'); // 'performance' or 'final'
        
        $quotation = Quotation::with(['customer', 'items.item'])->findOrFail($quotation_id);
        
        // Check if invoice already exists for this quotation
        $existing_invoice = DB::table('sales_invoices')
            ->where('quotation_id', $quotation_id)
            ->first();
        
        if ($existing_invoice) {
            return redirect()->route('quotations.index')
                ->with('error', 'Invoice already created for this quotation.');
        }
        
        return view('sales.create_from_quotation', compact('quotation', 'statusq'));
    }

    /**
     * Store the invoice created from quotation
     */
    public function storeFromQuotation(Request $request)
    {
        $validated = $request->validate([
            'quotation_id' => 'required|exists:quotations,id',
            'customer_id' => 'required|exists:customers,id',
            'sales_date' => 'required|date',
            'sales_status' => 'required|in:performance,final',
            'invoice_type' => 'required|in:Partially,Full',
            'reference_no' => 'nullable|string',
            'sales_note' => 'nullable|string',
            'amount' => 'nullable|numeric',
            'credit_due' => 'nullable|numeric',
            'payment_type' => 'nullable|string',
            'paid_type' => 'nullable|in:Paid,Not Paid',
            'payment_note' => 'nullable|string',
        ]);

        try {
            // Log quotation items being copied (debugging lot_id)
            try { Log::info('Invoice createFromQuotation copying quotation items', ['user_id' => optional($request->user())->id, 'quotation_id' => $request->quotation_id]); } catch (\Exception $e) { Log::error('Failed to log invoice creation debug: '.$e->getMessage()); }
            DB::beginTransaction();

            // Generate invoice code
            $invoice_code = $this->generateInvoiceCode($validated['sales_status']);

            // Create invoice/sales record
            $invoice_id = DB::table('sales_invoices')->insertGetId([
                'quotation_id' => $validated['quotation_id'],
                'invoice_no' => $invoice_code,
                'customer_id' => $validated['customer_id'],
                'invoice_date' => $validated['sales_date'],
                'invoice_status' => $validated['sales_status'],
                'invoice_type' => $validated['invoice_type'],
                'reference_no' => $validated['reference_no'] ?? null,
                'notes' => $validated['sales_note'] ?? null,
                'discount' => $request->input('discount_to_all_input', 0),
                'other_charges' => $request->input('other_charges_input', 0),
                'subtotal' => $request->input('subtotal', 0),
                'vat_amount' => 0,
                'total_amount' => $request->input('total_amt', 0),
                'credit_due' => $request->input('credit_due', 0),
                'payment_type' => $request->input('payment_type', null),
                'paid_type' => $request->input('paid_type', null),
                'company_id' => auth()->user()->company_id,
                'created_by' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // If items were submitted from the form (with possible lot_id overrides), use them.
            $formItems = $request->input('items');
            if (is_array($formItems) && count($formItems)) {
                foreach ($formItems as $fi) {
                    DB::table('sales_invoice_items')->insert([
                        'sales_invoice_id' => $invoice_id,
                        'item_id' => $fi['item_id'] ?? null,
                        'lot_id' => $fi['lot_id'] ?? null,
                        'qty' => $fi['qty'] ?? 0,
                        'sale_price' => $fi['rate'] ?? 0,
                        'amount' => ($fi['qty'] ?? 0) * ($fi['rate'] ?? 0),
                        'vat_amount' => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            } else {
                // Fallback: copy quotation items to sales items (preserve lot_id)
                $quotation_items = QuotationItem::where('quotation_id', $validated['quotation_id'])->get();
                foreach ($quotation_items as $item) {
                    DB::table('sales_invoice_items')->insert([
                        'sales_invoice_id' => $invoice_id,
                        'item_id' => $item->item_id,
                        'lot_id' => $item->lot_id ?? null,
                        'qty' => $item->qty,
                        'sale_price' => $item->rate,
                        'amount' => $item->amount,
                        'vat_amount' => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // Create payment record if amount is provided and marked as Paid
            if (!empty($validated['amount']) && ($validated['paid_type'] === 'Paid')) {
                // Map incoming payment_type to allowed `mode` enum values
                $mode = $validated['payment_type'] ?? 'Cash';
                if (strtolower($mode) === 'bank transfer') $mode = 'Bank';
                if (strtolower($mode) === 'credit') $mode = 'Online';
                if (!in_array($mode, ['Cash','Bank','Card','Cheque','Online'])) {
                    $mode = 'Online';
                }

                DB::table('payments')->insert([
                    'reference_type' => 'Sales',
                    'reference_id' => $invoice_id,
                    'customer_id' => $validated['customer_id'] ?? null,
                    'amount' => $validated['amount'],
                    'payment_date' => $validated['sales_date'],
                    'mode' => $mode,
                    'transaction_ref' => null,
                    'remarks' => $validated['payment_note'] ?? null,
                    'paid_type' => $validated['paid_type'] ?? null,
                    'credit_due' => $validated['credit_due'] ?? 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // credit_due is stored on the sales_invoices.credit_due column

            DB::commit();

            return redirect()->route('quotations.index')
                ->with('success', ucfirst($validated['sales_status']) . ' Invoice created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error creating invoice: ' . $e->getMessage());
        }
    }

    /**
     * Generate unique invoice code
     */
    private function generateInvoiceCode($type)
    {
        $prefix = $type === 'performance' ? 'PERF-INV-' : 'INV-';
        $last_invoice = DB::table('sales_invoices')
            ->where('invoice_no', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if ($last_invoice) {
            $last_number = intval(str_replace($prefix, '', $last_invoice->invoice_no));
            $new_number = $last_number + 1;
        } else {
            $new_number = 1;
        }

        return $prefix . str_pad($new_number, 6, '0', STR_PAD_LEFT);
    }
}
