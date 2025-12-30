@extends('layouts.app')

@section('title', 'Create New Job Card')

@section('content')
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-plus me-2"></i>Create New Job Card
    </h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('job-cards.index') }}">Job Cards</a></li>
            <li class="breadcrumb-item active">Create New Job Card</li>
        </ol>
    </nav>
</div>

<form action="{{ route('job-cards.store') }}" method="POST">
    @csrf
    
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-info-circle me-2"></i>Job Card Information
            </h5>
        </div>
        <div class="card-body">
            <!-- Row 1: Customer and Date -->
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Customer <span class="text-danger">*</span></label>
                <div class="col-sm-4">
                    <div class="input-group">
                        <select class="form-select @error('customer_id') is-invalid @enderror" 
                                    name="customer_id" 
                                    required>
                                <option value="">Select Customer</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ (old('customer_id', $prefill['customer_id'] ?? null) == $customer->id) ? 'selected' : '' }}>
                                        {{ $customer->customer_name }}
                                    </option>
                                @endforeach
                            </select>
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                    </div>
                    @error('customer_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <label class="col-sm-2 col-form-label">Job Card Date <span class="text-danger">*</span></label>
                <div class="col-sm-4">
                          <input type="date" 
                              class="form-control @error('scheduled_date') is-invalid @enderror" 
                              name="scheduled_date" 
                              value="{{ old('scheduled_date', $prefill['scheduled_date'] ?? date('Y-m-d')) }}"
                              required>
                    @error('scheduled_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Row 2: Job Card No and Invoice No -->
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Job Card No</label>
                <div class="col-sm-4">
                    <input type="text" 
                           class="form-control" 
                           value="Auto Generated" 
                           readonly>
                </div>
                
                <label class="col-sm-2 col-form-label">Invoice No</label>
                <div class="col-sm-4">
                          <input type="text" 
                              class="form-control @error('invoice_no') is-invalid @enderror" 
                              name="invoice_no" 
                              value="{{ old('invoice_no', $prefill['invoice_no'] ?? '') }}" 
                              placeholder="Enter invoice number">
                    @error('invoice_no')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Row 3: Model Number and Serial Number -->
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Model Number</label>
                <div class="col-sm-4">
                          <input type="text" 
                              class="form-control @error('model_no') is-invalid @enderror" 
                              name="model_no" 
                              value="{{ old('model_no', $prefill['model_no'] ?? '') }}" 
                              placeholder="Enter model number">
                    @error('model_no')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <label class="col-sm-2 col-form-label">Serial Number</label>
                <div class="col-sm-4">
                          <input type="text" 
                              class="form-control @error('serial_no') is-invalid @enderror" 
                              name="serial_no" 
                              value="{{ old('serial_no', $prefill['serial_no'] ?? '') }}" 
                              placeholder="Enter serial number">
                    @error('serial_no')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Row 4: Service Attend and Mobile -->
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Service Attend</label>
                <div class="col-sm-4">
                          <input type="text" 
                              class="form-control @error('service_attend') is-invalid @enderror" 
                              name="service_attend" 
                              value="{{ old('service_attend', $prefill['service_attend'] ?? '') }}" 
                              placeholder="Technician name">
                    @error('service_attend')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <label class="col-sm-2 col-form-label">Service Attend Mobile</label>
                <div class="col-sm-4">
                          <input type="text" 
                              class="form-control @error('service_attend_mobile') is-invalid @enderror" 
                              name="service_attend_mobile" 
                              value="{{ old('service_attend_mobile', $prefill['service_attend_mobile'] ?? '') }}" 
                              placeholder="Mobile number">
                    @error('service_attend_mobile')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Row 5: Loading HR and Service Duration -->
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Loading HR</label>
                <div class="col-sm-4">
                          <input type="text" 
                              class="form-control @error('loading_hr') is-invalid @enderror" 
                              name="loading_hr" 
                              value="{{ old('loading_hr', $prefill['loading_hr'] ?? '') }}" 
                              placeholder="Loading hours">
                    @error('loading_hr')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <label class="col-sm-2 col-form-label">Service Duration</label>
                <div class="col-sm-2">
                    <input type="time" 
                           class="form-control @error('service_start_time') is-invalid @enderror" 
                           name="service_start_time" 
                           value="{{ old('service_start_time') }}" 
                           placeholder="Start time">
                    @error('service_start_time')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-sm-2">
                    <input type="time" 
                           class="form-control @error('service_end_time') is-invalid @enderror" 
                           name="service_end_time" 
                           value="{{ old('service_end_time') }}" 
                           placeholder="End time">
                    @error('service_end_time')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Row 6: Reference No and Job Card Status -->
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Reference No</label>
                <div class="col-sm-4">
                    <input type="text" 
                           class="form-control @error('reference_no') is-invalid @enderror" 
                           name="reference_no" 
                           value="{{ old('reference_no') }}" 
                           placeholder="Reference number">
                    @error('reference_no')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <label class="col-sm-2 col-form-label">Job Card Status <span class="text-danger">*</span></label>
                <div class="col-sm-4">
                    <select class="form-select @error('status') is-invalid @enderror" 
                            name="status" 
                            required>
                        <option value="Pending" {{ old('status') == 'Pending' ? 'selected' : '' }}>Not Started</option>
                        <option value="In Progress" {{ old('status') == 'In Progress' ? 'selected' : '' }}>On Hold</option>
                        <option value="Completed" {{ old('status') == 'Completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Row 7: Job Report Date and Job Report No -->
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Job Report Date</label>
                <div class="col-sm-4">
                    <input type="date" 
                           class="form-control @error('job_report_date') is-invalid @enderror" 
                           name="job_report_date" 
                           value="{{ old('job_report_date') }}">
                    @error('job_report_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <label class="col-sm-2 col-form-label">Job Report No</label>
                <div class="col-sm-4">
                    <input type="text" 
                           class="form-control @error('job_report_no') is-invalid @enderror" 
                           name="job_report_no" 
                           value="{{ old('job_report_no') }}" 
                           placeholder="Job report number">
                    @error('job_report_no')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Priority -->
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Priority <span class="text-danger">*</span></label>
                <div class="col-sm-4">
                    <select class="form-select @error('priority') is-invalid @enderror" 
                            name="priority" 
                            required>
                        <option value="Low" {{ old('priority') == 'Low' ? 'selected' : '' }}>Low</option>
                        <option value="Medium" {{ old('priority') == 'Medium' ? 'selected' : '' }}>Medium</option>
                        <option value="High" {{ old('priority') == 'High' ? 'selected' : '' }}>High</option>
                        <option value="Urgent" {{ old('priority') == 'Urgent' ? 'selected' : '' }}>Urgent</option>
                    </select>
                    @error('priority')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <label class="col-sm-2 col-form-label">Estimated Hours</label>
                <div class="col-sm-4">
                    <input type="number" 
                           class="form-control @error('estimated_hours') is-invalid @enderror" 
                           name="estimated_hours" 
                           value="{{ old('estimated_hours') }}" 
                           step="0.5" 
                           min="0"
                           placeholder="0.0">
                    @error('estimated_hours')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <!-- Job Description -->
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Job Description <span class="text-danger">*</span></label>
                <div class="col-sm-10">
                    <textarea class="form-control @error('job_description') is-invalid @enderror" 
                              name="job_description" 
                              rows="4" 
                              placeholder="Enter detailed job description"
                              required>{{ old('job_description', $prefill['job_description'] ?? '') }}</textarea>
                    @error('job_description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Service Remarks and Customer Remarks -->
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Service Remarks</label>
                <div class="col-sm-5">
                    <textarea class="form-control @error('service_remarks') is-invalid @enderror" 
                              name="service_remarks" 
                              rows="3" 
                              placeholder="Service remarks">{{ old('service_remarks', $prefill['service_remarks'] ?? '') }}</textarea>
                    @error('service_remarks')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <label class="col-sm-2 col-form-label">Customer Remarks</label>
                <div class="col-sm-3">
                    <textarea class="form-control @error('customer_remarks') is-invalid @enderror" 
                              name="customer_remarks" 
                              rows="3" 
                              placeholder="Customer remarks">{{ old('customer_remarks', $prefill['customer_remarks'] ?? '') }}</textarea>
                    @error('customer_remarks')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Additional Notes -->
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Additional Notes</label>
                <div class="col-sm-10">
                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                              name="notes" 
                              rows="3" 
                              placeholder="Additional notes or requirements">{{ old('notes', $prefill['notes'] ?? '') }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Parts / Items: search and table (like quotations) -->
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Add Parts / Items</label>
                <div class="col-sm-6">
                    <div class="input-group position-relative">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" placeholder="Search Item name/Barcode/Item code" id="item_search" autocomplete="off">
                    </div>
                    <div class="form-check form-check-inline mt-2">
                        <input class="form-check-input" type="checkbox" id="search_by_lot" checked>
                        <label class="form-check-label small" for="search_by_lot">Search by Lot</label>
                    </div>
                </div>
                <div class="col-sm-2">
                    <a href="#" class="btn btn-warning btn-sm" onclick="alert('Item management coming soon'); return false;">Add Item</a>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-sm-12">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="parts_table">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Code</th>
                                    <th style="width:120px">Quantity</th>
                                    <th style="width:80px">Action</th>
                                </tr>
                            </thead>
                            <tbody id="parts_tbody">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card-footer d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i>Create Job Card
            </button>
            <button type="submit" name="create_quote" value="1" class="btn btn-success">
                <i class="fas fa-file-invoice me-1"></i>Create &amp; Quote
            </button>
            <a href="{{ route('job-cards.index') }}" class="btn btn-secondary ms-auto">
                <i class="fas fa-times me-1"></i>Cancel
            </a>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    const itemsData = @json($items);
    const prefillItems = @json($prefillItems ?? []);
    (function(){
        let idx = 0;
        const partsTbody = document.getElementById('parts_tbody');
        const itemSearch = document.getElementById('item_search');
        const searchByLot = document.getElementById('search_by_lot');

        function showSearchResults(items) {
            console.debug('showSearchResults count=', Array.isArray(items) ? items.length : 0);
            console.debug('showSearchResults sample=', (Array.isArray(items) && items.length) ? items[0] : null);
            let dropdown = document.getElementById('search_results_dropdown');
            if (!dropdown) {
                dropdown = document.createElement('div');
                dropdown.id = 'search_results_dropdown';
                dropdown.className = 'list-group position-absolute shadow';
                dropdown.style.position = 'absolute';
                dropdown.style.zIndex = 2147483647; // force top-most
                dropdown.style.maxHeight = '300px';
                dropdown.style.overflowY = 'auto';
                dropdown.style.border = '1px solid #ddd';
                // explicit visual styles to avoid theme hiding the content
                dropdown.style.backgroundColor = '#ffffff';
                dropdown.style.color = '#000000';
                dropdown.style.minWidth = '220px';
                dropdown.style.padding = '4px';
                dropdown.style.borderRadius = '6px';
                dropdown.style.boxShadow = '0 6px 18px rgba(0,0,0,0.12)';
                document.body.appendChild(dropdown);
            }
            dropdown.innerHTML = '';
            // ensure dropdown is forced visible regardless of theme CSS
            try {
                dropdown.style.setProperty('display', 'block', 'important');
                dropdown.style.setProperty('visibility', 'visible', 'important');
                dropdown.style.setProperty('opacity', '1', 'important');
                dropdown.style.setProperty('pointer-events', 'auto', 'important');
                dropdown.style.setProperty('transform', 'none', 'important');
            } catch(e) { console.debug('setProperty failed', e); }
            if (!items || items.length === 0) {
                dropdown.innerHTML = '<div class="list-group-item">No items found</div>';
                console.debug('showSearchResults: no items to render', items);
            } else {
                items.forEach(function(item){
                    const itemCode = item.item_code || '-';
                    const unit = item.unit || '';
                    const stock = (item.total_stock !== undefined) ? parseFloat(item.total_stock).toFixed(2) : (item.current_stock !== undefined ? parseFloat(item.current_stock).toFixed(2) : '-');
                    const barcode = item.barcode || '';
                    const price = (item.sale_price !== undefined) ? 'OMR ' + parseFloat(item.sale_price).toFixed(3) : '';
                    const html = `<a href="#" class="list-group-item list-group-item-action" data-item-id="${item.id}">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>${item.item_name}</strong>
                                <br>
                                <small class="text-muted">${itemCode} ${barcode ? '| ' + barcode : ''} ${unit ? '| ' + unit : ''} ${price ? '| ' + price : ''}</small>
                            </div>
                            <div class="text-end"><small class="text-muted">Stock: ${stock}</small></div>
                        </div>
                    </a>`;
                    dropdown.insertAdjacentHTML('beforeend', html);
                    });
                console.debug('showSearchResults rendered html length=', dropdown.innerHTML.length, 'children=', dropdown.children.length);
            }

            // position dropdown under the input using page coordinates (account for scroll)
            const rect = itemSearch.getBoundingClientRect();
            const pageLeft = rect.left + window.scrollX;
            const pageTopBelow = rect.bottom + window.scrollY;
            dropdown.style.left = pageLeft + 'px';
            // place below by default
            dropdown.style.top = pageTopBelow + 'px';
            // if dropdown would go off the bottom of the viewport, place it above the input
            const ddHeight = dropdown.offsetHeight || 200;
            const pageBottom = window.scrollY + window.innerHeight;
            if ((pageTopBelow + ddHeight) > pageBottom) {
                const pageTopAbove = rect.top + window.scrollY - ddHeight;
                dropdown.style.top = (pageTopAbove > 0 ? pageTopAbove : pageTopBelow) + 'px';
                console.debug('dropdown placed above input due to viewport space');
            } else {
                console.debug('dropdown placed below input');
            }
            try { console.debug('dropdown.outerHTML=', dropdown.outerHTML.slice(0,1000)); } catch(e){}
            dropdown.style.width = rect.width + 'px';
            dropdown.style.display = 'block';
            dropdown.scrollTop = 0;
            console.debug('dropdown positioned', {left: dropdown.style.left, top: dropdown.style.top, width: dropdown.style.width, rect: rect});

            dropdown.querySelectorAll('.list-group-item-action').forEach(function(el){
                el.addEventListener('click', function(ev){
                    ev.preventDefault();
                    const id = this.getAttribute('data-item-id');
                    const itm = itemsData.find(i => i.id == id);
                    if (itm) addItemToTable(itm, null);
                    dropdown.style.display = 'none';
                    itemSearch.value = '';
                });
            });
        }

        function showLotsResults(lots) {
            console.debug('showLotsResults count=', Array.isArray(lots) ? lots.length : 0);
            console.debug('showLotsResults sample=', (Array.isArray(lots) && lots.length) ? lots[0] : null);
            let dropdown = document.getElementById('search_results_dropdown');
            if (!dropdown) {
                dropdown = document.createElement('div');
                dropdown.id = 'search_results_dropdown';
                dropdown.className = 'list-group position-absolute shadow';
                dropdown.style.position = 'absolute';
                dropdown.style.zIndex = 2147483647;
                dropdown.style.maxHeight = '300px';
                dropdown.style.overflowY = 'auto';
                dropdown.style.border = '1px solid #ddd';
                dropdown.style.backgroundColor = '#ffffff';
                dropdown.style.color = '#000000';
                dropdown.style.minWidth = '220px';
                dropdown.style.padding = '4px';
                dropdown.style.borderRadius = '6px';
                dropdown.style.boxShadow = '0 6px 18px rgba(0,0,0,0.12)';
                document.body.appendChild(dropdown);
            }
            dropdown.innerHTML = '';
            try {
                dropdown.style.setProperty('display', 'block', 'important');
                dropdown.style.setProperty('visibility', 'visible', 'important');
                dropdown.style.setProperty('opacity', '1', 'important');
                dropdown.style.setProperty('pointer-events', 'auto', 'important');
                dropdown.style.setProperty('transform', 'none', 'important');
            } catch(e) { console.debug('setProperty failed', e); }
            if (!lots || lots.length === 0) {
                dropdown.innerHTML = '<div class="list-group-item">No lots found</div>';
            } else {
                lots.forEach(function(lot){
                    // resolve lot's item and skip only if stock_type is explicitly non-stock
                        let lotItem = lot.item || null;
                        if (!lotItem && (lot.item_id !== undefined && Array.isArray(itemsData))) {
                            lotItem = itemsData.find(i => i.id == lot.item_id) || null;
                        }
                        let lotItemStockType = '';
                        try { lotItemStockType = (lotItem && lotItem.stock_type) ? lotItem.stock_type.toString().toLowerCase() : ''; } catch(e){ lotItemStockType = ''; }
                        if (lotItemStockType && lotItemStockType !== 'stock') return;
                    const item = lot.item || {};
                    const code = item.item_code || '-';
                    const unit = item.unit || '';
                    const qty = (lot.qty_available !== undefined) ? parseFloat(lot.qty_available).toFixed(2) : '-';
                    const lotNo = lot.lot_no || '';
                    const expiry = lot.expiry_date ? ` | Exp: ${lot.expiry_date}` : '';
                    const html = `<a href="#" class="list-group-item list-group-item-action" data-lot-id="${lot.id}">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>${item.item_name || '-'} </strong>
                                <br>
                                <small class="text-muted">${code} ${unit ? '| ' + unit : ''} | Lot: ${lotNo} | Qty: ${qty}${expiry}</small>
                            </div>
                            <div class="text-end"><small class="text-muted">Available: ${qty}</small></div>
                        </div>
                    </a>`;
                    dropdown.insertAdjacentHTML('beforeend', html);
                });
                // ensure each child link is visible and clickable
                Array.from(dropdown.querySelectorAll('.list-group-item-action')).forEach(function(a){
                    try {
                        a.style.display = 'block';
                        a.style.background = '#fff';
                        a.style.color = '#000';
                        a.style.padding = '6px 8px';
                        a.style.margin = '2px 0';
                        a.style.borderRadius = '4px';
                        a.style.textDecoration = 'none';
                        a.style.cursor = 'pointer';
                    } catch(e){}
                });
            }
            // position dropdown under the input using page coordinates (account for scroll)
            const rect = itemSearch.getBoundingClientRect();
            const pageLeft = rect.left + window.scrollX;
            const pageTopBelow = rect.bottom + window.scrollY;
            dropdown.style.left = pageLeft + 'px';
            dropdown.style.top = pageTopBelow + 'px';
            const ddHeight2 = dropdown.offsetHeight || 200;
            const pageBottom2 = window.scrollY + window.innerHeight;
            if ((pageTopBelow + ddHeight2) > pageBottom2) {
                const pageTopAbove = rect.top + window.scrollY - ddHeight2;
                dropdown.style.top = (pageTopAbove > 0 ? pageTopAbove : pageTopBelow) + 'px';
                console.debug('lots dropdown placed above input due to viewport space');
            } else {
                console.debug('lots dropdown placed below input');
            }
            try { console.debug('lots dropdown.outerHTML=', dropdown.outerHTML.slice(0,1000)); } catch(e){}
            // detect what element is on top of the dropdown position
            try {
                const testX = Math.round(rect.left + 5);
                const testY = Math.round(rect.bottom + 5);
                const topEl = document.elementFromPoint(testX - window.scrollX, testY - window.scrollY);
                console.debug('elementFromPoint at dropdown pos:', topEl && (topEl.id || topEl.className || topEl.tagName), topEl && topEl.outerHTML && topEl.outerHTML.slice(0,200));
            } catch(e) { console.debug('elementFromPoint check failed', e); }
            dropdown.style.width = rect.width + 'px';
            dropdown.style.display = 'block';
            dropdown.scrollTop = 0;
            console.debug('lots dropdown positioned', {left: dropdown.style.left, top: dropdown.style.top, width: dropdown.style.width, rect: rect});

            dropdown.querySelectorAll('.list-group-item-action').forEach(function(el){
                el.addEventListener('click', function(ev){
                    ev.preventDefault();
                    const lotId = this.getAttribute('data-lot-id');
                    // fetch lot by id with by_id=1
                    fetch(`{{ url('') }}/api/lots?by_id=1&id=${lotId}`)
                        .then(r => r.json())
                        .then(data => {
                            const lot = Array.isArray(data) ? data.find(x => x.id == lotId) : data;
                            if (lot) addItemToTable(lot.item, lot);
                        }).catch(err => { console.error('fetch lot by id failed', err); });
                    dropdown.style.display = 'none';
                    itemSearch.value = '';
                });
            });
        }

        if (itemSearch) {
            itemSearch.addEventListener('keyup', function(){
                try {
                    const term = this.value.trim();
                    if (term.length < 1) {
                        const dd = document.getElementById('search_results_dropdown'); if (dd) dd.style.display='none';
                        return;
                    }
                    if (searchByLot && searchByLot.checked) {
                        fetch(`{{ url('') }}/api/lots?q=${encodeURIComponent(term)}`)
                            .then(r => r.json())
                            .then(lots => { console.debug('lots fetch returned', Array.isArray(lots) ? lots.length : typeof lots, lots); showLotsResults(lots); })
                            .catch(err => { console.error('Lots fetch failed', err); });
                    } else {
                        const q = term.toLowerCase();
                        const list = Array.isArray(itemsData) ? itemsData : [];
                        const filtered = list.filter(i => {
                            try {
                                const st = (i.stock_type || '').toString().toLowerCase();
                                if (st !== 'stock') return false;
                                return (i.item_name||'').toLowerCase().includes(q) || (i.item_code||'').toLowerCase().includes(q) || (i.barcode||'').toLowerCase().includes(q);
                            } catch (innerErr) {
                                console.error('Error filtering item', innerErr, i);
                                return false;
                            }
                        });
                        console.debug('filtered items count', filtered.length, filtered.slice(0,5));
                        showSearchResults(filtered);
                    }
                } catch (err) {
                    console.error('itemSearch keyup handler error', err);
                }
            });
        } else {
            console.warn('Item search input not found: #item_search');
        }

        document.addEventListener('click', function(e){
            if (!e.target.closest('#item_search') && !e.target.closest('#search_results_dropdown')) {
                const dd = document.getElementById('search_results_dropdown'); if (dd) dd.style.display='none';
            }
        });

        function addItemToTable(item, lot){
            idx++;
            const tr = document.createElement('tr');
            tr.id = 'part_row_'+idx;
            const lotHidden = lot ? `<input type="hidden" name="items[${idx}][lot_id]" value="${lot.id}">` : `<input type="hidden" name="items[${idx}][lot_id]" value="">`;
            tr.innerHTML = `
                <td><strong>${item.item_name}</strong><br><small class="text-muted">${item.item_code||''}</small>
                    <input type="hidden" name="items[${idx}][item_id]" value="${item.id}">
                    ${lotHidden}
                </td>
                <td><small class="text-muted">${item.item_code||''}</small></td>
                <td><input type="number" class="form-control" step="0.01" min="0" name="items[${idx}][quantity]" value="1"></td>
                <td><button type="button" class="btn btn-sm btn-danger" data-row="${idx}">Remove</button></td>
            `;
            partsTbody.appendChild(tr);
        }

        partsTbody.addEventListener('click', function(e){
            if (e.target && e.target.matches('button[data-row]')){
                const rid = e.target.getAttribute('data-row');
                const row = document.getElementById('part_row_'+rid);
                if(row) row.remove();
            }
        });
        // If prefillItems provided, add them to the parts table on load
        if (Array.isArray(prefillItems) && prefillItems.length > 0) {
            prefillItems.forEach(function(p){
                const itm = itemsData.find(i => i.id == p.item_id);
                if (!itm) return;
                // construct a lightweight lot object if lot_id present
                const lotObj = p.lot_id ? { id: p.lot_id, lot_no: '', qty_available: p.quantity } : null;
                addItemToTable(itm, lotObj);
                // set the quantity input for the last added row
                const rows = partsTbody.querySelectorAll('tr');
                if (rows.length) {
                    const last = rows[rows.length-1];
                    const qtyInput = last.querySelector('input[name$="[quantity]"]');
                    if (qtyInput) qtyInput.value = p.quantity || 1;
                }
            });
        }
    })();
</script>
@endpush