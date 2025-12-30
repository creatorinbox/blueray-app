<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Customer;
use App\Models\Item;
use App\Models\ApprovalLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QuotationController extends Controller
{
    public function index(Request $request)
    {
        $quotations = Quotation::with(['customer', 'creator', 'approver', 'items.item.supplier'])
            ->where('company_id', auth()->user()->company_id)
            ->orderBy('created_at', 'desc')
            ->paginate(25);
            
        return view('quotations.index', compact('quotations'));
    }

    public function create()
    {
        $customers = Customer::where('company_id', auth()->user()->company_id)
                           ->where('is_active', true)
                           ->orderBy('customer_name')
                           ->get();
        
        $items = Item::where('company_id', auth()->user()->company_id)
                    ->where('is_active', true)
                    ->with('supplier')
                    ->withSum('stockLots as total_stock', 'qty_available')
                    ->orderBy('item_name')
                    ->get();
        
        // Generate quotation number
        $year = date('Y');
        $lastQuotation = Quotation::where('company_id', auth()->user()->company_id)
                                 ->whereYear('created_at', $year)
                                 ->orderBy('id', 'desc')
                                 ->first();
        
        $nextNumber = $lastQuotation ? (intval(substr($lastQuotation->quotation_no, -4)) + 1) : 1;
        $quotationNo = 'QTN-' . $year . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        
        return view('quotations.create', compact('customers', 'items', 'quotationNo'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'quotation_date' => 'required|date',
            'valid_till' => 'required|date|after:quotation_date',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.qty' => 'required|numeric|min:0.01',
            'items.*.rate' => 'required|numeric|min:0.01',
        ]);

        try {
            // Log incoming items for debugging lot_id presence
            try { Log::info('Quotation store payload items', ['user_id' => optional($request->user())->id, 'items' => $request->input('items')]); } catch (\Exception $e) { Log::error('Failed to log quotation items: '.$e->getMessage()); }
            DB::beginTransaction();
            
            // Calculate totals
            $subtotal = 0;
            $totalVat = 0;
            
            foreach ($request->items as $itemData) {
                $item = Item::find($itemData['item_id']);
                $lineTotal = $itemData['qty'] * $itemData['rate'];
                $subtotal += $lineTotal;
                
                if ($item->vat_applicable) {
                    $totalVat += $lineTotal * ($item->vat_rate / 100);
                }
            }
            
            $discount_percent = $request->input('global_discount_percent', 0);
            $discount_amount = ($subtotal * $discount_percent) / 100;
            $tax_percent = $request->input('global_tax_percent', 0);
            $taxable = $subtotal - $discount_amount;
            $tax_amount = ($taxable * $tax_percent) / 100;
            // Correct total_amount calculation: subtotal - discount + tax
            $totalAmount = $subtotal - $discount_amount + $tax_amount;

            $quotation = Quotation::create([
                'company_id' => auth()->user()->company_id,
                'customer_id' => $request->customer_id,
                'quotation_no' => $request->quotation_no,
                'quotation_date' => $request->quotation_date,
                'valid_till' => $request->valid_till,
                'status' => 'Draft',
                'subtotal' => $subtotal,
                'discount_percent' => $discount_percent,
                'discount_amount' => $discount_amount,
                'tax_percent' => $tax_percent,
                'tax_amount' => $tax_amount,
                'vat_amount' => $totalVat,
                'total_amount' => $totalAmount,
                'terms_conditions' => $request->terms_conditions,
                'approval_status' => 'Draft',
                'created_by' => auth()->id(),
            ]);
            
            // Create quotation items
            foreach ($request->items as $itemData) {
                $item = Item::find($itemData['item_id']);
                $lineTotal = $itemData['qty'] * $itemData['rate'];
                
                QuotationItem::create([
                    'quotation_id' => $quotation->id,
                    'item_id' => $itemData['item_id'],
                    'lot_id' => $itemData['lot_id'] ?? null,
                    'supplier_id' => $item->supplier_id ?? null,
                    'description' => $itemData['description'] ?? '',
                    'qty' => $itemData['qty'],
                    'rate' => $itemData['rate'],
                    'amount' => $lineTotal,
                ]);
            }
            
            // Log the creation
            ApprovalLog::logAction('quotation', $quotation->id, 'Created', auth()->id());
            
            DB::commit();
            
            return redirect()->route('quotations.index')
                ->with('success', 'Quotation created successfully.');
            
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to create quotation: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Quotation $quotation)
    {
        $quotation->load(['customer', 'items.item.supplier', 'creator', 'approver']);
        $approvalLogs = ApprovalLog::where('module_name', 'quotation')
            ->where('record_id', $quotation->id)
            ->with('actionBy')
            ->orderBy('action_date', 'desc')
            ->get();
            
        return view('quotations.show', compact('quotation', 'approvalLogs'));
    }

    public function edit(Quotation $quotation)
    {
        if ($quotation->approval_status !== 'Draft') {
            return back()->with('error', 'Cannot edit quotation that is not in draft status.');
        }
        
        $quotation->load(['customer', 'items.item']);
        $customers = Customer::where('is_active', true)->get();
        $items = Item::where('is_active', true)->get();
        
        return view('quotations.edit', compact('quotation', 'customers', 'items'));
    }

    public function submit(Quotation $quotation)
    {
        if ($quotation->approval_status !== 'Draft') {
            return back()->with('error', 'Quotation is not in draft status.');
        }
        
        $quotation->update([
            'approval_status' => 'Submitted',
            'updated_by' => auth()->id(),
        ]);
        
        ApprovalLog::logAction('quotation', $quotation->id, 'Submitted', auth()->id(), 'Submitted for approval');
        
        return back()->with('success', 'Quotation submitted for approval.');
    }

    public function approve(Quotation $quotation)
    {
        if ($quotation->approval_status !== 'Submitted') {
            return back()->with('error', 'Only submitted quotations can be approved.');
        }
        
        $quotation->update([
            'approval_status' => 'Approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'updated_by' => auth()->id(),
        ]);
        
        $remarks = request('remarks');
        ApprovalLog::logAction('quotation', $quotation->id, 'Approved', auth()->id(), $remarks);
        
        return back()->with('success', 'Quotation approved successfully.');
    }

    public function reject(Quotation $quotation)
    {
        if ($quotation->approval_status !== 'Submitted') {
            return back()->with('error', 'Only submitted quotations can be rejected.');
        }
        
        $reason = request('reason');
        
        if (empty($reason)) {
            return back()->with('error', 'Rejection reason is required.');
        }
        
        $quotation->update([
            'approval_status' => 'Rejected',
            'rejection_reason' => $reason,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'updated_by' => auth()->id(),
        ]);
        
        ApprovalLog::logAction('quotation', $quotation->id, 'Rejected', auth()->id(), $reason);
        
        return back()->with('success', 'Quotation rejected successfully.');
    }

    public function print(Quotation $quotation)
    {
        $quotation->load(['customer', 'items.item', 'creator']);
        
        return view('quotations.print', compact('quotation'));
    }
}
