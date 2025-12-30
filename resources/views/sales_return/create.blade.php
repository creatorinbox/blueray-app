@extends('layouts.app')

@section('title', 'Sales Return')

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
.table td {
    vertical-align: middle;
}
.form-group {
    margin-bottom: 1rem;
}
.table-responsive {
    overflow-x: auto;
}
</style>
@endpush

@section('content')
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-undo me-2"></i>Sales Return
    </h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('sales-return.index') }}">Sales Returns</a></li>
            <li class="breadcrumb-item active">Create</li>
        </ol>
    </nav>
</div>

<form action="{{ route('sales-return.store') }}" method="POST" id="salesReturnForm">
    @csrf
    <input type="hidden" id="hidden_rowcount" value="1" name="hidden_rowcount">
    <input type="hidden" value="0" id="hidden_update_rowid" name="hidden_update_rowid">

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-file-invoice me-2"></i>Return Information
            </h5>
        </div>
        <div class="card-body">
            <!-- Basic Return Details -->
            <div class="row mb-3">
                @if(!empty($invoice->invoice_no))
                <div class="col-md-3">
                    <label class="form-label">Sales Code <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" value="{{ $invoice->invoice_no }}" readonly>
                    <input type="hidden" name="sales_invoice_id" value="{{ $invoice->id }}">
                </div>
                @endif
                
                @if(isset($invoice) && $invoice->customer_id)
                <div class="col-md-3">
                    <label class="form-label">Customer Name <span class="text-danger">*</span></label>
                    @php
                        $customerName = 'Loading...';
                        
                        // Try different sources for customer name
                        if (isset($customer_name) && !empty($customer_name)) {
                            $customerName = $customer_name;
                        } elseif (isset($invoice->customer_name) && !empty($invoice->customer_name)) {
                            // If customer name is directly on invoice
                            $customerName = $invoice->customer_name;
                        } elseif (isset($invoice->customer) && $invoice->customer && !empty($invoice->customer->customer_name)) {
                            $customerName = $invoice->customer->customer_name;
                        } else {
                            // Try to get customer from customers collection
                            $customer = collect($customers ?? [])->firstWhere('id', $invoice->customer_id);
                            if ($customer && isset($customer->customer_name)) {
                                $customerName = $customer->customer_name;
                            } else {
                                // Last resort - make an attempt to show something useful
                                $customerName = 'Customer (ID: ' . $invoice->customer_id . ')';
                            }
                        }
                    @endphp
                    <input type="text" class="form-control" value="{{ $customerName }}" readonly>
                    <input type="hidden" name="customer_id" id="customer_id" value="{{ $invoice->customer_id }}">
                    
                    <!-- Debug info - remove in production -->
                    {{-- Debug: Customer ID = {{ $invoice->customer_id }}, 
                    Has Relationship = {{ isset($invoice->customer) ? 'Yes' : 'No' }}, 
                    Customers Count = {{ count($customers ?? []) }} --}}
                </div>
                @else
                <div class="col-md-3">
                    <label class="form-label">Customer Name <span class="text-danger">*</span></label>
                    <select class="form-control select2" id="customer_id" name="customer_id" required>
                        <option value="">-Select-</option>
                        @foreach($customers ?? [] as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->customer_name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                
                @if(!empty($return_code))
                <div class="col-md-3">
                    <label class="form-label">Return Code <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" value="#{{ $return_code }}" readonly>
                    <input type="hidden" name="return_code" value="{{ $return_code }}">
                </div>
                @endif
                
                <div class="col-md-3">
                    <label class="form-label">Return Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control @error('return_date') is-invalid @enderror" 
                           id="return_date" name="return_date" 
                           value="{{ old('return_date', date('Y-m-d')) }}" required>
                    @error('return_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-3">
                    <label class="form-label">Return Status <span class="text-danger">*</span></label>
                    <select class="form-control select2 @error('return_status') is-invalid @enderror" 
                            id="return_status" name="return_status" required>
                        <option value="Return" {{ old('return_status') == 'Return' ? 'selected' : '' }}>Return</option>
                        <option value="Cancel" {{ old('return_status') == 'Cancel' ? 'selected' : '' }}>Cancel</option>
                    </select>
                    @error('return_status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Reference No</label>
                    <input type="text" class="form-control" 
                           name="reference_no" id="reference_no" 
                           value="{{ old('reference_no') }}" placeholder="Enter reference number">
                </div>
            </div>
        </div>
    </div>

    <!-- Items Section -->
    <div class="card mt-3">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col-md-8 offset-md-2">
                    <div class="input-group position-relative">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" 
                               class="form-control" 
                               placeholder="Search Item name/Barcode/Item code" 
                               id="item_search"
                               autocomplete="off">
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="sales_table">
                    <thead class="table-primary">
                        <tr>
                            <th style="width:20%">Item Name</th>
                            <th style="width:10%">Quantity</th>
                            <th style="width:12%">Unit Price (OMR)</th>
                            <th style="width:10%">Discount (OMR)</th>
                            <th style="width:8%">Tax %</th>
                            <th style="width:10%">Tax Amount (OMR)</th>
                            <th style="width:12%">Total Amount (OMR)</th>
                            <th style="width:8%">Action</th>
                        </tr>
                    </thead>
                    <tbody id="itemsTableBody">
                        @if(isset($items) && count($items) > 0)
                            @foreach($items as $index => $item)
                            <tr class="item-row" id="row_{{ $index + 1 }}" data-index="{{ $index + 1 }}">
                                <td>
                                    <strong>{{ $item->item_name ?? '' }}</strong><br>
                                    <small class="text-muted">{{ $item->item_code ?? '' }}</small>
                                    <input type="hidden" name="item_id[]" id="tr_item_id_{{ $index + 1 }}" value="{{ $item->item_id ?? '' }}">
                                    <input type="hidden" id="tr_tax_type_{{ $index + 1 }}" value="Exclusive">
                                    <input type="hidden" id="tr_tax_id_{{ $index + 1 }}" value="{{ $item->tax_id ?? 1 }}">
                                    <input type="hidden" id="tr_tax_value_{{ $index + 1 }}" value="{{ $item->tax_rate ?? 0 }}">
                                    <input type="hidden" id="description_{{ $index + 1 }}" value="">
                                    <input type="hidden" id="item_discount_input_{{ $index + 1 }}" value="0">
                                    <input type="hidden" id="item_discount_type_{{ $index + 1 }}" value="Fixed">
                                </td>
                                <td>
                                    <input type="number" class="form-control form-control-sm quantity" 
                                           id="td_data_{{ $index + 1 }}_3"
                                           name="qty[]" 
                                           value="{{ $item->qty ?? 0 }}" 
                                           min="0" 
                                           max="{{ $item->qty ?? 0 }}"
                                           step="0.01" 
                                           onchange="calculate_tax({{ $index + 1 }})" required>
                                </td>
                                <td>
                                    <input type="number" class="form-control form-control-sm text-end unit-price" 
                                           id="td_data_{{ $index + 1 }}_10"
                                           name="price[]" 
                                           value="{{ $item->sale_price ?? 0 }}" 
                                           readonly 
                                           step="0.001">
                                    <input type="hidden" id="td_data_{{ $index + 1 }}_4" value="{{ $item->sale_price ?? 0 }}">
                                    <input type="hidden" id="td_data_{{ $index + 1 }}_13" value="{{ $item->sale_price ?? 0 }}">
                                </td>
                                <td>
                                    <input type="number" class="form-control form-control-sm text-end discount" 
                                           id="td_data_{{ $index + 1 }}_8"
                                           value="0" 
                                           min="0" 
                                           step="0.01" 
                                           onchange="calculate_tax({{ $index + 1 }})">
                                </td>
                                <td class="text-center">
                                    <span id="td_data_{{ $index + 1 }}_12">0%</span>
                                </td>
                                <td class="text-end">
                                    <span id="td_data_{{ $index + 1 }}_7">0.000</span>
                                    <input type="hidden" id="td_data_{{ $index + 1 }}_11" name="tax_amount[]" value="0">
                                </td>
                                <td class="text-end">
                                    <strong><span id="td_data_{{ $index + 1 }}_9">0.000</span></strong>
                                    <input type="hidden" name="amount[]" id="td_data_{{ $index + 1 }}_amount" value="0">
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-danger remove-row" onclick="removerow({{ $index + 1 }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
            
            <div class="text-muted text-center py-3 {{ (isset($items) && count($items) > 0) ? 'd-none' : '' }}" id="noItemsMsg">
                <i class="fas fa-box-open fa-2x mb-2 opacity-50"></i>
                <p>No items added yet. Search and select items to add.</p>
            </div>
        </div>
    </div>

                <!-- Totals Section -->
    <!-- Amount Details Section -->
    <div class="row mt-3">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-3">
                        <label class="col-sm-4 col-form-label">Total Quantity</label>
                        <div class="col-sm-8">
                            <h4 class="text-success total_quantity">0</h4>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <label class="col-sm-4 col-form-label">Other Charges</label>
                        <div class="col-sm-5">
                            <input type="number" class="form-control text-end only_currency" 
                                   id="other_charges_input" 
                                   name="other_charges_input" 
                                   value="0" 
                                   min="0" 
                                   step="0.001"
                                   onkeyup="final_total()">
                        </div>
                        <div class="col-sm-3">
                            <select class="form-control" id="other_charges_tax_id" name="other_charges_tax_id" onchange="final_total()">
                                <option value="">None</option>
                                <!-- Add tax options here if needed -->
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <label class="col-sm-4 col-form-label">Discount on All</label>
                        <div class="col-sm-5">
                            <input type="number" class="form-control text-end only_currency" 
                                   id="discount_to_all_input" 
                                   name="discount_to_all_input" 
                                   value="0" 
                                   min="0" 
                                   step="0.001"
                                   onkeyup="enable_or_disable_item_discount()">
                        </div>
                        <div class="col-sm-3">
                            <select class="form-control" id="discount_to_all_type" name="discount_to_all_type" onchange="final_total()">
                                <option value="in_percentage">Per%</option>
                                <option value="in_fixed">Fixed</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <label class="col-sm-4 col-form-label">Return Note</label>
                        <div class="col-sm-8">
                            <textarea class="form-control" id="return_note" name="return_note" rows="4"></textarea>
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
                            <th class="text-end" style="padding-left:10%;font-size: 17px;">
                                <h4><b id="subtotal_amt">0.000</b></h4>
                            </th>
                        </tr>
                        <tr>
                            <th class="text-end" style="font-size: 17px;">Other Charges:</th>
                            <th class="text-end" style="padding-left:10%;font-size: 17px;">
                                <h4><b id="other_charges_amt">0.000</b></h4>
                            </th>
                        </tr>
                        <tr>
                            <th class="text-end" style="font-size: 17px;">Discount on All:</th>
                            <th class="text-end" style="padding-left:10%;font-size: 17px;">
                                <h4><b id="discount_to_all_amt">0.000</b></h4>
                            </th>
                        </tr>
                        <tr>
                            <th class="text-end" style="font-size: 17px;">Round Off:</th>
                            <th class="text-end" style="padding-left:10%;font-size: 17px;">
                                <h4><b id="round_off_amt">0.000</b></h4>
                            </th>
                        </tr>
                        <tr class="table-active">
                            <th class="text-end" style="font-size: 17px;">Grand Total:</th>
                            <th class="text-end" style="padding-left:10%;font-size: 17px;">
                                <h4><b id="total_amt">0.000</b></h4>
                            </th>
                        </tr>
                    </table>
                    
                    <!-- Hidden inputs for form submission -->
                    <input type="hidden" name="subtotal" id="hidden_subtotal" value="0">
                    <input type="hidden" name="other_charges_amount" id="hidden_other_charges_amt" value="0">
                    <input type="hidden" name="discount_to_all_amt" id="hidden_discount_to_all_amt" value="0">
                    <input type="hidden" name="round_off" id="hidden_round_off" value="0">
                    <input type="hidden" name="total_amt" id="hidden_total_amt" value="0">
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
                        <i class="fas fa-save me-2"></i>Save Sales Return
                    </button>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('sales-return.index') }}" class="btn btn-secondary btn-block">
                        <i class="fas fa-times me-2"></i>Close
                    </a>
                </div>
                <div class="col-md-3">
                    <button type="button" class="btn btn-info btn-block" onclick="final_total(); console.log('Manual calculation triggered');">
                        <i class="fas fa-calculator me-2"></i>Recalculate
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
let itemIndex = 1;
const itemsData = @json($items ?? []);

console.log('Items loaded:', itemsData.length);

$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        theme: 'bootstrap-5',
        width: '100%'
    });
    
    // Set initial row count
    if ($('.item-row').length > 0) {
        itemIndex = $('.item-row').length + 1;
        $('#hidden_rowcount').val(itemIndex);
    }
    
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
        const rate = parseFloat(item.sale_price || 0);
        const vatRate = parseFloat(item.vat_rate || 0);
        const amount = qty * rate;
        const discount = 0;
        const taxAmount = (amount - discount) * (vatRate / 100);
        const totalAmount = amount - discount + taxAmount;
        
        const rowId = itemIndex;
        
        const rowHtml = `
            <tr id="row_${rowId}" class="item-row" data-index="${rowId}">
                <td>
                    <strong>${item.item_name}</strong><br>
                    <small class="text-muted">${item.item_code}</small>
                    <input type="hidden" name="item_id[]" id="tr_item_id_${rowId}" value="${item.id}">
                    <input type="hidden" id="tr_tax_type_${rowId}" value="Exclusive">
                    <input type="hidden" id="tr_tax_id_${rowId}" value="${item.tax_id || 1}">
                    <input type="hidden" id="tr_tax_value_${rowId}" value="${vatRate}">
                    <input type="hidden" id="description_${rowId}" value="">
                    <input type="hidden" id="item_discount_input_${rowId}" value="0">
                    <input type="hidden" id="item_discount_type_${rowId}" value="Fixed">
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm quantity" 
                           id="td_data_${rowId}_3" 
                           name="qty[]" 
                           value="${qty}" 
                           min="0.01" 
                           step="0.01"
                           onchange="calculate_tax(${rowId})">
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm text-end unit-price" 
                           id="td_data_${rowId}_10" 
                           name="price[]" 
                           value="${rate.toFixed(3)}" 
                           readonly
                           step="0.001">
                    <input type="hidden" id="td_data_${rowId}_4" value="${rate.toFixed(3)}">
                    <input type="hidden" id="td_data_${rowId}_13" value="${rate.toFixed(3)}">
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm text-end discount" 
                           id="td_data_${rowId}_8" 
                           value="0.000" 
                           min="0" 
                           step="0.001"
                           onchange="calculate_tax(${rowId})">
                </td>
                <td class="text-center">
                    <span id="td_data_${rowId}_12">${vatRate}%</span>
                </td>
                <td class="text-end">
                    <span id="td_data_${rowId}_7">${taxAmount.toFixed(3)}</span>
                    <input type="hidden" id="td_data_${rowId}_11" name="tax_amount[]" value="${taxAmount.toFixed(3)}">
                </td>
                <td class="text-end">
                    <strong><span id="td_data_${rowId}_9">${totalAmount.toFixed(3)}</span></strong>
                    <input type="hidden" name="amount[]" id="td_data_${rowId}_amount" value="${totalAmount.toFixed(3)}">
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
    
    // Calculate initial totals
    if ($('.item-row').length > 0) {
        // Recalculate all existing rows
        $('.item-row').each(function() {
            const index = $(this).data('index');
            if (index) {
                // Get the actual tax rate from the data or default to 5%
                const vatRate = parseFloat($('#tr_tax_value_' + index).val()) || 5;
                $('#tr_tax_value_' + index).val(vatRate);
                $('#td_data_' + index + '_12').text(vatRate + '%');
                
                calculate_tax(index);
            }
        });
        
        // Force final total calculation after initial setup
        setTimeout(function() {
            final_total();
        }, 100);
    } else {
        final_total();
    }
});

// Global functions for inline event handlers
window.removerow = function(id) {
    $(`#row_${id}`).remove();
    if ($('#itemsTableBody tr').length === 0) {
        $('#noItemsMsg').show();
    }
    final_total();
};

window.calculate_tax = function(i) {
    // Get values
    var qty = parseFloat($("#td_data_"+i+"_3").val() || 0);
    var sales_price = parseFloat($("#td_data_"+i+"_10").val() || 0);
    var discount_amt = parseFloat($("#td_data_"+i+"_8").val() || 0);
    var tax_rate = parseFloat($("#tr_tax_value_"+i).val() || 0);
    var tax_type = $("#tr_tax_type_"+i).val() || 'Exclusive';
    
    // Calculate line amount before tax
    var line_amount = qty * sales_price;
    var discounted_amount = line_amount - discount_amt;
    
    // Calculate tax
    var tax_amount = 0;
    if (tax_type == 'Inclusive') {
        tax_amount = discounted_amount - (discounted_amount / (1 + (tax_rate / 100)));
    } else {
        tax_amount = (discounted_amount * tax_rate) / 100;
    }
    
    // Calculate total
    var total_amount = discounted_amount + (tax_type == 'Inclusive' ? 0 : tax_amount);
    
    // Update display
    $("#td_data_"+i+"_7").text(tax_amount.toFixed(3));
    $("#td_data_"+i+"_11").val(tax_amount.toFixed(3));
    $("#td_data_"+i+"_12").text(tax_rate + "%");
    $("#td_data_"+i+"_9").text(total_amount.toFixed(3));
    $("#td_data_"+i+"_amount").val(total_amount.toFixed(3));
    
    final_total();
};

function set_tax_value(row_id){
    // This function is now handled by calculate_tax
    // Keeping for compatibility
}

function calculate_exclusive(amount, tax_percentage) {
    return (amount * tax_percentage) / 100;
}

function calculate_inclusive(amount_with_tax, tax_percentage) {
    return amount_with_tax - (amount_with_tax / (1 + (tax_percentage / 100)));
}

function round_off(num) {
    return Math.round(num);
}

// Final total calculation
window.final_total = function(){
    var subtotal = 0;
    var total_quantity = 0;
    var tax_amt = 0;
    
    console.log('Final total calculation started');
    
    // Sum all item rows
    $('.item-row').each(function() {
        var rowIndex = $(this).data('index');
        if (rowIndex && $("#td_data_"+rowIndex+"_3").length) {
            var qty = parseFloat($("#td_data_"+rowIndex+"_3").val() || 0);
            var itemTotal = parseFloat($("#td_data_"+rowIndex+"_9").text() || 0);
            var itemTax = parseFloat($("#td_data_"+rowIndex+"_11").val() || 0);
            
            console.log('Row', rowIndex, 'qty:', qty, 'itemTotal:', itemTotal, 'itemTax:', itemTax);
            
            if (qty > 0) {
                subtotal += itemTotal;
                total_quantity += qty;
                tax_amt += itemTax;
            }
        }
    });
    
    console.log('Subtotal:', subtotal, 'Total quantity:', total_quantity);
    
    // Update quantity display
    $(".total_quantity").html(total_quantity.toFixed(2));
    
    // Calculate other charges
    var other_charges_input = parseFloat($("#other_charges_input").val() || 0);
    var other_charges_total_amt = other_charges_input;
    
    // Calculate discount
    var discount_input = parseFloat($("#discount_to_all_input").val() || 0);
    var discount = 0;
    var taxable = subtotal + other_charges_total_amt;
    
    if (discount_input > 0) {
        var discount_type = $("#discount_to_all_type").val();
        if (discount_type == 'in_fixed') {
            discount = discount_input;
        } else if (discount_type == 'in_percentage') {
            discount = (taxable * discount_input) / 100;
        }
    }
    
    taxable -= discount;
    
    // Calculate round off
    var subtotal_round = round_off(taxable);
    var subtotal_diff = subtotal_round - taxable;
    
    console.log('Final total after calculations:', subtotal_round);
    
    // Update display
    $("#subtotal_amt").html(subtotal.toFixed(3));
    $("#other_charges_amt").html(other_charges_total_amt.toFixed(3));
    $("#discount_to_all_amt").html(discount.toFixed(3));
    $("#round_off_amt").html(subtotal_diff.toFixed(3));
    $("#total_amt").html(subtotal_round.toFixed(3));
    
    // Update hidden fields - Make sure these are set correctly!
    $("#hidden_subtotal").val(subtotal.toFixed(3));
    $("#hidden_other_charges_amt").val(other_charges_total_amt.toFixed(3));
    $("#hidden_discount_to_all_amt").val(discount.toFixed(3));
    $("#hidden_round_off").val(subtotal_diff.toFixed(3));
    $("#hidden_total_amt").val(subtotal_round.toFixed(3));
    
    console.log('Hidden total_amt set to:', $("#hidden_total_amt").val());
};

window.enable_or_disable_item_discount = function(){
    var rowcount=$("#hidden_rowcount").val();
    for(k=1;k<=rowcount;k++){
        if(document.getElementById("tr_item_id_"+k)){
            calculate_tax(k);
        }//if end
    }//for end
};

// Form validation
$('#salesReturnForm').on('submit', function(e) {
    if ($('#itemsTableBody tr').length === 0) {
        e.preventDefault();
        alert('Please add at least one item to the sales return.');
        return false;
    }
});

</script>
@endpush

