<?php

namespace App\Http\Controllers;

use App\Models\Grn;
use App\Models\GrnItem;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Item;
use App\Models\StockLot;
use App\Models\GrnPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GrnController extends Controller
{
    public function index()
    {
        $grns = Grn::with(['supplier', 'purchaseOrder'])->orderBy('created_at', 'desc')->get();
        
        $stats = [
            'total' => Grn::count(),
            'this_month' => Grn::whereMonth('grn_date', now()->month)->count(),
        ];
        
        return view('grns.index', compact('grns', 'stats'));
    }

    public function createFromPO($purchase_order_id)
    {
        $purchaseOrder = PurchaseOrder::with(['supplier', 'items.item'])->findOrFail($purchase_order_id);
        
        if ($purchaseOrder->status !== 'Approved') {
            return redirect()->back()->with('error', 'Only approved purchase orders can have GRN created');
        }
        
        $grn_no = $this->generateGRNNo();
        
        return view('grns.create_from_po', compact('purchaseOrder', 'grn_no'));
    }

    public function storeFromPO(Request $request)
    {
        $validated = $request->validate([
            'purchase_order_id' => 'required|exists:purchase_orders,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'grn_no' => 'required|unique:grns,grn_no',
            'grn_date' => 'required|date',
            'currency' => 'required|string|max:3',
            'exchange_rate' => 'required|numeric|min:0',
            'invoice_no' => 'nullable|string',
            'remarks' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.qty_received' => 'required|numeric|min:0.01',
            'items.*.base_cost' => 'required|numeric|min:0',
            'items.*.lot_no' => 'nullable|string',
            'items.*.expiry_date' => 'nullable|date',
            'items.*.duty_amount' => 'nullable|numeric|min:0',
            'items.*.freight_amount' => 'nullable|numeric|min:0',
            'items.*.landed_cost_per_unit' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $grn = Grn::create($validated);

            // Add GRN items
            $items = $request->input('items', []);
            
            foreach ($items as $item_data) {
                if (isset($item_data['item_id']) && $item_data['item_id'] && 
                    isset($item_data['qty_received']) && $item_data['qty_received'] > 0) {
                    
                    // Create GRN item record
                    $grnItem = GrnItem::create([
                        'grn_id' => $grn->id,
                        'item_id' => $item_data['item_id'],
                        'lot_no' => $item_data['lot_no'] ?? '',
                        'expiry_date' => !empty($item_data['expiry_date']) ? $item_data['expiry_date'] : null,
                        'qty_received' => $item_data['qty_received'],
                        'base_cost' => $item_data['base_cost'] ?? 0,
                        'duty_amount' => $item_data['duty_amount'] ?? 0,
                        'freight_amount' => $item_data['freight_amount'] ?? 0,
                        'landed_cost_per_unit' => $item_data['landed_cost_per_unit'] ?? 0,
                    ]);
                    
                    // CRITICAL: Create or update stock lot for received inventory
                    $existingLot = StockLot::where('item_id', $item_data['item_id'])
                        ->where('lot_no', $item_data['lot_no'] ?? 'GRN-' . $grn->grn_no)
                        ->first();
                    
                    if ($existingLot) {
                        // Update existing lot quantity
                        $existingLot->update([
                            'qty_available' => $existingLot->qty_available + $item_data['qty_received'],
                            'cost_price' => $item_data['landed_cost_per_unit'] ?? $item_data['base_cost'] ?? 0
                        ]);
                    } else {
                        // Create new stock lot
                        StockLot::create([
                            'item_id' => $item_data['item_id'],
                            'lot_no' => $item_data['lot_no'] ?? 'GRN-' . $grn->grn_no,
                            'expiry_date' => !empty($item_data['expiry_date']) ? $item_data['expiry_date'] : null,
                            'qty_available' => $item_data['qty_received'],
                            'cost_price' => $item_data['landed_cost_per_unit'] ?? $item_data['base_cost'] ?? 0,
                            'is_active' => true
                        ]);
                    }
                }
            }

            // Update purchase order status
            $purchaseOrder = PurchaseOrder::find($validated['purchase_order_id']);
            $purchaseOrder->update(['status' => 'Received']);

            // Store payment if provided
            if ($request->filled('amount') && $request->input('amount') > 0) {
                $paymentData = [
                    'grn_id' => $grn->id,
                    'payment_date' => $request->input('payment_date', now()->toDateString()),
                    'amount' => $request->input('amount'),
                    'payment_type' => $request->input('payment_type', 'Cash'),
                    'paid_status' => $request->input('paid_status'),
                    'paid_type' => $request->input('paid_type') ?? $request->input('paid_status'),
                    'cheque_no' => $request->input('cheque_no'),
                    'cheque_date' => $request->input('cheque_date'),
                    'payment_note' => $request->input('payment_note'),
                    'credit_due' => $request->input('credit_due') ?? null,
                    'supplier_id' => $request->input('supplier_id') ?? $purchaseOrder->supplier_id,
                ];
                \App\Models\GrnPayment::create($paymentData);
            }

            DB::commit();
            return redirect()->route('grns.index')->with('success', 'GRN created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error creating GRN: ' . $e->getMessage());
        }
    }


    public function show($id)
    {
        $grn = Grn::with(['supplier', 'purchaseOrder', 'items.item', 'payments'])->findOrFail($id);
        $payments = $grn->payments()->orderBy('payment_date', 'asc')->get();
        $total_paid = $payments->sum('amount');
        return view('grns.show', compact('grn', 'payments', 'total_paid'));
    }

    // Store a new payment for a GRN
    public function storePayment(Request $request, $grn_id)
    {
        $grn = Grn::findOrFail($grn_id);
        $validated = $request->validate([
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'payment_type' => 'required|string|max:50',
            'paid_status' => 'nullable|string|max:20',
            'paid_type' => 'nullable|string|max:20',
            'credit_due' => 'nullable|numeric',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'cheque_no' => 'nullable|string|max:100',
            'cheque_date' => 'nullable|date',
            'payment_note' => 'nullable|string|max:255',
        ]);
        $validated['grn_id'] = $grn->id;
        // Map paid_type if provided to standard field used elsewhere
        $paymentData = $validated;
        if (isset($validated['paid_type'])) {
            $paymentData['paid_status'] = $validated['paid_type'];
        }
        $paymentData['credit_due'] = $validated['credit_due'] ?? null;
        $paymentData['supplier_id'] = $validated['supplier_id'] ?? $grn->supplier_id;
        GrnPayment::create($paymentData);
        return redirect()->route('grns.show', $grn->id)->with('success', 'Payment added successfully.');
    }

    // Delete a payment for a GRN
    public function deletePayment($grn_id, $payment_id)
    {
        $grn = Grn::findOrFail($grn_id);
        $payment = GrnPayment::where('grn_id', $grn->id)->where('id', $payment_id)->firstOrFail();
        $payment->delete();
        return redirect()->route('grns.show', $grn->id)->with('success', 'Payment deleted successfully.');
    }

    public function destroy($id)
    {
        $grn = Grn::findOrFail($id);
        
        // Update PO status back if needed
        if ($grn->purchaseOrder) {
            $grn->purchaseOrder->update(['status' => 'Approved']);
        }
        
        $grn->delete();
        
        return redirect()->route('grns.index')->with('success', 'GRN deleted successfully');
    }

    /**
     * Show printable GRN invoice page
     */
    public function print($id)
    {
        $grn = Grn::with(['supplier', 'purchaseOrder', 'items.item', 'payments'])->findOrFail($id);
        // You may want to fetch company info from config or DB
        $company_name = config('app.name');
        $company_address = config('app.address', '');
        $company_mobile = config('app.phone', '');
        $company_email = config('app.email', '');
        return view('grns.print', compact('grn', 'company_name', 'company_address', 'company_mobile', 'company_email'));
    }

    private function generateGRNNo()
    {
        $lastGRN = Grn::orderBy('id', 'desc')->first();
        $number = $lastGRN ? intval(substr($lastGRN->grn_no, 4)) + 1 : 1;
        return 'GRN-' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }
}
