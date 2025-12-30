<?php

namespace App\Http\Controllers;

use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnItem;
use App\Models\Supplier;
use App\Models\Grn;
use App\Models\Item;
use App\Models\StockLot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseReturnController extends Controller
{
    public function index()
    {
        $returns = PurchaseReturn::with(['supplier'])->orderBy('date', 'desc')->get();
        return view('purchase_returns.index', compact('returns'));
    }

    public function create()
    {
        $suppliers = Supplier::orderBy('supplier_name')->get();
        $grns = Grn::orderBy('grn_date', 'desc')->get();
        $items = Item::orderBy('item_name')->get();
        return view('purchase_returns.create', compact('suppliers', 'grns', 'items'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'return_no' => 'required|unique:purchase_returns,return_no',
            'date' => 'required|date',
            'supplier_id' => 'required|exists:suppliers,id',
            'grn_id' => 'nullable|exists:grns,id',
            'reason' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.lot_id' => 'nullable|exists:stock_lots,id',
            'items.*.qty' => 'required|numeric|min:0.01',
            'items.*.rate' => 'required|numeric|min:0',
            'items.*.amount' => 'required|numeric|min:0',
            'items.*.reason' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $total = 0;
            foreach ($validated['items'] as $item) {
                $total += $item['amount'];
            }
            $vat_reversal = round($total * 0.05, 3);
            $purchaseReturn = PurchaseReturn::create([
                'return_no' => $validated['return_no'],
                'date' => $validated['date'],
                'supplier_id' => $validated['supplier_id'],
                'grn_id' => $validated['grn_id'] ?? null,
                'total_amount' => $total,
                'reason' => $validated['reason'] ?? null,
                'vat_reversal' => $vat_reversal,
            ]);
            foreach ($validated['items'] as $item) {
                PurchaseReturnItem::create([
                    'purchase_return_id' => $purchaseReturn->id,
                    'item_id' => $item['item_id'],
                    'lot_id' => $item['lot_id'] ?? null,
                    'qty' => $item['qty'],
                    'rate' => $item['rate'],
                    'amount' => $item['amount'],
                    'reason' => $item['reason'] ?? null,
                ]);
            }
            DB::commit();
            return redirect()->route('purchase-returns.index')->with('success', 'Purchase return created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error creating purchase return: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $return = PurchaseReturn::with(['supplier', 'grn', 'items.item', 'items.lot'])->findOrFail($id);
        return view('purchase_returns.show', compact('return'));
    }

    public function destroy($id)
    {
        $return = PurchaseReturn::findOrFail($id);
        $return->delete();
        return redirect()->route('purchase-returns.index')->with('success', 'Purchase return deleted successfully');
    }
}
