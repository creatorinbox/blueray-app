@extends('layouts.app')

@section('title', 'Create Damage Stock')

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fas fa-exclamation-triangle me-2"></i>Create Damage Stock</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('stock.report') }}">Inventory</a></li>
            <li class="breadcrumb-item active">Damage Stock</li>
        </ol>
    </nav>
</div>

<form method="POST" action="{{ route('inventory.damage.store') }}" id="damageForm">
    @csrf
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" id="item_search" class="form-control" placeholder="Search item name/code/unit/lot">
                    </div>
                    <div id="selectedPreview" class="small text-muted mt-1"></div>
                    <input type="hidden" id="selected_data" name="_selected_data" value="">
                </div>
                <div class="col-md-2">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" id="search_by_lot" checked>
                        <label class="form-check-label small" for="search_by_lot">Search by Lot</label>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <button type="button" class="btn btn-success" id="addSelected">Add Selected</button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="itemsTable">
                    <thead class="table-primary">
                        <tr>
                            <th>Item Name</th>
                            <th>Unit</th>
                            <th style="width:150px">Qty</th>
                            <th style="width:50px">Action</th>
                        </tr>
                    </thead>
                    <tbody id="itemsTableBody"></tbody>
                </table>
            </div>
            <div class="text-muted" id="noItemsMsg">Select items to mark as damaged.</div>
        </div>
        <div class="card-footer text-end">
            <button type="submit" class="btn btn-danger">Submit Damage Stock</button>
        </div>
    </div>
</form>

@endsection

@push('scripts')
<script>
const itemsData = @json($items);
let selected = null;

$(document).ready(function(){
    $('#item_search').on('keyup', function(){
        const q = $(this).val().trim();
        $('#search_results_dropdown').remove();
        if (q.length < 1) return;

        if ($('#search_by_lot').is(':checked')) {
            // Lot-based search via API
            $.getJSON('{{ url('') }}/api/lots', { q: q })
            .done(function(lots){
                const dropdown = $('<div id="search_results_dropdown" class="list-group position-absolute shadow" style="z-index:1050; max-height:300px; overflow-y:auto; width:calc(100% - 48px);"></div>');
                $('#item_search').parent().append(dropdown);
                if (!lots || lots.length === 0) {
                    dropdown.append('<div class="list-group-item">No lots found</div>');
                    return;
                }
                lots.forEach(lot => {
                    const item = lot.item || {};
                    const expiry = lot.expiry_date ? (' | Exp: ' + lot.expiry_date) : '';
                    const html = `<a href="#" class="list-group-item list-group-item-action"
                        data-lot-id="${lot.id}" data-item-id="${item.id}"
                        data-item-name="${(item.item_name||'').replace(/"/g,'&quot;')}"
                        data-unit="${item.unit||''}"
                        data-lot-no="${lot.lot_no||''}"
                        data-qty-available="${parseFloat(lot.qty_available||0).toFixed(2)}">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>${item.item_name || '-'} </strong><br>
                                <small class="text-muted">${item.item_code || '-'} | ${item.unit || ''} | Lot: ${lot.lot_no} ${expiry}</small>
                            </div>
                            <div class="text-end">
                                <small>Avail: ${parseFloat(lot.qty_available || 0).toFixed(2)}</small>
                            </div>
                        </div>
                    </a>`;
                    dropdown.append($(html));
                });
            }).fail(function(){
                console.error('Lot search failed');
            });
        } else {
            const term = q.toLowerCase();
            const matches = itemsData.filter(i => (i.item_name||'').toLowerCase().includes(term) || (i.item_code||'').toLowerCase().includes(term) || (i.unit||'').toLowerCase().includes(term));
            const dropdown = $('<div id="search_results_dropdown" class="list-group position-absolute shadow" style="z-index:1050; max-height:300px; overflow-y:auto; width:calc(100% - 48px);"></div>');
            $('#item_search').parent().append(dropdown);
            if (matches.length === 0) dropdown.append('<div class="list-group-item">No items</div>');
            matches.forEach(it=>{
                const html = `<a href="#" class="list-group-item list-group-item-action" data-id="${it.id}" data-item-name="${(it.item_name||'').replace(/"/g,'&quot;')}" data-unit="${it.unit||''}" data-current-stock="${parseFloat(it.current_stock||0).toFixed(2)}">
                    <strong>${it.item_name}</strong><br><small class="text-muted">${it.item_code||''} | ${it.unit||''} | Stock: ${parseFloat(it.current_stock||0).toFixed(2)}</small>
                </a>`;
                dropdown.append($(html));
            });
        }
    });

    // Delegated handler for selecting search results
    $(document).on('click', '#search_results_dropdown .list-group-item-action', function(e){
        e.preventDefault();
        const $el = $(this);
        const lotId = $el.data('lot-id');
        const itemId = $el.data('item-id') || $el.data('id');
        const itemName = $el.attr('data-item-name') || $el.find('strong').text();
        const unit = $el.attr('data-unit') || '';
        const lotNo = $el.attr('data-lot-no') || null;
        const qtyAv = $el.attr('data-qty-available') || null;

        if (lotId) {
            selected = { lot: { id: lotId, lot_no: lotNo, qty_available: qtyAv }, item: { id: itemId, item_name: itemName, unit: unit } };
            $('#selectedPreview').html(`<span class="badge bg-info">Selected: ${itemName} (Lot: ${lotNo})</span>`);
        } else {
            selected = { item: { id: itemId, item_name: itemName, unit: unit } };
            $('#selectedPreview').html(`<span class="badge bg-info">Selected: ${itemName}</span>`);
        }
        // persist selection JSON so Add Selected can read it reliably
        try { $('#selected_data').val(JSON.stringify(selected)); } catch(e){ console.warn('persist selected failed', e); }
        $('#search_results_dropdown').remove();
        $('#item_search').val('');
    });

        function refreshItemIndices(){
            $('#itemsTableBody tr').each(function(i){
                $(this).find('input.input-item-id').attr('name', `items[${i}][item_id]`);
                $(this).find('input.input-lot-id').attr('name', `items[${i}][lot_id]`);
                $(this).find('input.input-unit').attr('name', `items[${i}][unit]`);
                $(this).find('input.input-qty').attr('name', `items[${i}][qty]`);
            });
        }

        $('#addSelected').on('click', function(){
        // Try to read selection from in-memory or hidden persisted JSON
        try {
            if (!selected || !selected.item) {
                const json = $('#selected_data').val();
                if (json) {
                    selected = JSON.parse(json);
                }
            }
        } catch (e) { console.warn('Failed to parse selected_data', e); }
        console.log('AddSelected clicked, selected=', selected, 'selected_data=', $('#selected_data').val());
        if (!selected || !selected.item) { alert('Select an item or lot first'); return; }
        // If selected is a lot object
            if (selected.lot) {
            const lot = selected.lot;
            const item = selected.item || {};
            const existing = $(`#itemsTableBody tr[data-lot='${lot.id}']`);
            if (existing.length) {
                const qtyEl = existing.find('input.qty');
                qtyEl.val((parseFloat(qtyEl.val()||0)+1).toFixed(2));
            } else {
                console.log('Creating lot row for', item, lot);
                    const tr = $('<tr>').attr('data-lot', lot.id).attr('data-id', item.id);
                    const inputItemId = $('<input>').attr({type:'hidden', value:item.id}).addClass('input-item-id');
                    const inputLotId = $('<input>').attr({type:'hidden', value:lot.id}).addClass('input-lot-id');
                    const td1 = $('<td>').text(item.item_name).append(inputItemId).append(inputLotId);
                    const inputUnit = $('<input>').attr({type:'hidden', value:item.unit||''}).addClass('input-unit');
                    const td2 = $('<td>').addClass('text-center').text(item.unit||'').append(inputUnit);
                    const qtyInput = $('<input>').attr({type:'number', step:'0.01', min:'0.01', value:1}).addClass('form-control qty input-qty');
                    const td3 = $('<td>').append(qtyInput);
                    const removeBtn = $('<button>').attr({type:'button'}).addClass('btn btn-sm btn-danger remove').text('x');
                    const td4 = $('<td>').addClass('text-center').append(removeBtn);
                    removeBtn.on('click', function(){ tr.remove(); refreshItemIndices(); if ($('#itemsTableBody tr').length === 0) $('#noItemsMsg').show(); });
                    tr.append(td1, td2, td3, td4);
                    $('#itemsTableBody').append(tr);
                    refreshItemIndices();
                    $('#noItemsMsg').hide();
                    console.log('Appended lot row html:', tr.prop('outerHTML'));
            }
        } else {
                // support both shapes: selected = { item: {...} } or selected = {...}
                const it = (selected && selected.item) ? selected.item : selected;
                const existing = $(`#itemsTableBody tr[data-id='${it.id}']`);
                if (existing.length) {
                    const qtyEl = existing.find('input.input-qty');
                    qtyEl.val((parseFloat(qtyEl.val()||0)+1).toFixed(2));
                } else {
                    console.log('Creating item row for', it);
                    const tr = $('<tr>').attr('data-id', it.id);
                    const inputItemId = $('<input>').attr({type:'hidden', value:it.id}).addClass('input-item-id');
                    const td1 = $('<td>').text(it.item_name).append(inputItemId);
                    const inputUnit = $('<input>').attr({type:'hidden', value:it.unit||''}).addClass('input-unit');
                    const td2 = $('<td>').addClass('text-center').text(it.unit||'').append(inputUnit);
                    const qtyInput = $('<input>').attr({type:'number', step:'0.01', min:'0.01', value:1}).addClass('form-control qty input-qty');
                    const td3 = $('<td>').append(qtyInput);
                    const removeBtn = $('<button>').attr({type:'button'}).addClass('btn btn-sm btn-danger remove').text('x');
                    const td4 = $('<td>').addClass('text-center').append(removeBtn);
                    removeBtn.on('click', function(){ tr.remove(); refreshItemIndices(); if ($('#itemsTableBody tr').length === 0) $('#noItemsMsg').show(); });
                    tr.append(td1, td2, td3, td4);
                    $('#itemsTableBody').append(tr);
                    refreshItemIndices();
                    $('#noItemsMsg').hide();
                    console.log('Appended item row html:', tr.prop('outerHTML'));
                }
        }
        selected = null;
    });

    $('#damageForm').submit(function(e){
        if ($('#itemsTableBody tr').length === 0) { e.preventDefault(); alert('Add at least one item'); }
    });
});
</script>
@endpush
