@extends('layouts.app')

@section('title', 'Create Quotation')

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
        <i class="fas fa-plus me-2"></i>Create New Quotation
    </h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('quotations.index') }}">Quotations</a></li>
            <li class="breadcrumb-item active">Create</li>
        </ol>
    </nav>
</div>

<form action="{{ route('quotations.store') }}" method="POST" id="quotationForm">
    @csrf
    <input type="hidden" id="hidden_rowcount" value="1">
    
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
                           value="{{ old('quotation_no', $quotationNo) }}" 
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
                           value="{{ old('quotation_date', date('Y-m-d')) }}" 
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
                                    {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
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
                           value="{{ old('valid_till', date('Y-m-d', strtotime('+30 days'))) }}" 
                           required>
                    @error('valid_till')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <input type="hidden" name="status" value="Pending">
                
                <label class="col-sm-2 col-form-label">Reference/Subject</label>
                <div class="col-sm-4">
                    <input type="text" 
                           class="form-control" 
                           name="reference_no" 
                           placeholder="Enter reference or subject">
                </div>
            </div>
        </div>
    </div>
    
    <!-- Items Section -->
    <div class="card mt-3">
        <div class="card-header">
            <div class="row align-items-center">
                <!-- Add Service button removed per request -->
                <div class="col-md-6 offset-md-2">
                    <div class="input-group position-relative">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" 
                               class="form-control" 
                               placeholder="Search Item name/Barcode/Item code" 
                               id="item_search"
                               autocomplete="off">
                    </div>
                    <div class="form-check form-check-inline mt-2">
                        <input class="form-check-input" type="checkbox" id="search_by_lot" checked>
                        <label class="form-check-label small" for="search_by_lot">Search by Lot</label>
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
                            <th style="width:38%">Item Name</th>
                            <th style="width:8%">Unit</th>
                            <th style="width:12%">Quantity</th>
                            <th style="width:15%">Unit Price (OMR)</th>
                            <th style="width:20%">Total Amount (OMR)</th>
                            <th style="width:3%">Action</th>
                        </tr>
                    </thead>
                    <tbody id="itemsTableBody">
                        <!-- Items will be added here dynamically -->
                    </tbody>
                </table>
            </div>
            
            <div class="text-muted text-center py-3" id="noItemsMsg">
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
                            <h4 class="text-success total_quantity">0</h4>
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-4 col-form-label">Terms & Conditions</label>
                        <div class="col-sm-8">
                            <textarea class="form-control" 
                                      name="terms_conditions" 
                                      rows="4">{{ old('terms_conditions', 'Payment Terms: 30 Days from invoice date\nDelivery: 7-10 Working Days\nValidity: 30 Days from quotation date') }}</textarea>
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
                                <h4><b>OMR <span id="subtotal">0.000</span></b></h4>
                            </th>
                        </tr>
                        <tr>
                            <th class="text-end" style="font-size: 17px;">Discount (%)</th>
                            <th class="text-end">
                                <div class="input-group input-group-sm justify-content-end">
                                    <input type="number" id="global_discount_percent" name="global_discount_percent" class="form-control text-end" value="5" min="0" step="0.01" style="max-width:120px;">
                                </div>
                            </th>
                        </tr>
                        <tr>
                            <th class="text-end" style="font-size: 17px;">Discount Amount:</th>
                            <th class="text-end">
                                <h6>OMR <span id="discount_amount">0.000</span></h6>
                            </th>
                        </tr>
                        <tr>
                            <th class="text-end" style="font-size: 17px;">Tax (%)</th>
                            <th class="text-end">
                                <div class="input-group input-group-sm justify-content-end">
                                    <input type="number" id="global_tax_percent" name="global_tax_percent" class="form-control text-end" value="5" min="0" step="0.01" style="max-width:120px;">
                                </div>
                            </th>
                        </tr>
                        <tr>
                            <th class="text-end" style="font-size: 17px;">Tax Amount:</th>
                            <th class="text-end">
                                <h6>OMR <span id="tax_amount">0.000</span></h6>
                            </th>
                        </tr>
                        <tr>
                            <th class="text-end" style="font-size: 17px;">Grand Total:</th>
                            <th class="text-end" style="font-size: 17px;">
                                <h4><b>OMR <span id="total_amt">0.000</span></b></h4>
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
                        <i class="fas fa-save me-2"></i>Save Quotation
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
let itemIndex = 1;
const itemsData = @json($items);
const prefillItems = @json($prefillItems ?? []);
const prefillCustomerId = @json($prefillCustomerId ?? null);

console.log('Items loaded:', itemsData.length, 'prefillItems:', prefillItems.length);

$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        theme: 'bootstrap-5',
        width: '100%'
    });
    
    // Item / Lot search functionality
    $('#item_search').on('keyup', function() {
        const searchTerm = $(this).val().trim();
        if (searchTerm.length < 1) {
            $('#search_results_dropdown').hide();
            return;
        }

        if ($('#search_by_lot').is(':checked')) {
            // AJAX search lots
            $.getJSON('{{ url('') }}/api/lots', { q: searchTerm })
            .done(function(lots) {
                showLotsResults(lots);
            }).fail(function() {
                console.error('Lot search failed');
            });
        } else {
            const term = searchTerm.toLowerCase();
            const filteredItems = itemsData.filter(item => {
                const itemName = (item.item_name || '').toLowerCase();
                const itemCode = (item.item_code || '').toLowerCase();
                const oemPartNo = (item.oem_part_no || '').toLowerCase();
                return itemName.includes(term) || itemCode.includes(term) || oemPartNo.includes(term);
            });
            showSearchResults(filteredItems);
        }
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
                addItemToTable(item, null);
            }
            dropdown.hide();
            $('#item_search').val('');
        });
    }

    function showLotsResults(lots) {
        let dropdown = $('#search_results_dropdown');
        if (dropdown.length === 0) {
            dropdown = $('<div id="search_results_dropdown" class="list-group position-absolute shadow" style="z-index: 1050; max-height: 400px; overflow-y: auto; width: calc(100% - 48px); top: 100%; left: 48px; margin-top: 5px; border: 1px solid #ddd;"></div>');
            $('#item_search').parent().append(dropdown);
        }
        dropdown.empty();
        if (!lots || lots.length === 0) {
            dropdown.append('<div class="list-group-item">No lots found</div>');
        } else {
            lots.forEach(lot => {
                const item = lot.item || {};
                const expiryBadge = lot.expired ? '<span class="badge bg-danger ms-2">Expired</span>' : (lot.expiring_soon ? '<span class="badge bg-warning text-dark ms-2">Expiring Soon</span>' : '');
                const html = `
                    <a href="#" class="list-group-item list-group-item-action" data-lot-id="${lot.id}">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>${item.item_name || '-'} </strong> ${expiryBadge}<br>
                                <small class="text-muted">${item.item_code || '-'}${item.oem_part_no ? ' | OEM: ' + item.oem_part_no : ''} | Lot: ${lot.lot_no} | Qty: ${parseFloat(lot.qty_available).toFixed(2)} ${lot.expiry_date ? '| Exp: ' + lot.expiry_date : ''}</small>
                            </div>
                            <div class="text-end">
                                <small>OMR ${parseFloat(lot.cost_price).toFixed(3)}</small>
                            </div>
                        </div>
                    </a>
                `;
                dropdown.append(html);
            });
        }
        dropdown.show();
        dropdown.find('.list-group-item-action').click(function(e) {
            e.preventDefault();
            const lotId = $(this).data('lot-id');
            const selected = lots.find(function(l){ return l.id == lotId; });
            if (selected) {
                addItemToTable(selected.item, selected);
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
    
    function addItemToTable(item, lot = null) {
        const qty = 1;
        // Determine unit price: if a lot is provided use lot cost + item profit margin, else use item sale_price
        let rate = parseFloat(item.sale_price) || 0;
        // Prefer the full item entry from `itemsData` when available (for unit, profit_margin, etc.)
        const fullItem = itemsData.find(i => i.id == (item.id || item.item_id)) || item;
        const unitVal = fullItem.unit || item.unit || '';
        if (lot && (lot.cost_price !== undefined && lot.cost_price !== null)) {
            const lotPrice = parseFloat(lot.cost_price) || 0;
            const profitMargin = parseFloat(fullItem.profit_margin) || 0; // percent
            rate = lotPrice + (lotPrice * profitMargin / 100);
        }
        const amount = qty * rate;
        const totalAmount = amount;
        
        let lotInfo = '';
        let lotHidden = '';
        if (lot) {
            lotInfo = `<br><span class="badge bg-info">Lot: ${lot.lot_no} | Qty: ${parseFloat(lot.qty_available).toFixed(2)}</span>`;
            lotHidden = `<input type="hidden" name="items[][lot_id]" value="${lot.id}">`;
        }

        const rowId = itemIndex;
        
            const rowHtml = `
            <tr id="row_${rowId}">
                <td>
                    <strong>${item.item_name}</strong><br>
                    <small class="text-muted">${item.item_code || 'N/A'} ${item.oem_part_no ? '| OEM: ' + item.oem_part_no : ''}</small>
                    ${lotInfo}
                    <input type="hidden" name="items[][item_id]" id="tr_item_id_${rowId}" value="${item.id}">
                    ${lotHidden}
                </td>
                <td class="align-middle text-center">
                    <span id="td_unit_${rowId}">${unitVal}</span>
                    <input type="hidden" name="items[][unit]" value="${unitVal}">
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm" 
                           id="td_qty_${rowId}" 
                           name="items[][qty]" 
                           value="${qty}" 
                           min="0.01" 
                           step="0.01"
                           onchange="update_row_total(${rowId})">
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm text-end" 
                           id="td_rate_${rowId}" 
                           name="items[][rate]" 
                           value="${rate.toFixed(3)}" 
                           min="0.01" 
                           step="0.001"
                           onchange="update_row_total(${rowId})">
                </td>
                <td class="text-end">
                    <strong id="td_total_${rowId}">${totalAmount.toFixed(3)}</strong>
                    <input type="hidden" id="td_row_total_input_${rowId}" name="items[][row_total]" value="${totalAmount.toFixed(3)}">
                    <input type="hidden" name="items[][description]" value="">
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

    window.update_row_total = function(i) {
        const qty = parseFloat($(`#td_qty_${i}`).val()) || 0;
        const rate = parseFloat($(`#td_rate_${i}`).val()) || 0;
        const rowTotal = qty * rate;
        $(`#td_total_${i}`).text(rowTotal.toFixed(3));
        $(`#td_row_total_input_${i}`).val(rowTotal.toFixed(3));
        final_total();
    };

    // Ensure calculate_tax is available for other views' shared calls
    window.calculate_tax = function(i) {
        // Recalculate the row total and overall totals
        const qty = parseFloat($(`#td_qty_${i}`).val()) || 0;
        const rate = parseFloat($(`#td_rate_${i}`).val()) || 0;
        const rowTotal = qty * rate;
        $(`#td_total_${i}`).text(rowTotal.toFixed(3));
        $(`#td_row_total_input_${i}`).val(rowTotal.toFixed(3));
        final_total();
    };

    function final_total() {
        const rowcount = parseInt($('#hidden_rowcount').val());
        let subtotal = 0;
        let totalQuantity = 0;

        for (let i = 1; i < rowcount; i++) {
            if (document.getElementById(`td_qty_${i}`)) {
                const qty = parseFloat($(`#td_qty_${i}`).val()) || 0;
                const rate = parseFloat($(`#td_rate_${i}`).val()) || 0;
                const itemTotal = qty * rate;
                subtotal += itemTotal;
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
    }
    // Recalculate totals when discount or tax percent changes
    $('#global_discount_percent, #global_tax_percent').on('input change', function() {
        final_total();
    });
    
    // Apply prefill items (if any)
    if (prefillCustomerId) {
        $('#customer_id').val(prefillCustomerId).trigger('change');
    }
    if (prefillItems && prefillItems.length) {
        // Delay slightly to ensure functions are ready
        setTimeout(function(){
            prefillItems.forEach(function(pi){
                // item may be nested object
                const itemObj = pi.item || itemsData.find(i => i.id == (pi.item.id || pi.item_id));
                if (!itemObj) return;
                addItemToTable(itemObj, pi.lot_id ? { id: pi.lot_id, item: itemObj, lot_no: pi.lot_no || '' } : null);
                // set quantity if provided
                const lastIndex = itemIndex - 1;
                if (pi.quantity) {
                    $(`#td_data_${lastIndex}_3`).val(pi.quantity).trigger('change');
                }
            });
        }, 100);
    }

    // Form validation and AJAX submit with feedback
    $('#quotationForm').submit(function(e) {
        e.preventDefault();
        if ($('#itemsTableBody tr').length === 0) {
            alert('Please add at least one item to the quotation.');
            return false;
        }

        const $form = $(this);
        const url = $form.attr('action');
        const method = ($form.attr('method') || 'POST').toUpperCase();
        // Disable save button to prevent double submit
        $('#saveBtn').prop('disabled', true).text('Saving...');

        // Ensure totals are up-to-date before collecting data
        final_total();

        // Build FormData explicitly to ensure `items` array is structured correctly
        const formData = new FormData();

        // Append non-item fields from the form
        $form.serializeArray().forEach(function(field) {
            if (!field.name.startsWith('items')) {
                formData.append(field.name, field.value);
            }
        });

        // Collect items from table rows and append as items[0][field], items[1][field], ...
        const rows = $('#itemsTableBody tr');
        rows.each(function(idx, tr) {
            const $tr = $(tr);
            const itemId = $tr.find('input[id^="tr_item_id_"]').val();
            const qty = $tr.find('input[id^="td_qty_"]').val() || 0;
            const rate = $tr.find('input[id^="td_rate_"]').val() || 0;
            const rowTotal = $tr.find('input[id^="td_row_total_input_"]').val() || 0;
            const lotInput = $tr.find('input[name="items[][lot_id]"]').val() || $tr.find('input[id^="td_lot_"]').val() || '';
            formData.append(`items[${idx}][item_id]`, itemId);
            formData.append(`items[${idx}][qty]`, qty);
            formData.append(`items[${idx}][rate]`, rate);
            formData.append(`items[${idx}][row_total]`, rowTotal);
            if (lotInput) formData.append(`items[${idx}][lot_id]`, lotInput);
        });

        // Debug: list FormData entries to console to ensure `items` inputs are present
        try {
            const fdEntries = [];
            for (const pair of formData.entries()) {
                fdEntries.push(`${pair[0]}=${pair[1]}`);
            }
            console.log('FormData entries before submit:', fdEntries);
            console.log('Serialized fallback:', $form.serialize());
        } catch (err) {
            console.warn('Failed to enumerate FormData entries', err);
        }

        $.ajax({
            url: url,
            method: method,
            data: formData,
            processData: false,
            contentType: false,
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function(data, textStatus, xhr) {
                window.location.href = '{{ route('quotations.index') }}';
            },
            error: function(xhr) {
                $('#saveBtn').prop('disabled', false).text('Save Quotation');
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    let message = 'Validation errors:\n';
                    Object.keys(errors).forEach(function(k){ message += errors[k].join('\n') + '\n'; });
                    alert(message);
                } else {
                    alert('Failed to save quotation. Check console for details.');
                    console.error(xhr.responseText || xhr);
                }
            }
        });
    });
});
</script>
@endpush