<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\DamageHistory;
use App\Models\StockLot;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DamageStockController extends Controller
{
    public function create()
    {
        $items = Item::orderBy('item_name')->get();
        return view('inventory.damage_create', compact('items'));
    }

    public function index(Request $request)
    {
        $query = DamageHistory::with(['item', 'user'])->orderBy('created_at', 'desc');

        if ($request->filled('item_id')) {
            $query->where('item_id', $request->item_id);
        }
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $items = Item::orderBy('item_name')->get();
        $histories = $query->paginate(25)->appends($request->all());

        return view('inventory.damage_history', compact('histories', 'items'));
    }

    public function store(Request $request)
    {
        // Log incoming payload for debugging
        Log::info('DamageStock store called', $request->all());

        $data = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|integer|exists:items,id',
            'items.*.qty' => 'required|numeric|min:0.01',
            'items.*.lot_id' => 'nullable|integer|exists:stock_lots,id',
            'items.*.unit' => 'nullable|string',
            'items.*.reason' => 'nullable|string',
        ]);

        $items = $data['items'];

        DB::beginTransaction();
        try {
            foreach ($items as $row) {
                $item = Item::lockForUpdate()->find($row['item_id']);
                if (!$item) {
                    throw new \Exception('Item not found');
                }
                $qty = floatval($row['qty']);

                // If a lot is provided, validate and decrement the lot first
                if (!empty($row['lot_id'])) {
                    $lot = StockLot::lockForUpdate()->where('id', $row['lot_id'])->where('item_id', $item->id)->first();
                    if (!$lot) {
                        throw new \Exception('Selected lot not found for item ' . $item->item_name);
                    }
                    $lotAvailable = floatval($lot->qty_available ?? 0);
                    if ($qty > $lotAvailable) {
                        throw new \Exception("Insufficient stock in lot for item {$item->item_name}. Available in lot: {$lotAvailable}");
                    }

                    // Decrement lot and overall item stock
                    $previousItem = floatval($item->current_stock ?? 0);
                    $previousLot = $lotAvailable;
                    $lot->decrement('qty_available', $qty);
                    $item->decrement('current_stock', $qty);
                    $afterItem = floatval($item->fresh()->current_stock ?? 0);

                    DamageHistory::create([
                        'item_id' => $item->id,
                        'qty' => $qty,
                        'previous_stock' => $previousItem,
                        'after_stock' => $afterItem,
                        'user_id' => auth()->id() ?? null,
                        'reason' => $row['reason'] ?? null,
                    ]);
                } else {
                    $current = floatval($item->current_stock ?? 0);
                    if ($qty > $current) {
                        throw new \Exception("Insufficient stock for item {$item->item_name}. Available: {$current}");
                    }
                    // Decrement stock and record history
                    $previous = floatval($item->current_stock ?? 0);
                    $item->decrement('current_stock', $qty);
                    $after = floatval($item->fresh()->current_stock ?? 0);

                    DamageHistory::create([
                        'item_id' => $item->id,
                        'qty' => $qty,
                        'previous_stock' => $previous,
                        'after_stock' => $after,
                        'user_id' => auth()->id() ?? null,
                        'reason' => $row['reason'] ?? null,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('stock.report')->with('success', 'Damage stock recorded and inventory updated.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', $e->getMessage());
        }
    }
}
