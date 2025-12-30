<?php

namespace App\Http\Controllers;

use App\Models\AmcService;
use App\Models\Customer;
use Illuminate\Http\Request;

class AmcServiceController extends Controller
{
    public function index()
    {
        $amcs = AmcService::with('customer')->orderBy('start_date', 'desc')->get();
        return view('amc_services.index', compact('amcs'));
    }

    public function create()
    {
        $customers = Customer::orderBy('customer_name')->get();
        return view('amc_services.create', compact('customers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'amc_no' => 'required|unique:amc_services,amc_no',
            'customer_id' => 'required|exists:customers,id',
            'service_item' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'amc_type' => 'required|in:Labour,Comprehensive',
            'contract_value' => 'required|numeric|min:0',
            'vat' => 'required|numeric|min:0',
            'invoice_ref' => 'nullable|string',
        ]);
        $validated['status'] = now()->between($validated['start_date'], $validated['end_date']) ? 'Active' : 'Expired';
        AmcService::create($validated);
        return redirect()->route('amc-services.index')->with('success', 'AMC created successfully');
    }
}
