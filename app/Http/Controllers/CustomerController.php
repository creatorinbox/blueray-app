<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::orderBy('customer_name')->get();
        
        $stats = [
            'total_customers' => Customer::count(),
            'active_customers' => Customer::where('is_active', true)->count(),
        ];
        
        return view('customers.index', compact('customers', 'stats'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_username' => 'nullable|string|max:255',
            'designation' => 'nullable|string|max:255',
            'trn' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'alt_email' => 'nullable|email|max:255',
            'gstin' => 'nullable|string|max:15',
            'tax_number' => 'nullable|string|max:20',
            'credit_limit' => 'nullable|numeric|min:0',
            'opening_balance' => 'nullable|numeric',
            'custom_period' => 'nullable|string|max:255',
            'payment_terms_days' => 'nullable|integer|min:0',
            'country' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'postcode' => 'nullable|string|max:10',
            'address' => 'nullable|string',
            'customer_notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        
        // Custom validation: either gstin or tax_number should be provided if any is filled
        if (($request->filled('gstin') || $request->filled('tax_number')) && 
            (empty($request->gstin) && empty($request->tax_number))) {
            return back()->withErrors([
                'gstin' => 'Either GST Number or Tax Number is required',
                'tax_number' => 'Either GST Number or Tax Number is required'
            ])->withInput();
        }

        // Set default values
        $validated['company_id'] = 1; // Default company
        $validated['is_active'] = $validated['is_active'] ?? true;

        $customer = Customer::create($validated);

        return redirect()->route('customers.index')
            ->with('success', 'Customer created successfully');
    }

    public function show($id)
    {
        $customer = Customer::findOrFail($id);
        return view('customers.show', compact('customer'));
    }

    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_username' => 'nullable|string|max:255',
            'designation' => 'nullable|string|max:255',
            'trn' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'alt_email' => 'nullable|email|max:255',
            'gstin' => 'nullable|string|max:15',
            'tax_number' => 'nullable|string|max:20',
            'credit_limit' => 'nullable|numeric|min:0',
            'opening_balance' => 'nullable|numeric',
            'custom_period' => 'nullable|string|max:255',
            'payment_terms_days' => 'nullable|integer|min:0',
            'country' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'postcode' => 'nullable|string|max:10',
            'address' => 'nullable|string',
            'customer_notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        
        // Custom validation: either gstin or tax_number should be provided if any is filled
        if (($request->filled('gstin') || $request->filled('tax_number')) && 
            (empty($request->gstin) && empty($request->tax_number))) {
            return back()->withErrors([
                'gstin' => 'Either GST Number or Tax Number is required',
                'tax_number' => 'Either GST Number or Tax Number is required'
            ])->withInput();
        }

        $customer->update($validated);

        return redirect()->route('customers.index')
            ->with('success', 'Customer updated successfully');
    }

    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        
        // Check if customer has quotations
        if ($customer->quotations()->exists()) {
            return back()->with('error', 'Cannot delete customer with existing quotations');
        }

        $customer->delete();

        return redirect()->route('customers.index')
            ->with('success', 'Customer deleted successfully');
    }
}