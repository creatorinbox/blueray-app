<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesReturnController extends Controller
{
    public function index()
    {
        $returns = DB::table('sales_returns')
            ->leftJoin('customers', 'sales_returns.customer_id', '=', 'customers.id')
            ->leftJoin('sales_invoices', 'sales_returns.sales_invoice_id', '=', 'sales_invoices.id')
            ->select(
                'sales_returns.*',
                'customers.customer_name',
                'sales_invoices.invoice_no'
            )
            ->orderBy('sales_returns.return_date', 'desc')
            ->get();
        
        return view('sales_return.index', compact('returns'));
    }

    public function createFromInvoice($invoice_id)
    {
        $invoice = DB::table('sales_invoices')
            ->leftJoin('customers', 'sales_invoices.customer_id', '=', 'customers.id')
            ->select('sales_invoices.*', 'customers.customer_name')
            ->where('sales_invoices.id', $invoice_id)
            ->first();
        
        if (!$invoice) {
            return redirect()->route('sales.index')->with('error', 'Invoice not found');
        }
        
        $items = DB::table('sales_invoice_items')
            ->leftJoin('items', 'sales_invoice_items.item_id', '=', 'items.id')
            ->select('sales_invoice_items.*', 'items.item_name', 'items.oem_part_no', 'items.unit')
            ->where('sales_invoice_items.sales_invoice_id', $invoice_id)
            ->get();
        
        $return_code = $this->generateReturnCode();
        
        return view('sales_return.create', compact('invoice', 'items', 'return_code'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sales_invoice_id' => 'required|exists:sales_invoices,id',
            'customer_id' => 'required|exists:customers,id',
            'return_date' => 'required|date',
            'return_status' => 'required|in:Return,Cancel',
            'reference_no' => 'nullable|string',
            'return_note' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $validated['return_code'] = $this->generateReturnCode();
            $validated['discount_to_all_input'] = $request->input('discount_to_all_input', 0);
            $validated['discount_to_all_type'] = $request->input('discount_to_all_type', 'in_fixed');
            $validated['other_charges_input'] = $request->input('other_charges_input', 0);
            $validated['subtotal'] = $request->input('subtotal', 0);
            $validated['total_amount'] = $request->input('total_amt', 0);
            $validated['company_id'] = auth()->user()->company_id;
            $validated['created_by'] = auth()->id();
            $validated['created_at'] = now();
            $validated['updated_at'] = now();
            
            $return_id = DB::table('sales_returns')->insertGetId($validated);

            // Add return items
            $item_ids = $request->input('item_id', []);
            $qtys = $request->input('qty', []);
            $prices = $request->input('price', []);
            $amounts = $request->input('amount', []);

            foreach ($item_ids as $index => $item_id) {
                if ($item_id && isset($qtys[$index]) && $qtys[$index] > 0) {
                    DB::table('sales_return_items')->insert([
                        'sales_return_id' => $return_id,
                        'item_id' => $item_id,
                        'qty' => $qtys[$index],
                        'price' => $prices[$index],
                        'amount' => $amounts[$index],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('sales-return.index')->with('success', 'Sales Return created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error creating sales return: ' . $e->getMessage());
        }
    }

    private function generateReturnCode()
    {
        $lastReturn = DB::table('sales_returns')->orderBy('id', 'desc')->first();
        $number = $lastReturn ? intval(substr($lastReturn->return_code, 3)) + 1 : 1;
        return 'SR-' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }
}
