<?php

namespace App\Http\Controllers;

use App\Models\JobCard;
use App\Models\Customer;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class JobCardController extends Controller
{
    public function index()
    {
        $jobCards = JobCard::with(['customer'])->orderBy('created_at', 'desc')->get();
        
        $stats = [
            'total_jobs' => JobCard::count(),
            'pending_jobs' => JobCard::where('status', 'Pending')->count(),
            'in_progress' => JobCard::where('status', 'In Progress')->count(),
            'completed' => JobCard::where('status', 'Completed')->count(),
        ];
        
        return view('job-cards.index', compact('jobCards', 'stats'));
    }

    public function create()
    {
        $customers = Customer::where('is_active', true)->orderBy('customer_name')->get();
        $items = Item::where('is_active', true)->where('stock_type', '!=', 'Service')->orderBy('item_name')->get(['id','item_name','item_code','sale_price','stock_type','current_stock']);
        // Pass empty prefill so create view uses blank fields
        $prefill = [];
        $prefillItems = [];
        return view('job-cards.create', compact('customers','items','prefill','prefillItems'));
    }

    /**
     * Duplicate an existing job card: open create form prefilled with job card data
     */
    public function duplicate($id)
    {
        $jobCard = JobCard::with(['parts.item'])->findOrFail($id);

        // We want duplicate to only prefill parts/items; keep other fields blank
        $prefill = [];

        // prepare parts/items to prefill
        $prefillItems = [];
        foreach ($jobCard->parts as $part) {
            $prefillItems[] = [
                'item_id' => $part->item_id,
                'lot_id' => $part->lot_id,
                'quantity' => $part->qty_used ?? $part->quantity_used ?? 0,
            ];
        }

        $customers = Customer::where('is_active', true)->orderBy('customer_name')->get();
        $items = Item::where('is_active', true)->where('stock_type', '!=', 'Service')->orderBy('item_name')->get(['id','item_name','item_code','sale_price','stock_type','current_stock']);

        return view('job-cards.create', compact('customers','items','prefill','prefillItems'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'job_description' => 'required|string',
            'priority' => 'required|in:Low,Medium,High,Urgent',
            'status' => 'required|in:Pending,In Progress,Completed,Cancelled',
            'scheduled_date' => 'nullable|date',
            'estimated_hours' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'invoice_no' => 'nullable|string|max:100',
            'model_no' => 'nullable|string|max:100',
            'serial_no' => 'nullable|string|max:100',
            'service_attend' => 'nullable|string|max:255',
            'service_attend_mobile' => 'nullable|string|max:20',
            'loading_hr' => 'nullable|string|max:50',
            'service_start_time' => 'nullable',
            'service_end_time' => 'nullable',
            'reference_no' => 'nullable|string|max:100',
            'job_report_date' => 'nullable|date',
            'job_report_no' => 'nullable|string|max:100',
            'service_remarks' => 'nullable|string',
            'customer_remarks' => 'nullable|string'
        ]);

        // Generate job card number
        $lastJobCard = JobCard::orderBy('id', 'desc')->first();
        $number = $lastJobCard ? intval(substr($lastJobCard->job_card_no, 3)) + 1 : 1;
        $validated['job_card_no'] = 'JOB' . str_pad($number, 6, '0', STR_PAD_LEFT);
        
        // Set defaults
        $validated['company_id'] = 1;
        $validated['status'] = $validated['status'] ?? 'Pending';
        $validated['created_by'] = auth()->id();
        // Only set job_date if the column exists in DB (some installs may not have it)
        if (Schema::hasColumn('job_cards', 'job_date')) {
            $validated['job_date'] = $validated['scheduled_date'] ?? now()->toDateString();
        }

        $jobCard = JobCard::create($validated);

        // Handle parts/items if provided
        if ($request->has('items') && is_array($request->input('items'))) {
            foreach ($request->input('items') as $part) {
                $itemId = $part['item_id'] ?? null;
                $qty = $part['quantity'] ?? 0;
                $lotId = $part['lot_id'] ?? null;
                if ($itemId && floatval($qty) > 0) {
                    $item = Item::find($itemId);
                    $unitCost = $item ? ($item->sale_price ?? 0) : 0;
                    $totalCost = $unitCost * floatval($qty);
                    $jobCard->parts()->create([
                        'item_id' => $itemId,
                        'qty_used' => $qty,
                        'cost_price' => $unitCost,
                        'sale_price' => $unitCost,
                        'amount' => $totalCost,
                        'lot_id' => $lotId,
                    ]);
                }
            }
        }

        // If user asked to create quote immediately, redirect to createQuote route
        if ($request->has('create_quote') && $request->input('create_quote')) {
            return redirect()->route('job-cards.create_quote', $jobCard->id);
        }

        return redirect()->route('job-cards.index')
            ->with('success', 'Job card created successfully');
    }

    public function show($id)
    {
        $jobCard = JobCard::with(['customer', 'parts.item'])->findOrFail($id);
        return view('job-cards.show', compact('jobCard'));
    }

    public function edit($id)
    {
        $jobCard = JobCard::findOrFail($id);
        $customers = Customer::where('is_active', true)->orderBy('customer_name')->get();
        $items = Item::where('is_active', true)->where('stock_type', '!=', 'Service')->orderBy('item_name')->get(['id','item_name','item_code','sale_price','stock_type','current_stock']);
        // prepare parts for prefill
        $prefillItems = [];
        foreach ($jobCard->parts as $part) {
            $prefillItems[] = [
                'item_id' => $part->item_id,
                'lot_id' => $part->lot_id,
                'quantity' => $part->qty_used ?? $part->quantity_used ?? 0,
            ];
        }
        return view('job-cards.edit', compact('jobCard', 'customers', 'items', 'prefillItems'));
    }

    public function update(Request $request, $id)
    {
        $jobCard = JobCard::findOrFail($id);

        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'job_description' => 'required|string',
            'priority' => 'required|in:Low,Medium,High,Urgent',
            'status' => 'required|in:Pending,In Progress,Completed,Cancelled',
            'scheduled_date' => 'nullable|date',
            'estimated_hours' => 'nullable|numeric|min:0',
            'actual_hours' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'invoice_no' => 'nullable|string|max:100',
            'model_no' => 'nullable|string|max:100',
            'serial_no' => 'nullable|string|max:100',
            'service_attend' => 'nullable|string|max:100',
            'service_attend_mobile' => 'nullable|string|max:20',
            'loading_hr' => 'nullable|string|max:50',
            'service_start_time' => 'nullable|date_format:H:i',
            'service_end_time' => 'nullable|date_format:H:i',
            'reference_no' => 'nullable|string|max:100',
            'job_report_date' => 'nullable|date',
            'job_report_no' => 'nullable|string|max:100',
            'service_remarks' => 'nullable|string|max:1000',
            'customer_remarks' => 'nullable|string|max:1000',
        ]);

        $jobCard->update($validated);

        return redirect()->route('job-cards.index')
            ->with('success', 'Job card updated successfully');
    }

    public function destroy($id)
    {
        $jobCard = JobCard::findOrFail($id);
        
        if ($jobCard->status === 'Completed') {
            return back()->with('error', 'Cannot delete completed job card');
        }

        $jobCard->delete();

        return redirect()->route('job-cards.index')
            ->with('success', 'Job card deleted successfully');
    }

    public function print($id)
    {
        $jobCard = JobCard::with(['customer', 'parts.item'])->findOrFail($id);
        return view('job-cards.print', compact('jobCard'));
    }

    /**
     * Create a quotation prefilled from job card parts
     */
    public function createQuote($id)
    {
        $jobCard = JobCard::with(['customer', 'parts.item'])->findOrFail($id);

        // Prepare prefill items from job card parts
        $prefillItems = [];
        foreach ($jobCard->parts as $part) {
            $prefillItems[] = [
                'item' => [
                    'id' => $part->item->id ?? $part->item_id,
                    'item_name' => $part->item->item_name ?? '-',
                    'item_code' => $part->item->item_code ?? '',
                    'sale_price' => $part->item->sale_price ?? 0,
                ],
                'lot_id' => $part->lot_id ?? null,
                'quantity' => $part->quantity_used ?? 0,
            ];
        }

        // Build data needed by quotations.create
        $customers = Customer::where('is_active', true)->orderBy('customer_name')->get();
        $items = Item::where('is_active', true)->with('supplier')->withSum('stockLots as total_stock', 'qty_available')->orderBy('item_name')->get();

        // Generate quotation number (same logic as QuotationController)
        $year = date('Y');
        $lastQuotation = \App\Models\Quotation::where('company_id', auth()->user()->company_id)->whereYear('created_at', $year)->orderBy('id', 'desc')->first();
        $nextNumber = $lastQuotation ? (intval(substr($lastQuotation->quotation_no, -4)) + 1) : 1;
        $quotationNo = 'QTN-' . $year . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        $prefillCustomerId = $jobCard->customer_id ?? null;
        return view('quotations.create', compact('customers', 'items', 'quotationNo', 'prefillItems', 'prefillCustomerId'));
    }
}