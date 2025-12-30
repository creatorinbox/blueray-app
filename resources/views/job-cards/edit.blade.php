@extends('layouts.app')

@section('title', 'Edit Job Card')

@section('content')
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-edit me-2"></i>Edit Job Card
    </h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('job-cards.index') }}">Job Cards</a></li>
            <li class="breadcrumb-item active">Edit {{ $jobCard->job_card_no }}</li>
        </ol>
    </nav>
</div>

<form action="{{ route('job-cards.update', $jobCard) }}" method="POST">
    @csrf
    @method('PUT')
    
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Job Card Information</h5>
        </div>
        <div class="card-body">
            <!-- Row 1: Job Card No and Customer -->
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Job Card No</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" value="{{ $jobCard->job_card_no }}" readonly>
                </div>
                
                <label class="col-sm-2 col-form-label">Customer <span class="text-danger">*</span></label>
                <div class="col-sm-4">
                    <select class="form-select @error('customer_id') is-invalid @enderror" 
                            name="customer_id" 
                            required>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ old('customer_id', $jobCard->customer_id) == $customer->id ? 'selected' : '' }}>
                                {{ $customer->customer_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('customer_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Row 2: Invoice No and Job Card Date -->
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Invoice No</label>
                <div class="col-sm-4">
                    <input type="text" 
                           class="form-control @error('invoice_no') is-invalid @enderror" 
                           name="invoice_no" 
                           value="{{ old('invoice_no', $jobCard->invoice_no) }}" 
                           placeholder="Enter invoice number">
                    @error('invoice_no')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <label class="col-sm-2 col-form-label">Job Card Date</label>
                <div class="col-sm-4">
                    <input type="date" 
                           class="form-control @error('scheduled_date') is-invalid @enderror" 
                           name="scheduled_date" 
                           value="{{ old('scheduled_date', $jobCard->scheduled_date?->format('Y-m-d')) }}">
                    @error('scheduled_date')
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
                           value="{{ old('model_no', $jobCard->model_no) }}" 
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
                           value="{{ old('serial_no', $jobCard->serial_no) }}" 
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
                           value="{{ old('service_attend', $jobCard->service_attend) }}" 
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
                           value="{{ old('service_attend_mobile', $jobCard->service_attend_mobile) }}" 
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
                           value="{{ old('loading_hr', $jobCard->loading_hr) }}" 
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
                           value="{{ old('service_start_time', $jobCard->service_start_time) }}" 
                           placeholder="Start time">
                    @error('service_start_time')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-sm-2">
                    <input type="time" 
                           class="form-control @error('service_end_time') is-invalid @enderror" 
                           name="service_end_time" 
                           value="{{ old('service_end_time', $jobCard->service_end_time) }}" 
                           placeholder="End time">
                    @error('service_end_time')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Row 6: Reference No and Status -->
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Reference No</label>
                <div class="col-sm-4">
                    <input type="text" 
                           class="form-control @error('reference_no') is-invalid @enderror" 
                           name="reference_no" 
                           value="{{ old('reference_no', $jobCard->reference_no) }}" 
                           placeholder="Reference number">
                    @error('reference_no')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <label class="col-sm-2 col-form-label">Status <span class="text-danger">*</span></label>
                <div class="col-sm-4">
                    <select class="form-select @error('status') is-invalid @enderror" 
                            name="status" 
                            required>
                        <option value="Pending" {{ old('status', $jobCard->status) == 'Pending' ? 'selected' : '' }}>Not Started</option>
                        <option value="In Progress" {{ old('status', $jobCard->status) == 'In Progress' ? 'selected' : '' }}>On Hold</option>
                        <option value="Completed" {{ old('status', $jobCard->status) == 'Completed' ? 'selected' : '' }}>Completed</option>
                        <option value="Cancelled" {{ old('status', $jobCard->status) == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
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
                           value="{{ old('job_report_date', $jobCard->job_report_date?->format('Y-m-d')) }}">
                    @error('job_report_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <label class="col-sm-2 col-form-label">Job Report No</label>
                <div class="col-sm-4">
                    <input type="text" 
                           class="form-control @error('job_report_no') is-invalid @enderror" 
                           name="job_report_no" 
                           value="{{ old('job_report_no', $jobCard->job_report_no) }}" 
                           placeholder="Job report number">
                    @error('job_report_no')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Row 8: Priority and Estimated Hours -->
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Priority <span class="text-danger">*</span></label>
                <div class="col-sm-4">
                    <select class="form-select @error('priority') is-invalid @enderror" 
                            name="priority" 
                            required>
                        <option value="Low" {{ old('priority', $jobCard->priority) == 'Low' ? 'selected' : '' }}>Low</option>
                        <option value="Medium" {{ old('priority', $jobCard->priority) == 'Medium' ? 'selected' : '' }}>Medium</option>
                        <option value="High" {{ old('priority', $jobCard->priority) == 'High' ? 'selected' : '' }}>High</option>
                        <option value="Urgent" {{ old('priority', $jobCard->priority) == 'Urgent' ? 'selected' : '' }}>Urgent</option>
                    </select>
                    @error('priority')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <label class="col-sm-2 col-form-label">Estimated Hours</label>
                <div class="col-sm-2">
                    <input type="number" 
                           class="form-control @error('estimated_hours') is-invalid @enderror" 
                           name="estimated_hours" 
                           value="{{ old('estimated_hours', $jobCard->estimated_hours) }}" 
                           step="0.5" 
                           min="0">
                    @error('estimated_hours')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <label class="col-sm-1 col-form-label">Actual Hours</label>
                <div class="col-sm-1">
                    <input type="number" 
                           class="form-control @error('actual_hours') is-invalid @enderror" 
                           name="actual_hours" 
                           value="{{ old('actual_hours', $jobCard->actual_hours) }}" 
                           step="0.5" 
                           min="0">
                    @error('actual_hours')
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
                              required>{{ old('job_description', $jobCard->job_description) }}</textarea>
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
                              rows="3">{{ old('service_remarks', $jobCard->service_remarks) }}</textarea>
                    @error('service_remarks')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <label class="col-sm-2 col-form-label">Customer Remarks</label>
                <div class="col-sm-3">
                    <textarea class="form-control @error('customer_remarks') is-invalid @enderror" 
                              name="customer_remarks" 
                              rows="3">{{ old('customer_remarks', $jobCard->customer_remarks) }}</textarea>
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
                              rows="3">{{ old('notes', $jobCard->notes) }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <!-- Parts / Items: search and table (like create) -->
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Parts / Items</label>
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
        
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i>Update Job Card
            </button>
            <a href="{{ route('job-cards.index') }}" class="btn btn-secondary">
                <i class="fas fa-times me-1"></i>Cancel
            </a>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    const itemsData = @json($items ?? []);
    const prefillItems = @json($prefillItems ?? []);
    (function(){
        let idx = 0;
        const partsTbody = document.getElementById('parts_tbody');
        const itemSearch = document.getElementById('item_search');
        const searchByLot = document.getElementById('search_by_lot');

        function showSearchResults(items) {
            let dropdown = document.getElementById('search_results_dropdown');
            if (!dropdown) {
                dropdown = document.createElement('div');
                dropdown.id = 'search_results_dropdown';
                dropdown.className = 'list-group position-absolute shadow';
                dropdown.style.zIndex = 1050;
                dropdown.style.maxHeight = '300px';
                dropdown.style.overflowY = 'auto';
                dropdown.style.width = 'calc(100% - 48px)';
                dropdown.style.top = '100%';
                dropdown.style.left = '48px';
                dropdown.style.marginTop = '5px';
                dropdown.style.border = '1px solid #ddd';
                itemSearch.parentNode.appendChild(dropdown);
            }
            dropdown.innerHTML = '';
            if (!items || items.length === 0) {
                dropdown.innerHTML = '<div class="list-group-item">No items found</div>';
            } else {
                items.forEach(function(item){
                    const itemCode = item.item_code || '-';
                    const html = `<a href="#" class="list-group-item list-group-item-action" data-item-id="${item.id}">
                        <div class="d-flex justify-content-between align-items-center">
                            <div><strong>${item.item_name}</strong><br><small class="text-muted">${itemCode} | OMR ${parseFloat(item.sale_price).toFixed(3)}</small></div>
                        </div>
                    </a>`;
                    dropdown.insertAdjacentHTML('beforeend', html);
                });
            }
            dropdown.style.display = 'block';

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
            let dropdown = document.getElementById('search_results_dropdown');
            if (!dropdown) {
                dropdown = document.createElement('div');
                dropdown.id = 'search_results_dropdown';
                dropdown.className = 'list-group position-absolute shadow';
                dropdown.style.zIndex = 1050;
                dropdown.style.maxHeight = '300px';
                dropdown.style.overflowY = 'auto';
                dropdown.style.width = 'calc(100% - 48px)';
                dropdown.style.top = '100%';
                dropdown.style.left = '48px';
                dropdown.style.marginTop = '5px';
                dropdown.style.border = '1px solid #ddd';
                itemSearch.parentNode.appendChild(dropdown);
            }
            dropdown.innerHTML = '';
            if (!lots || lots.length === 0) {
                dropdown.innerHTML = '<div class="list-group-item">No lots found</div>';
            } else {
                lots.forEach(function(lot){
                    const item = lot.item || {};
                    const html = `<a href="#" class="list-group-item list-group-item-action" data-lot-id="${lot.id}">
                        <div class="d-flex justify-content-between align-items-center">
                            <div><strong>${item.item_name || '-'} </strong><br><small class="text-muted">${item.item_code || '-'} | Lot: ${lot.lot_no} | Qty: ${parseFloat(lot.qty_available).toFixed(2)}</small></div>
                        </div>
                    </a>`;
                    dropdown.insertAdjacentHTML('beforeend', html);
                });
            }
            dropdown.style.display = 'block';

            dropdown.querySelectorAll('.list-group-item-action').forEach(function(el){
                el.addEventListener('click', function(ev){
                    ev.preventDefault();
                    const lotId = this.getAttribute('data-lot-id');
                    fetch(`{{ url('') }}/api/lots?by_id=1&id=${lotId}`)
                        .then(r => r.json())
                        .then(data => {
                            const lot = Array.isArray(data) ? data.find(x => x.id == lotId) : data;
                            if (lot) addItemToTable(lot.item, lot);
                        }).catch(()=>{});
                    dropdown.style.display = 'none';
                    itemSearch.value = '';
                });
            });
        }

        itemSearch.addEventListener('keyup', function(){
            const term = this.value.trim();
            if (term.length < 1) {
                const dd = document.getElementById('search_results_dropdown'); if (dd) dd.style.display='none';
                return;
            }
            if (searchByLot && searchByLot.checked) {
                fetch(`{{ url('') }}/api/lots?q=${encodeURIComponent(term)}`)
                    .then(r => r.json())
                    .then(lots => showLotsResults(lots))
                    .catch(()=>{});
            } else {
                const q = term.toLowerCase();
                const filtered = itemsData.filter(i => (i.item_name||'').toLowerCase().includes(q) || (i.item_code||'').toLowerCase().includes(q));
                showSearchResults(filtered);
            }
        });

        document.addEventListener('click', function(e){
            if (!e.target.closest('#item_search')) {
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

        // load existing parts
        if (Array.isArray(prefillItems) && prefillItems.length > 0) {
            prefillItems.forEach(function(p){
                const itm = itemsData.find(i => i.id == p.item_id);
                if (!itm) return;
                const lotObj = p.lot_id ? { id: p.lot_id, lot_no: '', qty_available: p.quantity } : null;
                addItemToTable(itm, lotObj);
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