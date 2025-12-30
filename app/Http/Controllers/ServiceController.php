<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $services = Item::where('stock_type', 'Service')->orderBy('created_at', 'desc')->get();
        return view('services.index', compact('services'));
    }

    public function create()
    {
        return view('services.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_name' => 'required|string|max:255',
            'sale_price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        try {
            $data = [
                'item_name' => $validated['item_name'],
                'item_code' => 'SRV-' . time(),
                'company_id' => 1,
                'item_type' => 'Service',
                'stock_type' => 'Service',
                'unit' => 'Service',
                'description' => $validated['description'] ?? null,
                'sale_price' => $validated['sale_price'],
                'min_sale_price' => $validated['sale_price'],
                'vat_applicable' => false,
                'vat_rate' => 0,
                'is_active' => true,
            ];

            Item::create($data);

            return redirect()->route('services.index')->with('success', 'Service created successfully');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error creating service: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $service = Item::findOrFail($id);
        return view('services.edit', compact('service'));
    }

    public function update(Request $request, $id)
    {
        $service = Item::findOrFail($id);

        $validated = $request->validate([
            'item_name' => 'required|string|max:255',
            'sale_price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $service->update([
            'item_name' => $validated['item_name'],
            'description' => $validated['description'] ?? null,
            'sale_price' => $validated['sale_price'],
            'min_sale_price' => $validated['sale_price'],
        ]);

        return redirect()->route('services.index')->with('success', 'Service updated successfully');
    }

    public function destroy($id)
    {
        $service = Item::findOrFail($id);
        $service->delete();
        return redirect()->route('services.index')->with('success', 'Service deleted');
    }
}
