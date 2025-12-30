@extends('layouts.app')

@section('title', 'Create Invoice from Quotation')

@push('styles')
<style>
table.table-bordered > thead > tr > th {
    text-align: center;
}
.table > tbody > tr > td, 
.table > tbody > tr > th, 
.table > tfoot > tr > td, 
.table > tfoot > tr > th, 
.table > thead > tr > td, 
.table > thead > tr > th {
    padding-left: 2px;
    padding-right: 2px;  
}
</style>
@endpush

@section('content')
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-file-invoice me-2"></i>Create {{ ucfirst($statusq) }} Invoice
        <small class="text-muted d-block mt-1">Convert Quotation to Invoice</small>
    </h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('quotations.index') }}">Quotations</a></li>
            <li class="breadcrumb-item active">Create {{ ucfirst($statusq) }} Invoice</li>
        </ol>
    </nav>
</div>

<!-- Alert Messages -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card">
    <div class="card-header bg-info text-white">
        <h3 class="card-title mb-0">
            <i class="fas fa-file-invoice me-2"></i>Invoice Details
        </h3>
    </div>
    
    <form method="POST" action="{{ route('sales.store_from_quotation') }}" id="sales-form">
        @csrf
        <input type="hidden" name="quotation_id" value="{{ $quotation->id }}">
        <input type="hidden" id="sales_status" name="sales_status" value="{{ $statusq }}">
        <input type="hidden" id="hidden_rowcount" value="1">
        <input type="hidden" id="hidden_total_amt" name="total_amt" value="0">
        <input type="hidden" id="hidden_discount_to_all_amt" name="discount_to_all_amt" value="0">
        <input type="hidden" name="subtotal" id="subtotal_hidden" value="0">
        <input type="hidden" name="round_off" id="round_off_hidden" value="0">
        
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="customer_id" class="form-label">Customer Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" value="{{ $quotation->customer->customer_name }}" readonly>
                    <input type="hidden" name="customer_id" value="{{ $quotation->customer_id }}">
                </div>
                
                <div class="col-md-6">
                    <label for="sales_date" class="form-label">Invoice Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="sales_date" name="sales_date" value="{{ date('Y-m-d') }}" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="status_display" class="form-label">Status <span class="text-danger">*</span></label>
                    <input type="text" readonly class="form-control" value="{{ $statusq === 'performance' ? 'Performance Invoice' : 'Invoice' }}">
                </div>
                
                <div class="col-md-6">
                    <label for="reference_no" class="form-label">Reference No</label>
                    <input type="text" class="form-control" id="reference_no" name="reference_no" value="{{ $quotation->reference_no }}">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="quotation_code" class="form-label">Quotation Code</label>
                    <input type="text" class="form-control" value="{{ $quotation->quotation_no }}" readonly>
                </div>
                
                <div style="display:none">
                    <input type="hidden" name="invoice_type" id="invoice_type" value="Full">
                </div>
                <div style="display:none">
                    <input type="hidden" id="sales_status_display" value="{{ $statusq }}">
                </div>
            </div>

            <!-- Items Table -->
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="sales_table">
                            <thead class="table-primary">
                                <tr>
                                    <th style="width:25%">Item Name</th>
                                    <th style="width:8%">Unit</th>
                                    <th style="width:15%">Quantity</th>
                                    <th style="width:25%">Unit Price</th>
                                    <th style="width:15%">Total Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($quotation->items as $index => $item)
                                <tr id="row_{{ $index + 1 }}">
                                    <td>
                                        {{ $item->item->item_name ?? 'N/A' }}
                                        <input type="hidden" name="items[{{ $index + 1 }}][item_id]" id="td_data_{{ $index + 1 }}_1" value="{{ $item->item_id }}">
                                        <input type="hidden" name="items[{{ $index + 1 }}][lot_id]" value="{{ $item->lot_id ?? '' }}">
                                    </td>
                                    <td class="text-center align-middle">
                                        <span id="td_unit_{{ $index + 1 }}">{{ $item->item->unit ?? '' }}</span>
                                        <input type="hidden" name="items[{{ $index + 1 }}][unit]" value="{{ $item->item->unit ?? '' }}">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" class="form-control text-end" 
                                               name="items[{{ $index + 1 }}][qty]" id="td_data_{{ $index + 1 }}_3" 
                                               value="{{ $item->qty }}" 
                                               onchange="calculate_tax({{ $index + 1 }})" readonly>
                                    </td>
                                    <td>
                                        <input type="number" step="0.001" class="form-control text-end" 
                                               name="items[{{ $index + 1 }}][rate]" id="td_data_{{ $index + 1 }}_10" 
                                               value="{{ number_format($item->rate, 3, '.', '') }}" 
                                               onchange="calculate_tax({{ $index + 1 }})" readonly>
                                        <input type="hidden" id="td_data_{{ $index + 1 }}_4" value="{{ $item->rate }}">
                                        <input type="hidden" id="td_data_{{ $index + 1 }}_13" value="{{ $item->rate }}">
                                    </td>
                                    <td>
                                        <input type="number" step="0.001" class="form-control text-end" 
                                               id="td_data_{{ $index + 1 }}_9" 
                                               value="{{ number_format($item->amount, 3, '.', '') }}" readonly>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Summary Section -->
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label"><strong>Total Quantity:</strong></label>
                        <span class="total_quantity text-success fs-5 ms-2">{{ $quotation->items->sum('qty') }}</span>
                    </div>
                    
                    <div class="mb-3">
                        <label for="other_charges_input" class="form-label">Other Charges</label>
                        <div class="input-group">
                            <input type="number" step="0.001" class="form-control text-end" 
                                   id="other_charges_input" name="other_charges_input" 
                                   value="0" onchange="final_total()">
                            <select class="form-select" id="other_charges_tax_id" name="other_charges_tax_id" 
                                    onchange="final_total()" style="max-width: 150px;">
                                <option data-tax="0">None</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="global_discount_percent" class="form-label">Discount (%)</label>
                        <div class="input-group">
                            <input type="number" step="0.01" class="form-control text-end" 
                                   id="global_discount_percent" name="global_discount_percent" 
                                   value="{{ $quotation->discount_percent ?? 0 }}" onchange="final_total()" style="max-width:150px;">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="global_tax_percent" class="form-label">Tax (%)</label>
                        <div class="input-group">
                            <input type="number" step="0.01" class="form-control text-end" 
                                   id="global_tax_percent" name="global_tax_percent" 
                                   value="{{ $quotation->tax_percent ?? 0 }}" onchange="final_total()" style="max-width:150px;">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="sales_note" class="form-label">Note</label>
                        <textarea class="form-control" id="sales_note" name="sales_note" rows="3">{{ $quotation->notes ?? '' }}</textarea>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <table class="table">
                        <tr>
                            <th class="text-end" style="font-size: 17px;">Subtotal:</th>
                            <th class="text-end" style="font-size: 17px;">
                                <h4><strong id="subtotal_amt">0.000</strong></h4>
                            </th>
                        </tr>
                        <tr>
                            <th class="text-end" style="font-size: 17px;">Other Charges:</th>
                            <th class="text-end" style="font-size: 17px;">
                                <h4><strong id="other_charges_amt">0.000</strong></h4>
                            </th>
                        </tr>
                        <tr>
                            <th class="text-end" style="font-size: 17px;">Discount Amount:</th>
                            <th class="text-end" style="font-size: 17px;">
                                <h4><strong id="discount_amount">{{ number_format($quotation->discount_amount ?? 0, 3) }}</strong></h4>
                            </th>
                        </tr>
                        <tr>
                            <th class="text-end" style="font-size: 17px;">Tax Amount:</th>
                            <th class="text-end" style="font-size: 17px;">
                                <h4><strong id="tax_amount">{{ number_format($quotation->tax_amount ?? 0, 3) }}</strong></h4>
                            </th>
                        </tr>
                        <tr>
                            <th class="text-end" style="font-size: 17px;">Round Off:</th>
                            <th class="text-end" style="font-size: 17px;">
                                <h4><strong id="round_off_amt">0.000</strong></h4>
                            </th>
                        </tr>
                        <tr>
                            <th class="text-end" style="font-size: 17px;">Grand Total:</th>
                            <th class="text-end" style="font-size: 17px;">
                                <h4><strong id="total_amt" class="text-success">0.000</strong></h4>
                            </th>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Payment Section -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card bg-light">
                        <div class="card-header">
                            <h5 class="mb-0">Payment Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="amount" class="form-label">Amount</label>
                                    <input type="number" step="0.001" class="form-control text-end" 
                                           id="amount" name="amount" value="0">
                                </div>
                                <div class="col-md-4">
                                    <label for="credit_due" class="form-label">Credit Due</label>
                                    <input type="number" step="0.001" class="form-control text-end" 
                                           id="credit_due" name="credit_due" value="0">
                                </div>
                                <div class="col-md-4">
                                    <label for="payment_type" class="form-label">Payment Type</label>
                                    <select class="form-select" id="payment_type" name="payment_type">
                                        <option value="">-Select-</option>
                                        <option value="Cash">Cash</option>
                                        <option value="Card">Card</option>
                                        <option value="Cheque">Cheque</option>
                                        <option value="Bank Transfer">Bank Transfer</option>
                                        <option value="Credit">Credit</option>
                                    </select>
                                </div>
                                <input type="hidden" id="paid_type" name="paid_type" value="Not Paid">
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <label for="payment_note" class="form-label">Payment Note</label>
                                    <textarea class="form-control" id="payment_note" name="payment_note" rows="2"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer">
            <div class="row">
                <div class="col-md-12 text-center">
                    <button type="submit" id="create_invoice" class="btn btn-success btn-lg me-2">
                        <i class="fas fa-save me-2"></i>Create {{ ucfirst($statusq) }} Invoice
                    </button>
                    <a href="{{ route('quotations.index') }}" class="btn btn-secondary btn-lg">
                        <i class="fas fa-times me-2"></i>Close
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize row count
    var itemCount = {{ $quotation->items->count() }};
    $("#hidden_rowcount").val(itemCount);
    
    // Calculate initial totals
    setTimeout(function() {
        final_total();
    }, 500);
});

function calculate_tax(row_id) {
    var qty = parseFloat($("#td_data_" + row_id + "_3").val()) || 0;
    var price = parseFloat($("#td_data_" + row_id + "_10").val()) || 0;
    var subtotal = qty * price;
    $("#td_data_" + row_id + "_9").val(subtotal.toFixed(3));
    
    final_total();
}

function final_total() {
    var rowcount = parseInt($("#hidden_rowcount").val()) || 0;
    var subtotal = 0;
    var total_quantity = 0;
    
    // Calculate subtotal from all rows
    for (var i = 1; i <= rowcount; i++) {
        if ($("#td_data_" + i + "_3").length) {
            var qty = parseFloat($("#td_data_" + i + "_3").val()) || 0;
            var row_total = parseFloat($("#td_data_" + i + "_9").val()) || 0;
            
            if (qty > 0) {
                subtotal += row_total;
                total_quantity += qty;
            }
        }
    }
    
    // Other charges
    var other_charges = parseFloat($("#other_charges_input").val()) || 0;
    var other_charges_tax = parseFloat($('#other_charges_tax_id option:selected').attr('data-tax')) || 0;
    var other_charges_total = other_charges + (other_charges * other_charges_tax / 100);
    
    // legacy per-all discount inputs removed; using global_discount_percent instead
    
    // Calculate discount and tax similar to quotations.create
    var discountPercent = parseFloat($('#global_discount_percent').val()) || 0;
    var discountAmount = (subtotal * discountPercent) / 100;
    var taxable = subtotal - discountAmount + other_charges_total;
    var taxPercent = parseFloat($('#global_tax_percent').val()) || 0;
    var taxAmount = (taxable * taxPercent) / 100;
    var grand_total = taxable + taxAmount;
    var round_off_amt = Math.round(grand_total) - grand_total;
    
    // Update display
    $(".total_quantity").html(total_quantity.toFixed(0));
    $("#subtotal_amt").html(subtotal.toFixed(3));
    $("#other_charges_amt").html(other_charges_total.toFixed(3));
    $("#discount_amount").html(discountAmount.toFixed(3));
    $("#tax_amount").html(taxAmount.toFixed(3));
    $("#round_off_amt").html(round_off_amt.toFixed(3));
    $("#total_amt").html(grand_total.toFixed(3));
    
    // Update hidden fields
    $("#subtotal_hidden").val(subtotal.toFixed(3));
    $("#round_off_hidden").val(round_off_amt.toFixed(3));
    $("#hidden_total_amt").val(grand_total.toFixed(3));
    $("#hidden_discount_to_all_amt").val(discountAmount.toFixed(3));
    
    // Auto-fill amount but keep it editable
    $("#amount").val(grand_total.toFixed(3));
    $("#amount").prop('readonly', false);
}

// No invoice_type validation required (default Full)
</script>
@endpush
