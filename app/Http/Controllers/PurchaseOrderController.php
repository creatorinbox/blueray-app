<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    public function index()
    {
        $purchaseOrders = PurchaseOrder::with('supplier')->orderBy('created_at', 'desc')->get();
        
        $stats = [
            'total' => PurchaseOrder::count(),
            'draft' => PurchaseOrder::where('status', 'Draft')->count(),
            'pending' => PurchaseOrder::where('status', 'Pending')->count(),
            'approved' => PurchaseOrder::where('status', 'Approved')->count(),
        ];
        
        return view('purchase_orders.index', compact('purchaseOrders', 'stats'));
    }

    public function create()
    {
        $suppliers = Supplier::orderBy('supplier_name')->get();
        $items = Item::orderBy('item_name')->get();
        $companies = \App\Models\Company::orderBy('company_name')->get();
        $po_no = $this->generatePONo();
        return view('purchase_orders.create', compact('suppliers', 'items', 'companies', 'po_no'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'company_id' => 'required|exists:companies,id',
            'po_no' => 'required|unique:purchase_orders,po_no',
            'po_date' => 'required|date',
            'currency' => 'required|string|max:3',
            'currency_value' => 'nullable|numeric|min:0',
            'status' => 'required|in:Draft,Pending,Approved,Received,Cancelled',
            'remarks' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.qty' => 'required|numeric|min:0.01',
            'items.*.rate' => 'required|numeric|min:0',
            'items.*.amount' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $items = $request->input('items', []);
            $total = 0;
            foreach ($items as $item_data) {
                if (isset($item_data['item_id']) && $item_data['item_id'] && isset($item_data['qty']) && $item_data['qty'] > 0) {
                    $total += isset($item_data['amount']) ? floatval($item_data['amount']) : (floatval($item_data['qty']) * floatval($item_data['rate']));
                }
            }
            $validated['total_amount'] = $total;
            $currencyValue = floatval($request->input('currency_value', 1));
            $validated['currency_value'] = $currencyValue;
            $validated['total_amount_omr'] = $total * $currencyValue;
            $purchaseOrder = PurchaseOrder::create($validated);
            // Add items
            foreach ($items as $item_data) {
                if (isset($item_data['item_id']) && $item_data['item_id'] && isset($item_data['qty']) && $item_data['qty'] > 0) {
                    PurchaseOrderItem::create([
                        'purchase_order_id' => $purchaseOrder->id,
                        'item_id' => $item_data['item_id'],
                        'qty' => $item_data['qty'],
                        'rate' => $item_data['rate'],
                        'amount' => $item_data['amount'],
                    ]);
                }
            }
            DB::commit();
            return redirect()->route('purchase-orders.index')->with('success', 'Purchase Order created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error creating purchase order: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $purchaseOrder = PurchaseOrder::with(['supplier', 'items.item'])->findOrFail($id);
        return view('purchase_orders.show', compact('purchaseOrder'));
    }

    public function edit($id)
    {
        $purchaseOrder = PurchaseOrder::with('items.item')->findOrFail($id);
        $suppliers = Supplier::orderBy('supplier_name')->get();
        $items = Item::orderBy('item_name')->get();
        $companies = \App\Models\Company::orderBy('company_name')->get();
        return view('purchase_orders.edit', compact('purchaseOrder', 'suppliers', 'items', 'companies'));
    }

    public function update(Request $request, $id)
    {
        $purchaseOrder = PurchaseOrder::findOrFail($id);
        
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'company_id' => 'required|exists:companies,id',
            'po_date' => 'required|date',
            'currency' => 'required|string|max:3',
            'currency_value' => 'nullable|numeric|min:0',
            'status' => 'required|in:Draft,Pending,Approved,Received,Cancelled',
            'remarks' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.qty' => 'required|numeric|min:0.01',
            'items.*.rate' => 'required|numeric|min:0',
            'items.*.amount' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $items = $request->input('items', []);
            $total = 0;
            foreach ($items as $item_data) {
                if (isset($item_data['item_id']) && $item_data['item_id'] && isset($item_data['qty']) && $item_data['qty'] > 0) {
                    $total += isset($item_data['amount']) ? floatval($item_data['amount']) : (floatval($item_data['qty']) * floatval($item_data['rate']));
                }
            }
            $validated['total_amount'] = $total;
            $currencyValue = floatval($request->input('currency_value', 1));
            $validated['currency_value'] = $currencyValue;
            $validated['total_amount_omr'] = $total * $currencyValue;
            $purchaseOrder->update($validated);
            // Delete old items and add new ones
            $purchaseOrder->items()->delete();
            foreach ($items as $item_data) {
                if (isset($item_data['item_id']) && $item_data['item_id'] && isset($item_data['qty']) && $item_data['qty'] > 0) {
                    PurchaseOrderItem::create([
                        'purchase_order_id' => $purchaseOrder->id,
                        'item_id' => $item_data['item_id'],
                        'qty' => $item_data['qty'],
                        'rate' => $item_data['rate'],
                        'amount' => $item_data['amount'],
                    ]);
                }
            }
            DB::commit();
            return redirect()->route('purchase-orders.show', $purchaseOrder->id)->with('success', 'Purchase Order updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error updating purchase order: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $purchaseOrder = PurchaseOrder::findOrFail($id);
        $purchaseOrder->delete();
        
        return redirect()->route('purchase-orders.index')->with('success', 'Purchase Order deleted successfully');
    }

    /**
     * Generate and download a PDF for the purchase order.
     */
    public function print($id)
    {
        $purchaseOrder = PurchaseOrder::with(['supplier', 'items'])->findOrFail($id);
        $company = auth()->user()->company;
        $pdf = \PDF::loadView('purchase_orders.print', compact('purchaseOrder', 'company'));
        $filename = 'PurchaseOrder-' . $purchaseOrder->po_no . '.pdf';
        return $pdf->download($filename);
    }

    private function generatePONo()
    {
        $lastPO = PurchaseOrder::orderBy('id', 'desc')->first();
        $number = $lastPO ? intval(substr($lastPO->po_no, 3)) + 1 : 1;
        return 'PO-' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }
}
