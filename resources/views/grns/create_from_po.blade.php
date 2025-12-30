@extends('layouts.app')

@section('title', 'Create GRN from Purchase Order')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<style>
.table > tbody > tr > td, 
.table > tbody > tr > th, 
.table > tfoot > tr > td, 
.table > tfoot > tr > th, 
.table > thead > tr > td, 
.table > thead > tr > th {
    padding: 8px;
    vertical-align: middle;
}
.po-info-card {
    background-color: #f8f9fa;
    border-left: 4px solid #0d6efd;
}
</style>
@endpush

@section('content')
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-file-import me-2"></i>Create GRN from Purchase Order
    </h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('purchase-orders.index') }}">Purchase Orders</a></li>
            <li class="breadcrumb-item"><a href="{{ route('purchase-orders.show', $purchaseOrder) }}">{{ $purchaseOrder->po_no }}</a></li>
            <li class="breadcrumb-item active">Create GRN</li>
        </ol>
    </nav>
</div>

<!-- Success/Error Messages -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Purchase Order Info (Readonly) -->
<div class="card po-info-card mb-3">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">
            <i class="fas fa-info-circle me-2"></i>Purchase Order Information
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <strong>PO No:</strong> {{ $purchaseOrder->po_no }}
            </div>
            <div class="col-md-3">
                <strong>PO Date:</strong> {{ $purchaseOrder->po_date->format('d M Y') }}
            </div>
            <div class="col-md-3">
                <strong>Supplier:</strong> {{ $purchaseOrder->supplier->supplier_name }}
            </div>
            <div class="col-md-3">
                <strong>Total Amount:</strong> {{ $purchaseOrder->currency }} {{ number_format($purchaseOrder->total_amount, 3) }}
            </div>
        </div>
    </div>
</div>

<form action="{{ route('grns.store_from_po') }}" method="POST" id="grnForm">
    @csrf
    <input type="hidden" name="purchase_order_id" value="{{ $purchaseOrder->id }}">
    <input type="hidden" name="supplier_id" value="{{ $purchaseOrder->supplier_id }}">
    <input type="hidden" id="hidden_rowcount" value="{{ count($purchaseOrder->items) }}">
    
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-file-alt me-2"></i>GRN Information
            </h5>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">GRN No <span class="text-danger">*</span></label>
                <div class="col-sm-4">
                    <input type="text" 
                           class="form-control @error('grn_no') is-invalid @enderror" 
                           id="grn_no" 
                           name="grn_no" 
                           value="{{ old('grn_no', $grn_no) }}" 
                           readonly
                           required>
                    @error('grn_no')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <label class="col-sm-2 col-form-label">GRN Date <span class="text-danger">*</span></label>
                <div class="col-sm-4">
                    <input type="date" 
                           class="form-control @error('grn_date') is-invalid @enderror" 
                           id="grn_date" 
                           name="grn_date" 
                           value="{{ old('grn_date', date('Y-m-d')) }}" 
                           required>
                    @error('grn_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Currency <span class="text-danger">*</span></label>
                <div class="col-sm-4">
                    <input type="text" 
                           class="form-control" 
                           value="{{ $purchaseOrder->currency }}" 
                           readonly>
                    <input type="hidden" name="currency" value="{{ $purchaseOrder->currency }}">
                </div>
                
                <label class="col-sm-2 col-form-label">Exchange Rate <span class="text-danger">*</span></label>
                <div class="col-sm-4">
                    <input type="number" 
                           class="form-control @error('exchange_rate') is-invalid @enderror" 
                           id="exchange_rate" 
                           name="exchange_rate" 
                           value="{{ old('exchange_rate', $purchaseOrder->currency_value ?? ($purchaseOrder->currency == 'OMR' ? '1.000' : '0.385')) }}" 
                           min="0.001"
                           step="0.001"
                           required>
                    <input type="hidden" id="po_currency_value" value="{{ $purchaseOrder->currency_value ?? 1 }}">
                    <small class="text-muted">Exchange rate to OMR</small>
                    @error('exchange_rate')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Invoice No <span class="text-danger">*</span></label>
                <div class="col-sm-4">
                    <input type="text" 
                           class="form-control @error('invoice_no') is-invalid @enderror" 
                           id="invoice_no" 
                           name="invoice_no" 
                           value="{{ old('invoice_no') }}" 
                           placeholder="Enter supplier invoice number"
                           required>
                    @error('invoice_no')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>
    
    <!-- Items Section -->
    <div class="card mt-3">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-boxes me-2"></i>Items Received
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="itemsTable">
                    <thead class="table-primary">
                        <tr>
                            <th style="width:18%">Item Name</th>
                            <th style="width:8%">Unit</th>
                            <th style="width:10%">Lot No</th>
                            <th style="width:10%">Expiry Date</th>
                            <th style="width:8%">Qty Received</th>
                            <th style="width:10%">Base Cost</th>
                            <th style="width:10%">Duty Amount</th>
                            <th style="width:10%">Freight Amount</th>
                            <th style="width:12%">Landed Cost/Unit</th>
                            <th style="width:10%">Total Landed Cost</th>
                        </tr>
                    </thead>
                    <tbody id="itemsTableBody">
                        @foreach($purchaseOrder->items as $index => $poItem)
                        <tr id="row_{{ $index + 1 }}">
                            <td>
                                <strong>{{ $poItem->item->item_name }}</strong><br>
                                <small class="text-muted">{{ $poItem->item->item_code }}</small>
                                <input type="hidden" name="items[{{ $index + 1 }}][item_id]" value="{{ $poItem->item_id }}">
                                <input type="hidden" name="items[{{ $index + 1 }}][po_item_id]" value="{{ $poItem->id }}">
                            </td>
                            <td class="text-center align-middle">
                                <span id="td_unit_{{ $index + 1 }}">{{ $poItem->item->unit ?? '' }}</span>
                                <input type="hidden" name="items[{{ $index + 1 }}][unit]" value="{{ $poItem->item->unit ?? '' }}">
                            </td>
                            <td>
                                <input type="text" 
                                       class="form-control form-control-sm" 
                                       id="lot_no_{{ $index + 1 }}" 
                                       name="items[{{ $index + 1 }}][lot_no]" 
                                       placeholder="Lot No"
                                       required>
                            </td>
                            <td>
                                <input type="date" 
                                       class="form-control form-control-sm" 
                                       id="expiry_date_{{ $index + 1 }}" 
                                       name="items[{{ $index + 1 }}][expiry_date]">
                            </td>
                            <td>
                                <input type="number" 
                                       class="form-control form-control-sm" 
                                       id="qty_{{ $index + 1 }}" 
                                       name="items[{{ $index + 1 }}][qty_received]" 
                                       value="{{ $poItem->qty }}" 
                                       min="0.01" 
                                       step="0.01"
                                       required
                                       onchange="calculateLandedCost({{ $index + 1 }})">
                            </td>
                            <td>
                                <input type="number" 
                                       class="form-control form-control-sm text-end" 
                                       id="base_cost_{{ $index + 1 }}" 
                                       name="items[{{ $index + 1 }}][base_cost]" 
                                       value="{{ $poItem->rate }}" 
                                       min="0" 
                                       step="0.001"
                                       required
                                       onchange="calculateLandedCost({{ $index + 1 }})">
                            </td>
                            <td>
                                <input type="number" 
                                       class="form-control form-control-sm text-end" 
                                       id="duty_{{ $index + 1 }}" 
                                       name="items[{{ $index + 1 }}][duty_amount]" 
                                       value="0.000" 
                                       min="0" 
                                       step="0.001"
                                       onchange="calculateLandedCost({{ $index + 1 }})">
                            </td>
                            <td>
                                <input type="number" 
                                       class="form-control form-control-sm text-end" 
                                       id="freight_{{ $index + 1 }}" 
                                       name="items[{{ $index + 1 }}][freight_amount]" 
                                       value="0.000" 
                                       min="0" 
                                       step="0.001"
                                       onchange="calculateLandedCost({{ $index + 1 }})">
                            </td>
                            <td class="text-end">
                                <strong id="landed_cost_{{ $index + 1 }}">0.000</strong>
                                <input type="hidden" id="landed_cost_hidden_{{ $index + 1 }}" name="items[{{ $index + 1 }}][landed_cost_per_unit]" value="0">
                            </td>
                            <td class="text-end">
                                <strong id="total_landed_{{ $index + 1 }}">0.000</strong>
                                <input type="hidden" id="total_landed_hidden_{{ $index + 1 }}" name="items[{{ $index + 1 }}][total_landed_cost]" value="0">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr class="table-primary">
                            <td colspan="9" class="text-end"><h5 class="mb-0">Total Landed Cost:</h5></td>
                            <td class="text-end"><h5 class="mb-0 text-success">OMR <span id="grand_total">0.000</span></h5></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Remarks Section -->
    <div class="card mt-3">
        <div class="card-body">
            <div class="row">
                <label class="col-sm-2 col-form-label">Remarks</label>
                <div class="col-sm-10">
                    <textarea class="form-control" 
                              name="remarks" 
                              rows="3"
                              placeholder="Enter any remarks or notes">{{ old('remarks') }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Section -->
    <div class="card mt-3">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-money-check-alt me-2"></i>Payment Information</h5>
        </div>
        <div class="card-body">
            <div class="row g-2 align-items-end">
                <div class="col-md-2">
                    <label for="payment_date" class="form-label">Date</label>
                    <input type="date" name="payment_date" id="payment_date" class="form-control" value="{{ old('payment_date', date('Y-m-d')) }}">
                </div>
                <div class="col-md-2">
                    <label for="amount" class="form-label">Amount</label>
                    <input type="number" step="0.001" min="0.01" name="amount" id="amount" class="form-control" value="{{ old('amount') }}">
                </div>
                <div class="col-md-2">
                    <label for="payment_type" class="form-label">Type</label>
                    <select name="payment_type" id="payment_type" class="form-select">
                        <option value="">-Select-</option>
                        <option value="Cash">Cash</option>
                        <option value="Bank">Bank</option>
                        <option value="Card">Card</option>
                        <option value="Cheque">Cheque</option>
                    </select>
                </div>
                <input type="hidden" name="paid_status" id="paid_status" value="Not Paid">
                <div class="col-md-2">
                    <label for="credit_due" class="form-label">Credit Due</label>
                    <input type="number" step="0.001" min="0" name="credit_due" id="credit_due" class="form-control" value="{{ old('credit_due', 0) }}">
                </div>
                <div class="col-md-2">
                    <div class="cheque-fields">
                        <label for="cheque_no" class="form-label">Cheque No</label>
                        <input type="text" name="cheque_no" id="cheque_no" class="form-control" value="{{ old('cheque_no') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="cheque-fields">
                        <label for="cheque_date" class="form-label">Cheque Date</label>
                        <input type="date" name="cheque_date" id="cheque_date" class="form-control" value="{{ old('cheque_date') }}">
                    </div>
                </div>
                <div class="col-md-12 mt-2">
                    <label for="payment_note" class="form-label">Note</label>
                    <input type="text" name="payment_note" id="payment_note" class="form-control" value="{{ old('payment_note') }}">
                </div>
            </div>
        </div>
    </div>

    <!-- Form Buttons -->
    <div class="card mt-3">
        <div class="card-footer">
            <div class="row">
                <div class="col-md-3 offset-md-3">
                    <button type="submit" class="btn btn-success btn-block w-100" id="saveBtn">
                        <i class="fas fa-save me-2"></i>Save GRN
                    </button>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('purchase-orders.show', $purchaseOrder) }}" class="btn btn-secondary btn-block w-100">
                        <i class="fas fa-times me-2"></i>Cancel
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    const rowCount = parseInt($('#hidden_rowcount').val());
    
    // Calculate landed cost for all rows initially
    for (let i = 1; i <= rowCount; i++) {
        calculateLandedCost(i);
    }

    // Toggle cheque fields based on payment type
    function toggleChequeFields() {
        const type = $('#payment_type').val();
        if (type === 'Cheque') {
            $('.cheque-fields').show();
        } else {
            $('.cheque-fields').hide();
            $('#cheque_no').val('');
            $('#cheque_date').val('');
        }
    }

    toggleChequeFields();
    $('#payment_type').on('change', toggleChequeFields);
    // Recalculate when exchange rate changes
    $('#exchange_rate').on('input change', function() {
        for (let i = 1; i <= rowCount; i++) {
            calculateLandedCost(i);
        }
    });
});

window.calculateLandedCost = function(rowId) {
    const qty = parseFloat($(`#qty_${rowId}`).val()) || 0;
    const baseCost = parseFloat($(`#base_cost_${rowId}`).val()) || 0;
    const duty = parseFloat($(`#duty_${rowId}`).val()) || 0;
    const freight = parseFloat($(`#freight_${rowId}`).val()) || 0;
    // Determine exchange rate to OMR (exchange_rate input is 'to OMR')
    // Prefer the stored purchase order currency value for conversion
    const poCurrency = '{{ $purchaseOrder->currency }}';
    const poCv = parseFloat($('#po_currency_value').val());
    const exchangeRate = (poCv && !isNaN(poCv)) ? poCv : (parseFloat($('#exchange_rate').val()) || 1);

    // Convert base cost to OMR if PO currency is not OMR
    let baseCostOMR = baseCost;
    if (poCurrency && poCurrency !== 'OMR') {
        baseCostOMR = baseCost * exchangeRate;
    }
    
    // Calculate landed cost per unit: base_cost + (duty + freight) / qty
    let landedCostPerUnit = baseCostOMR;
    if (qty > 0) {
        landedCostPerUnit = baseCostOMR + ((duty + freight) / qty);
    }
    
    const totalLandedCost = landedCostPerUnit * qty;
    
    // Display landed costs in OMR (for reference)
    $(`#landed_cost_${rowId}`).text(landedCostPerUnit.toFixed(3));
    $(`#landed_cost_hidden_${rowId}`).val(landedCostPerUnit.toFixed(3));
    $(`#total_landed_${rowId}`).text(totalLandedCost.toFixed(3));
    $(`#total_landed_hidden_${rowId}`).val(totalLandedCost.toFixed(3));
    
    calculateGrandTotal();
};

function calculateGrandTotal() {
    const rowCount = parseInt($('#hidden_rowcount').val());
    let grandTotal = 0;
    
    for (let i = 1; i <= rowCount; i++) {
        if (document.getElementById(`total_landed_hidden_${i}`)) {
            const totalLanded = parseFloat($(`#total_landed_hidden_${i}`).val()) || 0;
            grandTotal += totalLanded;
        }
    }
    
    $('#grand_total').text(grandTotal.toFixed(3));
}

// Form validation
$('#grnForm').submit(function(e) {
    let hasQty = false;
    const rowCount = parseInt($('#hidden_rowcount').val());
    
    for (let i = 1; i <= rowCount; i++) {
        const qty = parseFloat($(`#qty_${i}`).val()) || 0;
        if (qty > 0) {
            hasQty = true;
            break;
        }
    }
    
    if (!hasQty) {
        e.preventDefault();
        alert('Please enter quantity received for at least one item.');
        return false;
    }
});
</script>
@endpush
