@extends('layouts.app')

@section('title', 'Create Delivery Note')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Create Delivery Note</h3>
                    <div class="card-tools">
                        <a href="{{ route('deliveries.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                
                <form action="{{ route('deliveries.store') }}" method="POST" id="delivery-form">
                    @csrf
                    <div class="card-body">
                        <!-- Customer and Date Information -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="customer_id" class="form-label">Customer Name <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <select class="form-select select2" id="customer_id" name="customer_id" required>
                                        <option value="">Select Customer</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                                {{ $customer->customer_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#customerModal">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                                @error('customer_id')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="delivery_date" class="form-label">Delivery Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="delivery_date" name="delivery_date" 
                                       value="{{ old('delivery_date', date('Y-m-d')) }}" required>
                                @error('delivery_date')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="delivery_status" class="form-label">Status</label>
                                <input type="text" class="form-control" value="Delivery Note" readonly>
                            </div>
                            <div class="col-md-6">
                                <label for="subject" class="form-label">Subject</label>
                                <input type="text" class="form-control" id="subject" name="subject" 
                                       value="{{ old('subject') }}" placeholder="Enter subject">
                                @error('subject')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="reference_no" class="form-label">Reference No</label>
                                <input type="text" class="form-control" id="reference_no" name="reference_no" 
                                       value="{{ old('reference_no') }}" placeholder="Enter reference number">
                                @error('reference_no')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Items Section -->
                        <div class="card">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h5>Items</h5>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group mb-2">
                                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                                            <input type="text" class="form-control" id="item_search" 
                                                   placeholder="Search Item name/Barcode/Item code" autocomplete="off">
                                            <button type="button" class="btn btn-warning" id="add_item_btn">
                                                <i class="fas fa-plus"></i> Add Item
                                            </button>
                                            <button type="button" class="btn btn-info" id="test_search_btn">
                                                Test
                                            </button>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="items_table">
                                        <thead class="table-primary">
                                            <tr>
                                                <th width="25%">Item Name</th>
                                                <th width="10%">Quantity</th>
                                                <th width="10%">Unit Price</th>
                                                <th width="10%">Discount ({{ config('app.currency', 'LKR') }})</th>
                                                <th width="10%">Tax Amount</th>
                                                <th width="8%">Tax %</th>
                                                <th width="12%">Total Amount</th>
                                                <th width="10%">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Items will be added dynamically -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Summary Section -->
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Total Quantity:</label>
                                    <span class="h5 text-success ms-2" id="total_quantity">0</span>
                                </div>
                                <div class="form-group mt-3">
                                    <label for="delivery_notes" class="form-label">Notes</label>
                                    <textarea class="form-control" id="delivery_notes" name="delivery_notes" rows="4" 
                                              placeholder="Enter delivery notes">{{ old('delivery_notes') }}</textarea>
                                    @error('delivery_notes')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <table class="table">
                                    <tr>
                                        <th>Subtotal:</th>
                                        <td class="text-end">
                                            <h5 id="subtotal_amount">0.000</h5>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Total Discount:</th>
                                        <td class="text-end">
                                            <h5 id="discount_amount">0.000</h5>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Total Tax:</th>
                                        <td class="text-end">
                                            <h5 id="tax_amount">0.000</h5>
                                        </td>
                                    </tr>
                                    <tr class="table-active">
                                        <th>Grand Total:</th>
                                        <td class="text-end">
                                            <h4 id="grand_total">0.000</h4>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer text-center">
                        <button type="submit" class="btn btn-primary btn-lg me-3" id="save_delivery">
                            <i class="fas fa-save"></i> Save Delivery Note
                        </button>
                        <a href="{{ route('deliveries.index') }}" class="btn btn-secondary btn-lg">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Item Details Modal -->
<div class="modal fade" id="itemModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Item Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="item_form">
                    <input type="hidden" id="modal_row_id">
                    <div class="mb-3">
                        <label class="form-label">Item Name</label>
                        <span id="modal_item_name" class="form-control-plaintext"></span>
                    </div>
                    <div class="mb-3">
                        <label for="modal_description" class="form-label">Description</label>
                        <textarea class="form-control" id="modal_description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="modal_serial_number" class="form-label">Serial Number</label>
                        <input type="text" class="form-control" id="modal_serial_number">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="updateItemDetails()">Update</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
// Check if jQuery is loaded
if (typeof $ === 'undefined') {
    console.error('jQuery is not loaded!');
    alert('jQuery is not loaded. Please refresh the page.');
} else {
    console.log('jQuery loaded successfully');
}

// Load items data for client-side search
const itemsData = @json($items);
console.log('Items loaded:', itemsData.length);

$(document).ready(function() {
    console.log('Delivery note create page loaded');
    
    // Initialize Select2
    $('.select2').select2({
        theme: 'bootstrap-5'
    });

    let rowCount = 0;

    // Test if item search input exists and add debugging
    console.log('Item search input exists:', $('#item_search').length > 0);
    console.log('Items data type:', typeof itemsData);
    console.log('Items data length:', itemsData.length);
    console.log('First few items:', itemsData.slice(0, 2));

    // Simple search test first
    $('#item_search').on('focus', function() {
        console.log('Search input focused');
    });

    $('#item_search').on('blur', function() {
        console.log('Search input blurred');
    });

    // Item search functionality - simplified for debugging
    $('#item_search').on('keyup', function(e) {
        console.log('===== SEARCH EVENT START =====');
        console.log('Event type:', e.type);
        console.log('Key pressed:', e.key);
        
        const searchTerm = $(this).val();
        console.log('Raw search term:', searchTerm);
        console.log('Lowercase search term:', searchTerm.toLowerCase());
        
        if (searchTerm.length < 1) {
            console.log('Search term too short, hiding dropdown');
            $('#search_results_dropdown').hide();
            return;
        }
        
        console.log('Starting search...');
        
        // Simple filter
        const filteredItems = itemsData.filter(item => {
            if (!item || !item.item_name) {
                return false;
            }
            const itemName = item.item_name.toLowerCase();
            const matches = itemName.includes(searchTerm.toLowerCase());
            if (matches) {
                console.log('Found match:', item.item_name);
            }
            return matches;
        });
        
        console.log('Total filtered items:', filteredItems.length);
        console.log('Filtered items:', filteredItems.map(i => i.item_name));
        
        // Show dropdown with results
        showSearchResults(filteredItems);
        console.log('===== SEARCH EVENT END =====');
    });

    function showSearchResults(items) {
        console.log('showSearchResults called with items:', items.length);
        
        try {
            let dropdown = $('#search_results_dropdown');
            
            if (dropdown.length === 0) {
                dropdown = $('<div id="search_results_dropdown" class="list-group position-absolute shadow" style="z-index: 1050; max-height: 400px; overflow-y: auto; width: calc(100% - 48px); top: 100%; left: 48px; margin-top: 5px; border: 1px solid #ddd;"></div>');
                $('#item_search').parent().append(dropdown);
                console.log('Created dropdown');
            }
            
            dropdown.empty();
            
            if (items.length === 0) {
                dropdown.append('<div class="list-group-item">No items found</div>');
                console.log('No items to show');
            } else {
                console.log('Adding items to dropdown:', items.length);
                items.forEach((item, index) => {
                    console.log('Processing item', index, item.item_name);
                    const stockText = 'Available';
                    const itemCode = item.item_code || 'N/A';
                    
                    const itemHtml = `
                        <a href="#" class="list-group-item list-group-item-action" data-item-id="${item.id}">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>${item.item_name}</strong><br>
                                    <small class="text-muted">${itemCode} | ${item.unit || 'PC'} | LKR ${parseFloat(item.sale_price || 0).toFixed(3)}</small>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-success text-white">${stockText}</span>
                                </div>
                            </div>
                        </a>
                    `;
                    dropdown.append(itemHtml);
                });
            }
            
            dropdown.show();
            console.log('Dropdown shown');
            
            // Click handler for search results
            dropdown.find('.list-group-item-action').click(function(e) {
                e.preventDefault();
                console.log('Item clicked');
                const itemId = $(this).data('item-id');
                const item = itemsData.find(i => i.id == itemId);
                if (item) {
                    console.log('Adding item to table:', item.item_name);
                    addItemRow(item);
                }
                dropdown.hide();
                $('#item_search').val('');
            });
        } catch (error) {
            console.error('Error in showSearchResults:', error);
        }
    }

    // Make showSearchResults global for testing
    window.showSearchResults = showSearchResults;

    // Hide dropdown when clicking outside
    $(document).click(function(e) {
        if (!$(e.target).closest('#item_search').length) {
            $('#search_results_dropdown').hide();
        }
    });

    // Add item button
    $('#add_item_btn').click(function() {
        console.log('Add item button clicked');
        addEmptyRow();
    });

    // Test search button
    $('#test_search_btn').click(function() {
        console.log('===== MANUAL TEST START =====');
        console.log('Items data available:', itemsData.length);
        
        // Set search value and trigger event
        $('#item_search').val('hp').trigger('keyup');
        
        console.log('Manual test completed - check for search results above');
        console.log('===== MANUAL TEST END =====');
    });

    // Alternative event binding for search
    $(document).on('keyup input', '#item_search', function(e) {
        console.log('Alternative search event triggered');
        const searchTerm = $(this).val().toLowerCase();
        console.log('Search term via alternative binding:', searchTerm);
        
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
        
        console.log('Filtered items via alternative:', filteredItems.length);
        showSearchResults(filteredItems);
    });

    // Manual search test - call this from browser console
    window.manualSearch = function(term) {
        console.log('Manual search for:', term);
        const results = itemsData.filter(item => {
            return item.item_name.toLowerCase().includes(term.toLowerCase());
        });
        console.log('Manual search results:', results);
        showSearchResults(results);
    };

    function showNoResultsMessage() {
        // Create a temporary message
        const message = $('<div class="alert alert-info alert-dismissible fade show mt-2" role="alert">' +
            'No items found. <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>');
        $('#item_search').parent().append(message);
        
        // Auto-dismiss after 3 seconds
        setTimeout(() => {
            message.alert('close');
        }, 3000);
    }

    function addEmptyRow() {
        rowCount++;
        const row = `
            <tr id="row_${rowCount}">
                <td>
                    <input type="text" class="form-control item-name" name="items[${rowCount}][item_name]" required>
                    <input type="hidden" name="items[${rowCount}][item_id]" class="item-id">
                </td>
                <td>
                    <input type="number" step="0.001" class="form-control quantity" name="items[${rowCount}][quantity]" 
                           onchange="calculateRowTotal(${rowCount})" required>
                </td>
                <td>
                    <input type="number" step="0.001" class="form-control unit-price" name="items[${rowCount}][unit_price]" 
                           onchange="calculateRowTotal(${rowCount})" required>
                </td>
                <td>
                    <input type="number" step="0.001" class="form-control discount" name="items[${rowCount}][discount_amount]" 
                           onchange="calculateRowTotal(${rowCount})" value="0">
                </td>
                <td>
                    <input type="number" step="0.001" class="form-control tax-amount" name="items[${rowCount}][tax_amount]" readonly>
                </td>
                <td>
                    <input type="number" step="0.01" class="form-control tax-rate" name="items[${rowCount}][tax_rate]" 
                           onchange="calculateRowTotal(${rowCount})" value="0">
                </td>
                <td>
                    <input type="number" step="0.001" class="form-control total-amount" name="items[${rowCount}][total_amount]" readonly>
                </td>
                <td>
                    <button type="button" class="btn btn-info btn-sm me-1" onclick="showItemModal(${rowCount})">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(${rowCount})">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>`;
        $('#items_table tbody').append(row);
    }

    function addItemRow(item) {
        rowCount++;
        const row = `
            <tr id="row_${rowCount}">
                <td>
                    <input type="text" class="form-control item-name" name="items[${rowCount}][item_name]" value="${item.item_name}" required>
                    <input type="hidden" name="items[${rowCount}][item_id]" class="item-id" value="${item.id}">
                </td>
                <td>
                    <input type="number" step="0.001" class="form-control quantity" name="items[${rowCount}][quantity]" 
                           onchange="calculateRowTotal(${rowCount})" value="1" required>
                </td>
                <td>
                    <input type="number" step="0.001" class="form-control unit-price" name="items[${rowCount}][unit_price]" 
                           onchange="calculateRowTotal(${rowCount})" value="${item.sale_price || 0}" required>
                </td>
                <td>
                    <input type="number" step="0.001" class="form-control discount" name="items[${rowCount}][discount_amount]" 
                           onchange="calculateRowTotal(${rowCount})" value="0">
                </td>
                <td>
                    <input type="number" step="0.001" class="form-control tax-amount" name="items[${rowCount}][tax_amount]" readonly>
                </td>
                <td>
                    <input type="number" step="0.01" class="form-control tax-rate" name="items[${rowCount}][tax_rate]" 
                           onchange="calculateRowTotal(${rowCount})" value="0">
                </td>
                <td>
                    <input type="number" step="0.001" class="form-control total-amount" name="items[${rowCount}][total_amount]" readonly>
                </td>
                <td>
                    <button type="button" class="btn btn-info btn-sm me-1" onclick="showItemModal(${rowCount})">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(${rowCount})">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>`;
        $('#items_table tbody').append(row);
        calculateRowTotal(rowCount);
    }

    // Make functions globally available
    window.addEmptyRow = addEmptyRow;
    window.addItemRow = addItemRow;
});

function calculateRowTotal(rowId) {
    const row = $(`#row_${rowId}`);
    const quantity = parseFloat(row.find('.quantity').val()) || 0;
    const unitPrice = parseFloat(row.find('.unit-price').val()) || 0;
    const discount = parseFloat(row.find('.discount').val()) || 0;
    const taxRate = parseFloat(row.find('.tax-rate').val()) || 0;

    const subtotal = quantity * unitPrice;
    const subtotalAfterDiscount = subtotal - discount;
    const taxAmount = (subtotalAfterDiscount * taxRate) / 100;
    const total = subtotalAfterDiscount + taxAmount;

    row.find('.tax-amount').val(taxAmount.toFixed(3));
    row.find('.total-amount').val(total.toFixed(3));

    calculateGrandTotal();
}

function calculateGrandTotal() {
    let totalQuantity = 0;
    let subtotal = 0;
    let totalDiscount = 0;
    let totalTax = 0;
    let grandTotal = 0;

    $('#items_table tbody tr').each(function() {
        const quantity = parseFloat($(this).find('.quantity').val()) || 0;
        const unitPrice = parseFloat($(this).find('.unit-price').val()) || 0;
        const discount = parseFloat($(this).find('.discount').val()) || 0;
        const taxAmount = parseFloat($(this).find('.tax-amount').val()) || 0;
        const total = parseFloat($(this).find('.total-amount').val()) || 0;

        totalQuantity += quantity;
        subtotal += (quantity * unitPrice);
        totalDiscount += discount;
        totalTax += taxAmount;
        grandTotal += total;
    });

    $('#total_quantity').text(totalQuantity.toFixed(3));
    $('#subtotal_amount').text(subtotal.toFixed(3));
    $('#discount_amount').text(totalDiscount.toFixed(3));
    $('#tax_amount').text(totalTax.toFixed(3));
    $('#grand_total').text(grandTotal.toFixed(3));
}

function removeRow(rowId) {
    $(`#row_${rowId}`).remove();
    calculateGrandTotal();
}

function showItemModal(rowId) {
    const row = $(`#row_${rowId}`);
    const itemName = row.find('.item-name').val();
    
    $('#modal_row_id').val(rowId);
    $('#modal_item_name').text(itemName);
    $('#modal_description').val($(`input[name="items[${rowId}][description]"]`).val() || '');
    $('#modal_serial_number').val($(`input[name="items[${rowId}][serial_number]"]`).val() || '');
    
    $('#itemModal').modal('show');
}

function updateItemDetails() {
    const rowId = $('#modal_row_id').val();
    const description = $('#modal_description').val();
    const serialNumber = $('#modal_serial_number').val();
    
    // Add hidden inputs for description and serial number if they don't exist
    if ($(`input[name="items[${rowId}][description]"]`).length === 0) {
        $(`#row_${rowId}`).append(`<input type="hidden" name="items[${rowId}][description]" value="${description}">`);
        $(`#row_${rowId}`).append(`<input type="hidden" name="items[${rowId}][serial_number]" value="${serialNumber}">`);
    } else {
        $(`input[name="items[${rowId}][description]"]`).val(description);
        $(`input[name="items[${rowId}][serial_number]"]`).val(serialNumber);
    }
    
    $('#itemModal').modal('hide');
}

// Form validation
$('#delivery-form').submit(function(e) {
    if ($('#items_table tbody tr').length === 0) {
        e.preventDefault();
        alert('Please add at least one item to the delivery note.');
        return false;
    }
});
</script>
@endsection