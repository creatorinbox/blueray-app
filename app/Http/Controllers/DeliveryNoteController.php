<?php

namespace App\Http\Controllers;

use App\Models\SalesInvoice;
use App\Models\Customer;
use App\Models\DeliveryNote;
use App\Models\DeliveryNoteItem;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeliveryNoteController extends Controller
{
    public function index()
    {
        $deliveries = DeliveryNote::with(['customer'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        $stats = [
            'total_deliveries' => DeliveryNote::count(),
            'pending_delivery' => DeliveryNote::where('delivery_status', 'Pending')->count(),
            'delivered' => DeliveryNote::where('delivery_status', 'Delivered')->count(),
        ];
        
        return view('deliveries.index', compact('deliveries', 'stats'));
    }

    public function create()
    {
        $customers = Customer::where('is_active', true)->orderBy('customer_name')->get();
        $items = Item::where('is_active', true)->orderBy('item_name')->get();
        
        return view('deliveries.create', compact('customers', 'items'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'delivery_date' => 'required|date',
            'reference_no' => 'nullable|string|max:255',
            'subject' => 'nullable|string|max:255',
            'delivery_notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0.001',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            $deliveryNote = DeliveryNote::create([
                'customer_id' => $request->customer_id,
                'delivery_date' => $request->delivery_date,
                'reference_no' => $request->reference_no,
                'subject' => $request->subject,
                'delivery_status' => 'Pending',
                'subtotal' => 0,
                'tax_amount' => 0,
                'discount_amount' => 0,
                'total_amount' => 0,
                'delivery_notes' => $request->delivery_notes,
                'created_by' => auth()->id(),
            ]);

            $subtotal = 0;
            $totalTax = 0;
            $totalDiscount = 0;

            foreach ($request->items as $itemData) {
                $quantity = floatval($itemData['quantity']);
                $unitPrice = floatval($itemData['unit_price']);
                $discountAmount = floatval($itemData['discount_amount'] ?? 0);
                $taxRate = floatval($itemData['tax_rate'] ?? 0);
                
                $itemTotal = $quantity * $unitPrice;
                $itemTotalAfterDiscount = $itemTotal - $discountAmount;
                $taxAmount = ($itemTotalAfterDiscount * $taxRate) / 100;
                $finalTotal = $itemTotalAfterDiscount + $taxAmount;

                DeliveryNoteItem::create([
                    'delivery_note_id' => $deliveryNote->id,
                    'item_id' => $itemData['item_id'] ?? null,
                    'item_name' => $itemData['item_name'],
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'discount_amount' => $discountAmount,
                    'tax_rate' => $taxRate,
                    'tax_amount' => $taxAmount,
                    'total_amount' => $finalTotal,
                    'description' => $itemData['description'] ?? '',
                    'serial_number' => $itemData['serial_number'] ?? '',
                ]);

                $subtotal += $itemTotal;
                $totalTax += $taxAmount;
                $totalDiscount += $discountAmount;
            }

            $deliveryNote->update([
                'subtotal' => $subtotal,
                'tax_amount' => $totalTax,
                'discount_amount' => $totalDiscount,
                'total_amount' => $subtotal - $totalDiscount + $totalTax,
            ]);
        });

        return redirect()->route('deliveries.index')
            ->with('success', 'Delivery note created successfully');
    }

    public function show($id)
    {
        $deliveryNote = DeliveryNote::with(['customer', 'items', 'createdBy'])
            ->findOrFail($id);
        
        return view('deliveries.show', compact('deliveryNote'));
    }

    public function print($id)
    {
        $deliveryNote = DeliveryNote::with(['customer', 'items', 'createdBy'])
            ->findOrFail($id);
        
        return view('deliveries.print', compact('deliveryNote'));
    }

    public function updateStatus(Request $request, $id)
    {
        $deliveryNote = DeliveryNote::findOrFail($id);
        
        $validated = $request->validate([
            'delivery_status' => 'required|in:Pending,Delivered,Cancelled',
            'delivery_notes' => 'nullable|string',
        ]);

        $deliveryNote->update($validated);

        return redirect()->route('deliveries.index')
            ->with('success', 'Delivery status updated successfully');
    }

    public function getItems(Request $request)
    {
        try {
            $search = $request->get('search', '');
            
            \Log::info('Item search requested', ['search' => $search]);
            
            $items = Item::where('is_active', true)
                ->where(function ($query) use ($search) {
                    $query->where('item_name', 'like', "%{$search}%")
                        ->orWhere('item_code', 'like', "%{$search}%")
                        ->orWhere('barcode', 'like', "%{$search}%");
                })
                ->limit(10)
                ->get(['id', 'item_name', 'item_code', 'sale_price']);

            \Log::info('Items found', ['count' => $items->count()]);

            return response()->json($items);
        } catch (\Exception $e) {
            \Log::error('Error in getItems', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'An error occurred'], 500);
        }
    }
}