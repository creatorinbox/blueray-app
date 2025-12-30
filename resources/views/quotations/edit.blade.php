@extends('layouts.app')

@section('title', 'Edit Quotation')

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
        <i class="fas fa-edit me-2"></i>Edit Quotation
    </h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('quotations.index') }}">Quotations</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol>
    </nav>
</div>

<form action="{{ route('quotations.update', $quotation->id) }}" method="POST" id="quotationForm">
    @csrf
    @method('PUT')
    <input type="hidden" id="hidden_rowcount" value="{{ count($quotation->items) + 1 }}">
    
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-file-contract me-2"></i>Quotation Information
            </h5>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Quotation No <span class="text-danger">*</span></label>
                <div class="col-sm-4">
                    <input type="text" 
                           class="form-control @error('quotation_no') is-invalid @enderror" 
                           id="quotation_no" 
                           name="quotation_no" 
                           value="{{ old('quotation_no', $quotation->quotation_no) }}" 
                           readonly
                           required>
                    @error('quotation_no')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <label class="col-sm-2 col-form-label">Quotation Date <span class="text-danger">*</span></label>
                <div class="col-sm-4">
                    <input type="date" 
                           class="form-control @error('quotation_date') is-invalid @enderror" 
                           id="quotation_date" 
                           name="quotation_date" 
                           value="{{ old('quotation_date', $quotation->quotation_date ? $quotation->quotation_date->format('Y-m-d') : '') }}" 
                           required>
                    @error('quotation_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Customer <span class="text-danger">*</span></label>
                <div class="col-sm-4">
                    <select class="form-select select2 @error('customer_id') is-invalid @enderror" 
                            id="customer_id" 
                            name="customer_id" 
                            required>
                        <option value="">-Select-</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" 
                                    {{ old('customer_id', $quotation->customer_id) == $customer->id ? 'selected' : '' }}>
                                {{ $customer->customer_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('customer_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <label class="col-sm-2 col-form-label">Valid Till <span class="text-danger">*</span></label>
                <div class="col-sm-4">
                    <input type="date" 
                           class="form-control @error('valid_till') is-invalid @enderror" 
                           id="valid_till" 
                           name="valid_till" 
                           value="{{ old('valid_till', $quotation->valid_till ? $quotation->valid_till->format('Y-m-d') : '') }}" 
                           required>
                    @error('valid_till')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Status</label>
                <div class="col-sm-4">
                    <input type="text" 
                           readonly 
                           class="form-control" 
                           value="{{ ucfirst($quotation->is_active) }}" />
                </div>
                
                <label class="col-sm-2 col-form-label">Reference/Subject</label>
                <div class="col-sm-4">
                    <input type="text" 
                           class="form-control" 
                           name="reference_no" 
                           value="{{ old('reference_no', $quotation->reference_no) }}"
                           placeholder="Enter reference or subject">
                </div>
            </div>
        </div>
    </div>
    
    <!-- Items Section -->
    <div class="card mt-3">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col-md-2">
                    <button type="button" class="btn btn-warning btn-sm" id="addServiceBtn">
                        <i class="fas fa-plus me-1"></i>Add Service/Job
                    </button>
                </div>
                <div class="col-md-6 offset-md-2">
                    <div class="input-group position-relative">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" 
                               class="form-control" 
                               placeholder="Search Item name/Barcode/Item code" 
                               id="item_search"
                               autocomplete="off">
                    </div>
                </div>
                <div class="col-md-2">
                    <a href="#" class="btn btn-warning btn-sm" onclick="alert('Item management feature coming soon'); return false;">
                        <i class="fas fa-plus me-1"></i>Add Item
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="itemsTable">
                    <thead class="table-primary">
                        <tr>
                            <th style="width:25%">Item Name</th>
                            <th style="width:15%">Supplier</th>
                            <th style="width:10%">Quantity</th>
                            <th style="width:15%">Unit Price (OMR)</th>
                            <th style="width:15%">Total Amount (OMR)</th>
                            <th style="width:3%">Action</th>
                        </tr>
                    </thead>
                    <tbody id="itemsTableBody">
                        @foreach($quotation->items as $index => $item)
                        <tr id="row_{{ $index + 1 }}">
                            <td>
                                <strong>{{ $item->item->item_name }}</strong><br>
                                <small class="text-muted">{{ $item->item->item_code }}</small>
                                <input type="hidden" name="items[{{ $index + 1 }}][item_id]" id="tr_item_id_{{ $index + 1 }}" value="{{ $item->item_id }}">
                            </td>
                            <td>
                                <small class="text-muted">{{ $item->item->supplier ? $item->item->supplier->supplier_name : '-' }}</small>
                                <input type="hidden" name="items[{{ $index + 1 }}][supplier_id]" value="{{ $item->item->supplier_id ?? '' }}">
                            </td>
                            <td>
                                <input type="number" class="form-control form-control-sm" 
                                       id="td_data_{{ $index + 1 }}_3" 
                                       name="items[{{ $index + 1 }}][qty]" 
                                       value="{{ $item->qty }}" 
                                       min="0.01" 
                                       step="0.01"
                                       onchange="calculate_tax({{ $index + 1 }})">
                            </td>
                            <td>
                                <input type="number" class="form-control form-control-sm text-end" 
                                       id="td_data_{{ $index + 1 }}_10" 
                                       name="items[{{ $index + 1 }}][rate]" 
                                       value="{{ number_format($item->rate, 3) }}" 
                                       min="0.01" 
                                       step="0.001"
                                       onchange="calculate_tax({{ $index + 1 }})">
                            </td>
                            <td class="text-end">
                                <strong id="td_data_{{ $index + 1 }}_9">{{ number_format($item->qty * $item->rate, 3) }}</strong>
                                <input type="hidden" id="td_data_{{ $index + 1 }}_13" value="{{ number_format($item->rate, 3) }}">
                                <input type="hidden" name="items[{{ $index + 1 }}][description]" value="{{ $item->description ?? '' }}">
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-danger" onclick="removerow({{ $index + 1 }})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="text-muted text-center py-3 {{ count($quotation->items) > 0 ? 'd-none' : '' }}" id="noItemsMsg">
                <i class="fas fa-box-open fa-2x mb-2 opacity-50"></i>
                <p>No items added yet. Search and select items to add.</p>
            </div>
        </div>
    </div>

    <!-- Amount Details Section -->
    <div class="row mt-3">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-3">
                        <label class="col-sm-4 col-form-label">Total Quantity</label>
                        <div class="col-sm-8">
                            <h4 class="text-success total_quantity">{{ number_format($quotation->items->sum('qty'), 2) }}</h4>
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-4 col-form-label">Terms & Conditions</label>
                        <div class="col-sm-8">
                            <textarea class="form-control" 
                                      name="terms_conditions" 
                                      rows="4">{{ old('terms_conditions', $quotation->terms_conditions ?? 'Payment Terms: 30 Days from invoice date\nDelivery: 7-10 Working Days\nValidity: 30 Days from quotation date') }}</textarea>
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
                            <th class="text-end" style="font-size: 17px;">Subtotal:</th>
                            <th class="text-end" style="font-size: 17px;">
                                <h4><b>OMR <span id="subtotal">{{ number_format($quotation->subtotal ?? 0, 3) }}</span></b></h4>
                            </th>
                        </tr>
                        <tr>
                            <th class="text-end" style="font-size: 17px;">Discount (%)</th>
                            <th class="text-end">
                                <div class="input-group input-group-sm justify-content-end">
                                    <input type="number" id="global_discount_percent" name="global_discount_percent" class="form-control text-end" value="{{ $quotation->discount_percent ?? 0 }}" min="0" step="0.01" style="max-width:120px;">
                                </div>
                            </th>
                        </tr>
                        <tr>
                            <th class="text-end" style="font-size: 17px;">Discount Amount:</th>
                            <th class="text-end">
                                <h6>OMR <span id="discount_amount">{{ number_format($quotation->discount_amount ?? 0, 3) }}</span></h6>
                                <input type="hidden" id="discount_amount_input" name="discount_amount" value="{{ number_format($quotation->discount_amount ?? 0, 3) }}">
                            </th>
                        </tr>
                        <tr>
                            <th class="text-end" style="font-size: 17px;">Tax (%)</th>
                            <th class="text-end">
                                <div class="input-group input-group-sm justify-content-end">
                                    <input type="number" id="global_tax_percent" name="global_tax_percent" class="form-control text-end" value="{{ $quotation->tax_percent ?? 0 }}" min="0" step="0.01" style="max-width:120px;">
                                </div>
                            </th>
                        </tr>
                        <tr>
                            <th class="text-end" style="font-size: 17px;">Tax Amount:</th>
                            <th class="text-end">
                                <h6>OMR <span id="tax_amount">{{ number_format($quotation->tax_amount ?? 0, 3) }}</span></h6>
                                <input type="hidden" id="tax_amount_input" name="tax_amount" value="{{ number_format($quotation->tax_amount ?? 0, 3) }}">
                            </th>
                        </tr>
                        <tr>
                            <th class="text-end" style="font-size: 17px;">Grand Total:</th>
                            <th class="text-end" style="font-size: 17px;">
                                <h4><b>OMR <span id="total_amt">{{ number_format($quotation->total_amount ?? 0, 3) }}</span></b></h4>
                                <input type="hidden" id="total_amount_input" name="total_amount" value="{{ number_format($quotation->total_amount ?? 0, 3) }}">
                            </th>
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
                    <button type="submit" class="btn btn-primary btn-block" id="saveBtn">
                        <i class="fas fa-save me-2"></i>Update Quotation
                    </button>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('quotations.index') }}" class="btn btn-secondary btn-block">
                        <i class="fas fa-times me-2"></i>Close
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
let itemIndex = {{ count($quotation->items) + 1 }};
const itemsData = @json($items);

console.log('Items loaded:', itemsData.length);

$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        theme: 'bootstrap-5',
        width: '100%'
    });
    
    // Calculate initial totals
    final_total();
    
    // Item search functionality
    $('#item_search').on('keyup', function() {
        const searchTerm = $(this).val().toLowerCase();
        
        console.log('Search term:', searchTerm);
        
        if (searchTerm.length < 1) {
            $('#search_results_dropdown').hide();
            return;
        }
        
        // Filter items based on search
        const filteredItems = itemsData.filter(item => {
            const itemName = (item.item_name || '').toLowerCase();
            const itemCode = (item.item_code || '').toLowerCase();
            const oemPartNo = (item.oem_part_no || '').toLowerCase();
            
            return itemName.includes(searchTerm) || 
                   itemCode.includes(searchTerm) || 
                   oemPartNo.includes(searchTerm);
        });
        
        console.log('Filtered items:', filteredItems.length);
        
        // Show dropdown with results
        showSearchResults(filteredItems);
    });
    
    function showSearchResults(items) {
        let dropdown = $('#search_results_dropdown');
        
        if (dropdown.length === 0) {
            dropdown = $('<div id="search_results_dropdown" class="list-group position-absolute shadow" style="z-index: 1050; max-height: 400px; overflow-y: auto; width: calc(100% - 48px); top: 100%; left: 48px; margin-top: 5px; border: 1px solid #ddd;"></div>');
            $('#item_search').parent().append(dropdown);
        }
        
        dropdown.empty();
        
        if (items.length === 0) {
            dropdown.append('<div class="list-group-item">No items found</div>');
        } else {
            items.forEach(item => {
                const stock = item.total_stock || 0;
                const stockClass = stock > 0 ? 'bg-success' : 'bg-danger';
                const stockText = stock > 0 ? `Stock: ${parseFloat(stock).toFixed(2)}` : 'Out of Stock';
                const itemCode = item.item_code || 'N/A';
                
                const itemHtml = `
                    <a href="#" class="list-group-item list-group-item-action" data-item-id="${item.id}">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>${item.item_name}</strong><br>
                                <small class="text-muted">${itemCode} | ${item.unit} | OMR ${parseFloat(item.sale_price).toFixed(3)}</small>
                            </div>
                            <div class="text-end">
                                <span class="badge ${stockClass} text-white">${stockText}</span>
                            </div>
                        </div>
                    </a>
                `;
                dropdown.append(itemHtml);
            });
        }
        
        dropdown.show();
        
        // Click handler for search results
        dropdown.find('.list-group-item-action').click(function(e) {
            e.preventDefault();
            const itemId = $(this).data('item-id');
            const item = itemsData.find(i => i.id == itemId);
            if (item) {
                addItemToTable(item);
            }
            dropdown.hide();
            $('#item_search').val('');
        });
    }
    
    // Hide dropdown when clicking outside
    $(document).click(function(e) {
        if (!$(e.target).closest('#item_search').length) {
            $('#search_results_dropdown').hide();
        }
    });
    
    function addItemToTable(item) {
        const qty = 1;
        const rate = parseFloat(item.sale_price);
        const vatRate = item.vat_applicable ? parseFloat(item.vat_rate) : 0;
        const amount = qty * rate;
        const discount = 0;
        const taxAmount = (amount - discount) * (vatRate / 100);
        const totalAmount = amount - discount + taxAmount;
        const supplierName = item.supplier ? item.supplier.supplier_name : '-';
        
        const rowId = itemIndex;
        
        const rowHtml = `
            <tr id="row_${rowId}">
                <td>
                    <strong>${item.item_name}</strong><br>
                    <small class="text-muted">${item.item_code}</small>
                    <input type="hidden" name="items[${rowId}][item_id]" id="tr_item_id_${rowId}" value="${item.id}">
                </td>
                <td>
                    <small class="text-muted">${supplierName}</small>
                    <input type="hidden" name="items[${rowId}][supplier_id]" value="${item.supplier_id || ''}">
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm" 
                           id="td_data_${rowId}_3" 
                           name="items[${rowId}][qty]" 
                           value="${qty}" 
                           min="0.01" 
                           step="0.01"
                           onchange="calculate_tax(${rowId})">
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm text-end" 
                           id="td_data_${rowId}_10" 
                           name="items[${rowId}][rate]" 
                           value="${rate.toFixed(3)}" 
                           min="0.01" 
                           step="0.001"
                           onchange="calculate_tax(${rowId})">
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm text-end" 
                           id="td_data_${rowId}_8" 
                           name="items[${rowId}][discount]" 
                           value="0.000" 
                           min="0" 
                           step="0.001"
                           onchange="calculate_tax(${rowId})">
                </td>
                <td class="text-center">
                    <span id="td_data_${rowId}_12">${vatRate}%</span>
                    <input type="hidden" id="tr_tax_value_${rowId}" value="${vatRate}">
                    <input type="hidden" id="tr_tax_type_${rowId}" value="Exclusive">
                </td>
                <td class="text-end">
                    <span id="td_data_${rowId}_7">${taxAmount.toFixed(3)}</span>
                    <input type="hidden" id="td_data_${rowId}_11" name="items[${rowId}][vat_amount]" value="${taxAmount.toFixed(3)}">
                </td>
                <td class="text-end">
                    <strong id="td_data_${rowId}_9">${totalAmount.toFixed(3)}</strong>
                    <input type="hidden" id="td_data_${rowId}_13" value="${rate.toFixed(3)}">
                    <input type="hidden" name="items[${rowId}][description]" value="">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-danger" onclick="removerow(${rowId})">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        
        $('#itemsTableBody').append(rowHtml);
        $('#noItemsMsg').hide();
        itemIndex++;
        $('#hidden_rowcount').val(itemIndex);
        
        calculate_tax(rowId);
    }
    
    window.removerow = function(id) {
        $(`#row_${id}`).remove();
        if ($('#itemsTableBody tr').length === 0) {
            $('#noItemsMsg').show();
        }
        final_total();
    };
    
    window.calculate_tax = function(i) {
        const qty = parseFloat($(`#td_data_${i}_3`).val()) || 0;
        const rate = parseFloat($(`#td_data_${i}_10`).val()) || 0;
        const discount = parseFloat($(`#td_data_${i}_8`).val()) || 0;
        const taxRate = parseFloat($(`#tr_tax_value_${i}`).val()) || 0;
        
        const amount = qty * rate;
        const taxableAmount = amount - discount;
        const taxAmount = (taxableAmount * taxRate) / 100;
        const totalAmount = taxableAmount + taxAmount;
        
        $(`#td_data_${i}_7`).text(taxAmount.toFixed(3));
        $(`#td_data_${i}_11`).val(taxAmount.toFixed(3));
        $(`#td_data_${i}_9`).text(totalAmount.toFixed(3));
        
        final_total();
    };
    
    function final_total() {
        const rowcount = parseInt($('#hidden_rowcount').val());
        let subtotal = 0;
        let totalQuantity = 0;
        for (let i = 1; i < rowcount; i++) {
            if (document.getElementById(`td_data_${i}_3`)) {
                const qty = parseFloat($(`#td_data_${i}_3`).val()) || 0;
                const rate = parseFloat($(`#td_data_${i}_10`).val()) || 0;
                subtotal += (qty * rate);
                totalQuantity += qty;
            }
        }

        const discountPercent = parseFloat($('#global_discount_percent').val()) || 0;
        const discountAmount = (subtotal * discountPercent) / 100;
        const taxable = subtotal - discountAmount;
        const taxPercent = parseFloat($('#global_tax_percent').val()) || 0;
        const taxAmount = (taxable * taxPercent) / 100;
        const grandTotal = taxable + taxAmount;

        $('.total_quantity').text(totalQuantity.toFixed(2));
        $('#subtotal').text(subtotal.toFixed(3));
        $('#discount_amount').text(discountAmount.toFixed(3));
        $('#tax_amount').text(taxAmount.toFixed(3));
        $('#total_amt').text(grandTotal.toFixed(3));

        // Update hidden inputs so values are submitted
        $('#discount_amount_input').val(discountAmount.toFixed(3));
        $('#tax_amount_input').val(taxAmount.toFixed(3));
        $('#total_amount_input').val(grandTotal.toFixed(3));
    }
    // Recalculate totals when discount or tax percent changes
    $('#global_discount_percent, #global_tax_percent').on('input change', function() {
        final_total();
    });
    
    // Form validation
    $('#quotationForm').submit(function(e) {
        if ($('#itemsTableBody tr').length === 0) {
            e.preventDefault();
            alert('Please add at least one item to the quotation.');
            return false;
        }
    });
});
</script>
@endpush