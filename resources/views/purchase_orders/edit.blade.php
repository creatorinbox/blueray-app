@extends('layouts.app')

@section('title', 'Edit Purchase Order')

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
</style>
@endpush

@section('content')
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-edit me-2"></i>Edit Purchase Order
    </h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('purchase-orders.index') }}">Purchase Orders</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol>
    </nav>
</div>

<form action="{{ route('purchase-orders.update', $purchaseOrder) }}" method="POST" id="poForm">
    @csrf
    @method('PUT')
    <input type="hidden" id="hidden_rowcount" value="{{ count($purchaseOrder->items) + 1 }}">
    
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-file-invoice me-2"></i>Purchase Order Information
            </h5>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Company <span class="text-danger">*</span></label>
                            <div class="col-sm-4">
                                <select class="form-select @error('company_id') is-invalid @enderror" name="company_id" required>
                                    <option value="">-Select-</option>
                                    @foreach($companies as $company)
                                        <option value="{{ $company->id }}" {{ old('company_id', $purchaseOrder->company_id) == $company->id ? 'selected' : '' }}>{{ $company->company_name }}</option>
                                    @endforeach
                                </select>
                                @error('company_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                <label class="col-sm-2 col-form-label">PO No <span class="text-danger">*</span></label>
                <div class="col-sm-4">
                    <input type="text" 
                           class="form-control @error('po_no') is-invalid @enderror" 
                           id="po_no" 
                           name="po_no" 
                           value="{{ old('po_no', $purchaseOrder->po_no) }}" 
                           readonly
                           required>
                    @error('po_no')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <label class="col-sm-2 col-form-label">PO Date <span class="text-danger">*</span></label>
                <div class="col-sm-4">
                    <input type="date" 
                           class="form-control @error('po_date') is-invalid @enderror" 
                           id="po_date" 
                           name="po_date" 
                           value="{{ old('po_date', $purchaseOrder->po_date->format('Y-m-d')) }}" 
                           required>
                    @error('po_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Supplier <span class="text-danger">*</span></label>
                <div class="col-sm-4">
                    <select class="form-select select2 @error('supplier_id') is-invalid @enderror" 
                            id="supplier_id" 
                            name="supplier_id" 
                            required>
                        <option value="">-Select-</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" 
                                    {{ old('supplier_id', $purchaseOrder->supplier_id) == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->supplier_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('supplier_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <label class="col-sm-2 col-form-label">Currency <span class="text-danger">*</span></label>
                <div class="col-sm-4">
                    <select class="form-select @error('currency') is-invalid @enderror" 
                            id="currency" 
                            name="currency" 
                            required>
                        <option value="OMR" {{ old('currency', $purchaseOrder->currency) == 'OMR' ? 'selected' : '' }}>OMR</option>
                        <option value="USD" {{ old('currency', $purchaseOrder->currency) == 'USD' ? 'selected' : '' }}>USD</option>
                        <option value="EUR" {{ old('currency', $purchaseOrder->currency) == 'EUR' ? 'selected' : '' }}>EUR</option>
                    </select>
                    @error('currency')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-sm-2">
                    <input type="number" step="0.001" min="0" name="currency_value" id="currency_value" class="form-control" value="{{ old('currency_value', $purchaseOrder->currency_value ?? 1.000) }}">
                </div>
            </div>
            
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Status</label>
                <div class="col-sm-4">
                    <select class="form-select @error('status') is-invalid @enderror" 
                            id="status" 
                            name="status">
                        <option value="Draft" {{ old('status', $purchaseOrder->status) == 'Draft' ? 'selected' : '' }}>Draft</option>
                        <option value="Pending" {{ old('status', $purchaseOrder->status) == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Approved" {{ old('status', $purchaseOrder->status) == 'Approved' ? 'selected' : '' }}>Approved</option>
                        <option value="Received" {{ old('status', $purchaseOrder->status) == 'Received' ? 'selected' : '' }}>Received</option>
                        <option value="Cancelled" {{ old('status', $purchaseOrder->status) == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>
    
    <!-- Items Section -->
    <div class="card mt-3">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h5 class="mb-0">Items</h5>
                </div>
                <div class="col-md-4 text-end">
                    <button type="button" class="btn btn-success btn-sm" id="addRowBtn">
                        <i class="fas fa-plus me-1"></i>Add Row
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="itemsTable">
                    <thead class="table-primary">
                        <tr>
                            <th style="width:35%">Item</th>
                            <th style="width:15%">Quantity</th>
                            <th style="width:15%">Rate</th>
                            <th style="width:20%">Amount</th>
                            <th style="width:5%">Action</th>
                        </tr>
                    </thead>
                    <tbody id="itemsTableBody">
                        <!-- Existing items will be rendered here -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Amount Details Section -->
    <div class="row mt-3">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-3">
                        <label class="col-sm-4 col-form-label">Remarks</label>
                        <div class="col-sm-8">
                            <textarea class="form-control" 
                                      name="remarks" 
                                      rows="5"
                                      placeholder="Enter any remarks or notes">{{ old('remarks', $purchaseOrder->remarks) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th class="text-end" style="font-size: 17px;">Total Amount:</th>
                            <th class="text-end" style="font-size: 17px;">
                                <h4><b><span id="currency_symbol">{{ $purchaseOrder->currency }}</span> <span id="total_amt">0.000</span></b></h4>
                            </th>
                        </tr>
                        <tr>
                            <td></td>
                            <td class="text-end text-muted">OMR Equivalent:</td>
                            <td class="text-end"><b>OMR <span id="total_amt_omr">0.000</span></b></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Buttons -->
    <div class="card mt-3">
        <div class="card-footer">
            <div class="row">
                <div class="col-md-3 offset-md-3">
                    <button type="submit" class="btn btn-primary btn-block w-100" id="saveBtn">
                        <i class="fas fa-save me-2"></i>Update Purchase Order
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
let itemIndex = {{ count($purchaseOrder->items) + 1 }};
const itemsData = @json($items);
const existingItems = @json($purchaseOrder->items);

$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        theme: 'bootstrap-5',
        width: '100%'
    });
    
    // Update currency symbol when currency changes
    $('#currency').change(function() {
        $('#currency_symbol').text($(this).val());
    });
    // Hide/show currency value when OMR selected
    function toggleCurrencyValue() {
        const c = $('#currency').val();
        if (c === 'OMR') {
            $('#currency_value').val('1.000');
            $('#currency_value').closest('.col-sm-2').hide();
        } else {
            $('#currency_value').closest('.col-sm-2').show();
        }
    }
    toggleCurrencyValue();
    $('#currency').on('change', toggleCurrencyValue);
    // store previous currency_value for conversions
    let prevCurrencyValue = parseFloat($('#currency_value').val()) || 1;
    $('#currency_value').data('prev-cv', prevCurrencyValue);
    $('#currency_value').on('input change', function() {
        const oldCv = parseFloat($(this).data('prev-cv')) || 1;
        const newCv = parseFloat($(this).val()) || 1;
        if (oldCv !== newCv) {
            const factor = oldCv / newCv;
            // convert existing rates
            $('#itemsTableBody').find('input[id^="rate_"]').each(function() {
                const cur = parseFloat($(this).val()) || 0;
                $(this).val((cur * factor).toFixed(3));
                // trigger change to recalc amount
                const id = $(this).attr('id').split('_')[1];
                calculateAmount(id);
            });
            $(this).data('prev-cv', newCv);
        }
    });
    // when currency changes, convert rates using prev/new currency_value
    $('#currency').on('change', function() {
        const prevCv = parseFloat($('#currency_value').data('prev-cv')) || 1;
        const newCv = parseFloat($('#currency_value').val()) || 1;
        const factor = prevCv / newCv;
        $('#itemsTableBody').find('input[id^="rate_"]').each(function() {
            const cur = parseFloat($(this).val()) || 0;
            $(this).val((cur * factor).toFixed(3));
            const id = $(this).attr('id').split('_')[1];
            calculateAmount(id);
        });
        $('#currency_symbol').text($(this).val());
        $('#currency_value').data('prev-cv', newCv);
    });
    
    // Load existing items
    existingItems.forEach((item, index) => {
        const rowId = index + 1;
        addItemRow(rowId, item);
    });
    
    calculateTotal();
    
    // Add row button
    $('#addRowBtn').click(function() {
        addItemRow(itemIndex);
        itemIndex++;
        $('#hidden_rowcount').val(itemIndex);
    });
    
    function addItemRow(rowId, existingItem = null) {
        const rowHtml = `
            <tr id="row_${rowId}">
                <td>
                    <select class="form-select form-select-sm select2-item" 
                            id="item_${rowId}" 
                            name="items[${rowId}][item_id]" 
                            required
                            onchange="updateItemDetails(${rowId})">
                        <option value="">-Select Item-</option>
                        ${itemsData.map(item => `<option value="${item.id}" data-price="${item.sale_price || 0}" ${existingItem && existingItem.item_id == item.id ? 'selected' : ''}>${item.item_name} (${item.oem_part_no})</option>`).join('')}
                    </select>
                </td>
                <td>
                    <input type="number" 
                           class="form-control form-control-sm" 
                           id="qty_${rowId}" 
                           name="items[${rowId}][qty]" 
                           value="${existingItem ? existingItem.qty : 1}" 
                           min="0.01" 
                           step="0.01"
                           required
                           onchange="calculateAmount(${rowId})">
                </td>
                <td>
                    <input type="number" 
                           class="form-control form-control-sm text-end" 
                           id="rate_${rowId}" 
                           name="items[${rowId}][rate]" 
                           value="${existingItem ? parseFloat(existingItem.rate).toFixed(3) : '0.000'}" 
                           min="0" 
                           step="0.001"
                           required
                           onchange="calculateAmount(${rowId})">
                </td>
                <td class="text-end">
                    <strong id="amount_${rowId}">0.000</strong>
                    <input type="hidden" id="amount_hidden_${rowId}" name="items[${rowId}][amount]" value="0">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-danger" onclick="removeRow(${rowId})">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        
        $('#itemsTableBody').append(rowHtml);
        
        // Initialize Select2 for the new dropdown
        $(`#item_${rowId}`).select2({
            theme: 'bootstrap-5',
            width: '100%',
            dropdownParent: $(`#row_${rowId}`)
        });
        
        if (existingItem) {
            calculateAmount(rowId);
        }
    }
    
    window.updateItemDetails = function(rowId) {
        const selectedOption = $(`#item_${rowId} option:selected`);
        const price = parseFloat(selectedOption.data('price')) || 0;
        // Convert stored price (OMR) to selected currency
        const currency = $('#currency').val();
        const cv = parseFloat($('#currency_value').val()) || 1;
        const rateInCurrency = (currency === 'OMR') ? price : (price / cv);
        $(`#rate_${rowId}`).val(rateInCurrency.toFixed(3));
        calculateAmount(rowId);
    };
    
    window.removeRow = function(id) {
        $(`#row_${id}`).remove();
        calculateTotal();
    };
    
    window.calculateAmount = function(rowId) {
        const qty = parseFloat($(`#qty_${rowId}`).val()) || 0;
        const rate = parseFloat($(`#rate_${rowId}`).val()) || 0;
        const amount = qty * rate;
        
        $(`#amount_${rowId}`).text(amount.toFixed(3));
        $(`#amount_hidden_${rowId}`).val(amount.toFixed(3));
        
        calculateTotal();
    };
    
    function calculateTotal() {
        const rowcount = parseInt($('#hidden_rowcount').val());
        let total = 0;
        
        for (let i = 1; i < rowcount; i++) {
            if (document.getElementById(`amount_hidden_${i}`)) {
                const amount = parseFloat($(`#amount_hidden_${i}`).val()) || 0;
                total += amount;
            }
        }
        
        $('#total_amt').text(total.toFixed(3));
        const cv = parseFloat($('#currency_value').val()) || 1;
        const totalOmr = total * cv;
        $('#total_amt_omr').text(totalOmr.toFixed(3));
    }
    
    // Form validation
    $('#poForm').submit(function(e) {
        if ($('#itemsTableBody tr').length === 0) {
            e.preventDefault();
            alert('Please add at least one item to the purchase order.');
            return false;
        }
    });
});
</script>
@endpush
