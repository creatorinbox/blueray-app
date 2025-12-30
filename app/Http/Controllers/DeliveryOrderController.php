<?php

namespace App\Http\Controllers;

use App\Models\DeliveryOrder;
use App\Models\DeliveryOrderItem;
use App\Models\Item;
use App\Models\Customer;
use App\Models\StockLot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class DeliveryOrderController extends Controller
{
    public function index()
    {
        $orders = DeliveryOrder::with('customer')->latest()->get();
        return view('delivery_orders.index', compact('orders'));
    }

    public function create(Request $request)
    {
        $customers = Customer::all();
        $items = Item::all();

        $prefillInvoice = null;
        $prefillItems = [];
        if ($request->filled('invoice_id')) {
            $invoice = \App\Models\SalesInvoice::with(['customer'])->find($request->invoice_id);
            if ($invoice) {
                $prefillInvoice = [
                    'id' => $invoice->id,
                    'invoice_no' => $invoice->invoice_no,
                    'customer_id' => $invoice->customer_id,
                ];
                $invoiceItems = \App\Models\SalesInvoiceItem::with('item')->where('sales_invoice_id', $invoice->id)->get();
                foreach ($invoiceItems as $ii) {
                    $prefillItems[] = [
                        'item' => [
                            'id' => $ii->item->id,
                            'item_name' => $ii->item->item_name,
                            'item_code' => $ii->item->item_code,
                            'sale_price' => $ii->sale_price ?? $ii->item->sale_price,
                            'supplier' => $ii->item->supplier ? ['supplier_name' => $ii->item->supplier->supplier_name] : null,
                        ],
                        'lot_id' => $ii->lot_id,
                        'quantity' => $ii->qty,
                    ];
                }
            }
        }

        return view('delivery_orders.create', compact('customers', 'items', 'prefillInvoice', 'prefillItems'));
    }

    public function store(Request $request)
    {
        $rules = [
            'customer_id' => 'required|exists:customers,id',
            'delivery_date' => 'required|date',
            'invoice_no' => 'nullable|string|max:255',
            'items' => 'required|array',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.lot_id' => 'nullable|integer|exists:stock_lots,id',
            'remarks' => 'nullable|string',
        ];

        $validator = Validator::make($request->all(), $rules);

        // Debug: log incoming items payload
        try { Log::info('DeliveryOrder store payload items', ['user_id' => optional($request->user())->id, 'items' => $request->input('items')]); } catch (\Exception $e) { Log::error('Failed to log delivery order items: '.$e->getMessage()); }

        if ($validator->fails()) {
            // Log context for debugging: items payload size and first item
            try {
                $items = $request->input('items');
                Log::warning('DeliveryOrder validation failed', [
                    'user_id' => optional($request->user())->id,
                    'items_count' => is_array($items) ? count($items) : 0,
                    'items_preview' => is_array($items) && count($items) ? $items[0] : null,
                    'errors' => $validator->errors()->all(),
                ]);
            } catch (\Exception $e) {
                Log::error('Error logging delivery order validation failure: ' . $e->getMessage());
            }

            return redirect()->back()->withInput()->withErrors($validator);
        }

        $validated = $validator->validated();

        DB::transaction(function () use ($validated) {
            $order = DeliveryOrder::create([
                'customer_id' => $validated['customer_id'],
                'delivery_date' => $validated['delivery_date'],
                'invoice_no' => $validated['invoice_no'] ?? null,
                'status' => 'pending',
                'remarks' => $validated['remarks'] ?? null,
            ]);

            foreach ($validated['items'] as $item) {
                DeliveryOrderItem::create([
                    'delivery_order_id' => $order->id,
                    'item_id' => $item['item_id'],
                    'quantity' => $item['quantity'],
                    'lot_id' => $item['lot_id'] ?? null,
                ]);
            }
        });

        return redirect()->route('delivery_orders.index')->with('success', 'Delivery order created.');
    }

    public function show(DeliveryOrder $deliveryOrder)
    {
        $deliveryOrder->load('items.item', 'customer');
        return view('delivery_orders.show', compact('deliveryOrder'));
    }

    /**
     * Render a printable Store copy (includes lot number & expiry)
     */
    public function printStore(DeliveryOrder $delivery_order)
    {
        $delivery_order->load(['items.item', 'items.lot', 'customer']);
        return view('delivery_orders.print_store', ['order' => $delivery_order]);
    }

    /**
     * Render a printable Customer copy (hide lot number & expiry)
     */
    public function printCustomer(DeliveryOrder $delivery_order)
    {
        $delivery_order->load(['items.item', 'items.lot', 'customer']);
        return view('delivery_orders.print_customer', ['order' => $delivery_order]);
    }

    public function complete(DeliveryOrder $deliveryOrder)
    {
        if ($deliveryOrder->status !== 'completed') {
            $deliveryOrder->load('items');
            foreach ($deliveryOrder->items as $orderItem) {
                // If lot specified, decrement that lot's qty_available
                if (!empty($orderItem->lot_id)) {
                    $lot = StockLot::find($orderItem->lot_id);
                    if ($lot) {
                        $lot->qty_available = max(0, $lot->qty_available - $orderItem->quantity);
                        $lot->save();
                    }
                } else {
                    // Fallback: try to deduct from any available lot for the item (FIFO by expiry)
                    $lot = StockLot::where('item_id', $orderItem->item_id)->where('qty_available', '>', 0)->orderBy('expiry_date')->first();
                    if ($lot) {
                        $lot->qty_available = max(0, $lot->qty_available - $orderItem->quantity);
                        $lot->save();
                    }
                }
            }
            $deliveryOrder->status = 'completed';
            $deliveryOrder->save();
        }
        // Redirect to index to show updated list (more robust than redirecting to show route)
        return redirect()->route('delivery_orders.index')->with('success', 'Delivery order completed and stock updated.');
    }
}
