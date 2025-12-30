@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Create Delivery Order</h1>
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <strong>There were validation errors:</strong>
            <ul class="mb-0">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if(!empty($prefillInvoice))
        <div class="mb-2">
            <span class="badge bg-primary">Prefilled from Invoice: {{ $prefillInvoice['invoice_no'] ?? $prefillInvoice['id'] }}</span>
        </div>
    @endif
    <form action="{{ route('delivery_orders.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="customer_id" class="form-label">Customer</label>
            <select name="customer_id" id="customer_id" class="form-select select2" required>
                <option value="">-Select-</option>
                @foreach($customers as $customer)
                    <option value="{{ $customer->id }}">{{ $customer->customer_name ?? $customer->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="delivery_date" class="form-label">Delivery Date</label>
            <input type="date" name="delivery_date" id="delivery_date" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="invoice_no" class="form-label">Invoice Number</label>
            <input type="text" name="invoice_no" id="invoice_no" class="form-control" value="{{ old('invoice_no', $prefillInvoice['invoice_no'] ?? '') }}" placeholder="Enter invoice number (optional)">
        </div>
        <div class="mb-3">
            <label class="form-label">Items</label>
            <div class="input-group mb-2 position-relative">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
                <input type="text" class="form-control" placeholder="Search Item name/Barcode/Item code" id="item_search" autocomplete="off">
                <div class="input-group-text">
                    <div class="form-check form-check-inline mb-0">
                        <input class="form-check-input" type="checkbox" id="search_by_lot" checked>
                        <label class="form-check-label small" for="search_by_lot">Search by Lot</label>
                    </div>
                </div>
            </div>
            <table class="table table-bordered" id="itemsTable">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Quantity</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="itemsTableBody">
                    <!-- dynamic rows -->
                </tbody>
            </table>
            <div id="noItemsMsg" class="text-muted">No items added yet. Search and select items to add.</div>
        </div>
        <div class="mb-3">
            <label for="remarks" class="form-label">Remarks</label>
            <textarea name="remarks" id="remarks" class="form-control"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Create</button>
    </form>
</div>
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    const itemsData = @json($items);
    const prefillInvoice = @json($prefillInvoice ?? null);
    const prefillItems = @json($prefillItems ?? []);
    let itemIndex = 0;
    let pendingPrefillRequests = 0;

    $(function() {
        // init select2 for customer
        if (typeof $().select2 === 'function') {
            $('.select2').select2({ theme: 'bootstrap-5', width: '100%' });
        }

        // If page was opened with invoice prefill, apply it (delayed to avoid timing issues)
        console.log('prefillInvoice / prefillItems on load ->', prefillInvoice, prefillItems);
        if (prefillInvoice) {
            // disable submit while prefill is applying
            $('form button[type="submit"]').prop('disabled', true).text('Applying prefill...');
            setTimeout(function() {
                if (prefillInvoice.customer_id) {
                    $('#customer_id').val(prefillInvoice.customer_id).trigger('change');
                }
                // Add items from invoice
                if (prefillItems && prefillItems.length) {
                    prefillItems.forEach(function(pi, piIndex) {
                        try {
                            console.log('prefill item', piIndex, pi);
                            // Ensure we always have an item object to show in the table
                            const safeItem = (pi && pi.item) ? pi.item : { id: (pi && pi.item_id) || 0, item_name: (pi && pi.item && pi.item.item_name) || 'Unknown item', item_code: (pi && pi.item && pi.item.item_code) || '' };

                            // If lot_id present, fetch lot details and then add (handle array/object returns)
                            if (pi.lot_id) {
                                pendingPrefillRequests++;
                                $.getJSON('{{ url('') }}/api/lots', { q: pi.lot_id, by_id: 1 }).always(function(){ pendingPrefillRequests--; checkPrefillDone(); }).done(function(res) {
                                    var all = Array.isArray(res) ? res : (res ? [res] : []);
                                    console.log('prefill lot fetch result', pi.lot_id, all);
                                    const selected = all.find(function(x){ return x && x.id == pi.lot_id; });
                                    if (selected) {
                                        selected.item = selected.item || safeItem;
                                        selected.item.sale_price = selected.item.sale_price || (pi.item?pi.item.sale_price:0);
                                        addItemToTable(selected.item, selected);
                                        const lastIdx = itemIndex - 1;
                                        $(`input[name="items[${lastIdx}][quantity]"]`).val(parseInt(pi.quantity) || 1);
                                    } else {
                                        console.warn('prefill: lot not found, adding item without lot', pi.lot_id);
                                        addItemToTable(safeItem, null);
                                        const lastIdx = itemIndex - 1;
                                        $(`input[name="items[${lastIdx}][quantity]"]`).val(parseInt(pi.quantity) || 1);
                                    }
                                }).fail(function(jq, status, err){
                                    console.error('prefill: failed to fetch lot', pi.lot_id, status, err);
                                    addItemToTable(safeItem, null);
                                const lastIdx = itemIndex - 1;
                                $(`input[name="items[${lastIdx}][quantity]"]`).val(parseInt(pi.quantity) || 1);
                                });
                            } else {
                                    addItemToTable(safeItem, null);
                                    const lastIdx = itemIndex - 1;
                                    $(`input[name="items[${lastIdx}][quantity]"]`).val(parseInt(pi.quantity) || 1);
                            }
                        } catch (e) {
                            console.error('Error while prefilling item', pi, e);
                            try { addItemToTable({ id: 0, item_name: 'Unknown', item_code: '' }, null); } catch(_){}
                        }
                    });
                }
                // in case there were no lot-based async requests, ensure submit enabled
                checkPrefillDone();
            }, 150);
        }

        function checkPrefillDone() {
            if (pendingPrefillRequests <= 0) {
                pendingPrefillRequests = 0;
                $('form button[type="submit"]').prop('disabled', false).text('Create');
                console.log('prefill done, submit enabled');
            } else {
                console.log('prefill pending requests:', pendingPrefillRequests);
            }
        }

        $('#item_search').on('keyup', function() {
            const searchTerm = $(this).val().trim();
            if (searchTerm.length < 1) { $('#search_results_dropdown').hide(); return; }

            if ($('#search_by_lot').is(':checked')) {
                $.getJSON('{{ url('') }}/api/lots', { q: searchTerm })
                .done(function(lots) { showLotsResults(lots); })
                .fail(function(){ console.error('Lot search failed'); });
            } else {
                const term = searchTerm.toLowerCase();
                const filtered = itemsData.filter(item => {
                    return (item.item_name||'').toLowerCase().includes(term) || (item.item_code||'').toLowerCase().includes(term) || (item.oem_part_no||'').toLowerCase().includes(term);
                });
                showSearchResults(filtered);
            }
        });

        function showSearchResults(items) {
            let dropdown = $('#search_results_dropdown');
            if (!dropdown.length) {
                dropdown = $('<div id="search_results_dropdown" class="list-group position-absolute shadow" style="z-index:1050; max-height:300px; overflow-y:auto; width:calc(100% - 48px); top:100%; left:48px; margin-top:5px; border:1px solid #ddd;"></div>');
                $('#item_search').parent().append(dropdown);
            }
            dropdown.empty();
            if (!items.length) dropdown.append('<div class="list-group-item">No items found</div>');
            else items.forEach(item => {
                const stock = item.total_stock || 0;
                const stockClass = stock > 0 ? 'bg-success' : 'bg-danger';
                const html = `<a href="#" class="list-group-item list-group-item-action" data-item-id="${item.id}"><div class="d-flex justify-content-between"><div><strong>${item.item_name}</strong><br><small class="text-muted">${item.item_code||''} | OMR ${parseFloat(item.sale_price||0).toFixed(3)}</small></div><div><span class="badge ${stockClass} text-white">${stock>0?'In':'Out'}</span></div></div></a>`;
                dropdown.append(html);
            });
            dropdown.show();
        }

        function showLotsResults(lots) {
            let dropdown = $('#search_results_dropdown');
            if (!dropdown.length) {
                dropdown = $('<div id="search_results_dropdown" class="list-group position-absolute shadow" style="z-index:1050; max-height:300px; overflow-y:auto; width:calc(100% - 48px); top:100%; left:48px; margin-top:5px; border:1px solid #ddd;"></div>');
                $('#item_search').parent().append(dropdown);
            }
            dropdown.empty();
            if (!lots || lots.length === 0) dropdown.append('<div class="list-group-item">No lots found</div>');
            else lots.forEach(lot=>{
                const item = lot.item||{};
                const expiryBadge = lot.expired?'<span class="badge bg-danger ms-2">Expired</span>':(lot.expiring_soon?'<span class="badge bg-warning text-dark ms-2">Expiring Soon</span>':'');
                const html = `<a href="#" class="list-group-item list-group-item-action" data-lot-id="${lot.id}"><div class="d-flex justify-content-between"><div><strong>${item.item_name||'-'}</strong> ${expiryBadge}<br><small class="text-muted">${item.item_code||'-'} | Lot: ${lot.lot_no} | Qty: ${parseFloat(lot.qty_available||0).toFixed(2)} ${lot.expiry_date? '| Exp: ' + lot.expiry_date : ''}</small></div><div class="text-end"><small>Cost: OMR ${parseFloat(lot.cost_price||0).toFixed(3)}</small></div></div></a>`;
                dropdown.append(html);
            });
            dropdown.show();
        }

        $(document).click(function(e){ if (!$(e.target).closest('#item_search, #search_results_dropdown').length) $('#search_results_dropdown').hide(); });

        // Single delegated click handler for search result items (prevents duplicate bindings)
        $(document).off('click', '#search_results_dropdown .list-group-item-action').on('click', '#search_results_dropdown .list-group-item-action', function(e){
            e.preventDefault();
            const itemId = $(this).data('item-id');
            const lotId = $(this).data('lot-id');
            console.log('search result clicked', { itemId, lotId });
            if (typeof itemId !== 'undefined') {
                const item = itemsData.find(i => i.id == itemId);
                console.log('found item in itemsData?', !!item, item);
                if (item) {
                    addItemToTable(item, null);
                } else {
                    // fallback: fetch item via API if not present
                    console.warn('Item not found in local itemsData, attempting fetch');
                    $.getJSON('{{ url('') }}/api/test-items', { q: itemId }).done(function(res){ console.log('test-items response', res); });
                }
                $('#search_results_dropdown').hide();
                $('#item_search').val('');
                return;
            }
            if (typeof lotId !== 'undefined') {
                console.log('fetching lot by id', lotId);
                $.getJSON('{{ url('') }}/api/lots', { q: lotId, by_id: 1 }).done(function(all){
                    console.log('lots api returned', all);
                    const selected = all.find(x => x.id == lotId);
                    if (selected) addItemToTable(selected.item, selected);
                    $('#search_results_dropdown').hide();
                    $('#item_search').val('');
                }).fail(function(){ console.error('Failed to fetch lot by id', lotId); });
            }
        });

        function addItemToTable(item, lot=null) {
            console.log('addItemToTable called', { item, lot });
            const idx = itemIndex++;
            const qty = 1;
            const lotHidden = lot ? `<input type="hidden" name="items[${idx}][lot_id]" value="${lot.id}">` : '';
            const row = `
                <tr id="row_${idx}">
                    <td>
                        <strong>${item.item_name}</strong><br>
                        <small class="text-muted">${item.item_code||''}</small>
                        ${lot?`<br><span class="badge bg-info">Lot: ${lot.lot_no} | Qty: ${parseFloat(lot.qty_available||0).toFixed(2)}</span>`:''}
                        <input type="hidden" name="items[${idx}][item_id]" value="${item.id}">
                        ${lotHidden}
                    </td>
                    <td><input type="number" inputmode="numeric" pattern="\d+" name="items[${idx}][quantity]" class="form-control form-control-sm" value="${qty}" min="1" step="1" oninput="this.value = this.value ? Math.max(1, Math.floor(this.value)) : ''"></td>
                    <td><button type="button" class="btn btn-sm btn-danger" onclick="$('#row_${idx}').remove(); if($('#itemsTableBody tr').length===0) $('#noItemsMsg').show();">Remove</button></td>
                </tr>
            `;
            $('#itemsTableBody').append(row);
            $('#noItemsMsg').hide();
        }

        // Prevent submitting empty items; show an alert and stop submit
        $('form').on('submit', function(e){
            if ($('#itemsTableBody tr').length === 0) {
                e.preventDefault();
                alert('No items added. Please add items before creating a delivery order.');
                return false;
            }
            if (pendingPrefillRequests > 0) {
                e.preventDefault();
                alert('Please wait while prefill is still applying.');
                return false;
            }
        });
    });
</script>
@endpush

@endsection
