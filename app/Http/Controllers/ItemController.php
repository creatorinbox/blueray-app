<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Company;
use App\Models\Supplier;
use App\Models\StockLot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ItemController extends Controller
{
    public function index()
    {
        $items = Item::with(['company', 'supplier', 'stockLots'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Calculate stock quantities for each item
        $items->map(function ($item) {
            $item->current_stock = $item->stockLots->sum('qty_available');
            $item->total_value = $item->stockLots->sum(function($lot) {
                return $lot->qty_available * $lot->cost_price;
            });
            return $item;
        });
        
        $stats = [
            'total_items' => Item::count(),
            'active_items' => Item::where('is_active', true)->count(),
            'low_stock_items' => Item::whereHas('stockLots', function($q) {
                $q->having(DB::raw('SUM(qty_available)'), '<', 10);
            })->count(),
            'total_value' => $items->sum('total_value'),
        ];
        
        return view('items.index', compact('items', 'stats'));
    }

    public function create()
    {
        $units = \App\Models\Unit::orderBy('name')->get();
        return view('items.create', compact('units'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'item_name' => 'required|string|max:255',
                'unit' => 'required|string|max:20',
                'oem_part_no' => 'nullable|string|max:100|unique:items,oem_part_no',
                'duplicate_part_no' => 'nullable|string|max:100',
                'hsn_code' => 'nullable|string|max:50',
                'barcode' => 'nullable|string|max:100|unique:items,barcode',
                'description' => 'nullable|string',
                'sale_price' => 'required|numeric|min:0',
                'purchase_price' => 'nullable|numeric|min:0',
                'currency' => 'nullable|string|max:3',
                'currency_value' => 'nullable|numeric|min:0',
                'profit_margin' => 'nullable|numeric|min:0|max:100',
            ]);
            
            // Set default and required values
            $validated['item_code'] = $this->generateItemCode();
            $validated['company_id'] = 1; // Default company ID
            $validated['item_type'] = 'Product';
            $validated['stock_type'] = 'Stock';
            $validated['min_sale_price'] = $validated['sale_price'];
            $validated['vat_applicable'] = false;
            $validated['vat_rate'] = 0;
            $validated['is_active'] = true;
            
            // If oem_part_no is empty, generate a unique one
            if (empty($validated['oem_part_no'])) {
                $validated['oem_part_no'] = 'OEM-' . time() . '-' . rand(1000, 9999);
            }

            $item = Item::create($validated);

            // Opening stock creation removed — manage stock via GRN/PO/Stock Lots

            return redirect()->route('items.index')->with('success', 'Item created successfully');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error creating item: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $item = Item::with(['company', 'supplier', 'stockLots', 'quotationItems', 'quotationItems.quotation'])
            ->findOrFail($id);
        
        // Calculate stock summary
        $stockSummary = [
            'current_stock' => $item->stockLots->sum('qty_available'),
            'total_value' => $item->stockLots->sum(function($lot) {
                return $lot->qty_available * $lot->cost_price;
            }),
            'avg_cost' => $item->stockLots->where('qty_available', '>', 0)->avg('cost_price') ?? 0,
            'stock_lots' => $item->stockLots->where('qty_available', '>', 0)->count(),
        ];
        
        return view('items.show', compact('item', 'stockSummary'));
    }

    public function edit($id)
    {
        $item = Item::findOrFail($id);
        $units = \App\Models\Unit::orderBy('name')->get();
        return view('items.edit', compact('item','units'));
    }

    public function update(Request $request, $id)
    {
        $item = Item::findOrFail($id);
        
        $validated = $request->validate([
            'item_name' => 'required|string|max:255',
            'unit' => 'required|string|max:20',
            'oem_part_no' => 'nullable|string|max:100|unique:items,oem_part_no,' . $id,
            'duplicate_part_no' => 'nullable|string|max:100',
            'hsn_code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'barcode' => 'nullable|string|max:100|unique:items,barcode,' . $id,
            'sale_price' => 'required|numeric|min:0',
            'purchase_price' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:3',
            'currency_value' => 'nullable|numeric|min:0',
            'profit_margin' => 'nullable|numeric|min:0|max:100',
        ]);

        // Keep existing values for fields not in form
        $validated['company_id'] = $item->company_id;
        $validated['item_type'] = $item->item_type;
        $validated['stock_type'] = $item->stock_type;
        $validated['min_sale_price'] = $validated['sale_price'];
        $validated['vat_applicable'] = $item->vat_applicable;
        $validated['vat_rate'] = $item->vat_rate;
        $validated['is_active'] = $item->is_active;

        // Handle opening stock changes
        $oldOpeningStock = $item->opening_stock;
        $newOpeningStock = $validated['opening_stock'] ?? 0;
        
        $item->update($validated);
        
        // Opening stock handling removed — manage stock via GRN/PO/Stock Lots
        
        return redirect()->route('items.index')->with('success', 'Item updated successfully');
    }

    public function destroy($id)
    {
        $item = Item::findOrFail($id);
        
        // Check if item has stock
        $hasStock = $item->stockLots()->where('qty_available', '>', 0)->exists();
        if ($hasStock) {
            return back()->with('error', 'Cannot delete item with existing stock');
        }
        
        // Check if item is used in transactions
        $hasTransactions = $item->quotationItems()->exists();
        if ($hasTransactions) {
            return back()->with('error', 'Cannot delete item used in transactions');
        }
        
        $item->delete();
        
        return redirect()->route('items.index')->with('success', 'Item deleted successfully');
    }

    public function stockReport()
    {
        $items = Item::with(['stockLots' => function($q) {
                $q->where('qty_available', '>', 0);
            }])
            ->whereHas('stockLots', function($q) {
                $q->where('qty_available', '>', 0);
            })
            ->orderBy('item_name')
            ->get();
        
        $items->map(function ($item) {
            $item->current_stock = $item->stockLots->sum('qty_available');
            $item->total_value = $item->stockLots->sum(function($lot) {
                return $lot->qty_available * $lot->cost_price;
            });
            $item->avg_cost = $item->stockLots->where('qty_available', '>', 0)->avg('cost_price') ?? 0;
            return $item;
        });
        
        return view('items.stock_report', compact('items'));
    }

    public function lowStockReport()
    {
        $items = Item::with(['stockLots'])
            ->get()
            ->filter(function ($item) {
                $currentStock = $item->stockLots->sum('qty_available');
                return $currentStock < 10; // Low stock threshold
            })
            ->sortBy('item_name');
        
        $items->map(function ($item) {
            $item->current_stock = $item->stockLots->sum('qty_available');
            return $item;
        });
        
        return view('items.low_stock_report', compact('items'));
    }


    /**
     * Search items by lot details (lot_no, expiry_date, supplier, price, etc.)
     */
    public function searchByLot(Request $request)
    {
        $query = StockLot::query()->with('item');

        if ($request->filled('lot_no')) {
            $query->where('lot_no', 'like', '%' . $request->lot_no . '%');
        }
        if ($request->filled('expiry_date')) {
            $query->where('expiry_date', $request->expiry_date);
        }
        if ($request->filled('supplier_id')) {
            $query->whereHas('item.supplier', function($q) use ($request) {
                $q->where('id', $request->supplier_id);
            });
        }
        if ($request->filled('price_min')) {
            $query->where('cost_price', '>=', $request->price_min);
        }
        if ($request->filled('price_max')) {
            $query->where('cost_price', '<=', $request->price_max);
        }
        if ($request->filled('qty_min')) {
            $query->where('qty_available', '>=', $request->qty_min);
        }
        if ($request->filled('qty_max')) {
            $query->where('qty_available', '<=', $request->qty_max);
        }

        $lots = $query->get();

        return view('items.lot_search', compact('lots'));
    }

    /**
     * API: search stock lots (for AJAX). Returns JSON.
     * Supports query param `q` to search item name, item code or lot_no.
     */
    public function apiSearchLots(Request $request)
    {
        $q = $request->get('q', '');

        // If caller requests lookup by id (used when selecting a specific lot),
        // return the lot matching that id (do not enforce qty > 0 filter).
        if ($request->filled('by_id') && is_numeric($q)) {
            $query = StockLot::with('item')->where('id', intval($q));
        } else {
            $query = StockLot::with('item')
                ->when($q, function($qbuilder) use ($q) {
                    $qbuilder->where('lot_no', 'like', "%{$q}%")
                        ->orWhereHas('item', function($iq) use ($q) {
                            $iq->where('item_name', 'like', "%{$q}%")
                            ->orWhere('item_code', 'like', "%{$q}%");
                        });
                })
                ->where('qty_available', '>', 0)
                ->orderBy('expiry_date', 'asc')
                ->limit(50);
        }

        $lots = $query->get()->map(function($lot) {
            $expiry = $lot->expiry_date; // Carbon or null
            $expired = $expiry ? $expiry->isPast() : false;
            $expiringSoon = false;
            if ($expiry && $expiry->isFuture()) {
                $expiringSoon = $expiry->diffInDays(now()) < 30;
            }

            return [
                'id' => $lot->id,
                'lot_no' => $lot->lot_no,
                'qty_available' => (float) $lot->qty_available,
                'cost_price' => (float) $lot->cost_price,
                'expiry_date' => $expiry ? $expiry->toDateString() : null,
                'expired' => $expired,
                'expiring_soon' => $expiringSoon,
                'item' => [
                    'id' => $lot->item->id,
                    'item_name' => $lot->item->item_name,
                    'item_code' => $lot->item->item_code,
                    'sale_price' => (float) $lot->item->sale_price,
                    'vat_applicable' => (bool) $lot->item->vat_applicable,
                    'vat_rate' => (float) $lot->item->vat_rate,
                    'supplier' => $lot->item->supplier ? [
                        'id' => $lot->item->supplier->id,
                        'supplier_name' => $lot->item->supplier->supplier_name
                    ] : null,
                ]
            ];
        });

        return response()->json($lots);
    }

    /**
     * API: return latest purchase cost for an item (from stock_lots)
     */
    public function apiLatestCost($id)
    {
        $latestLot = StockLot::where('item_id', $id)
            ->where('qty_available', '>', 0)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($latestLot) {
            return response()->json(['cost_price' => (float)$latestLot->cost_price]);
        }

        // fallback: return null to indicate no purchase history (frontend will decide what to show)
        return response()->json(['cost_price' => null]);
    }

    private function generateItemCode()
    {
        // Get the max numeric part of item_code from all items
        $maxCode = Item::selectRaw("MAX(CAST(SUBSTRING(item_code, 5) AS UNSIGNED)) as max_code")->value('max_code');
        $number = $maxCode ? $maxCode + 1 : 1;
        return 'ITM-' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }
}