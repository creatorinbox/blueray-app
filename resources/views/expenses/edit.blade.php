@extends('layouts.app')

@section('title', 'Edit Expense')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
@endpush

@section('content')
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-edit me-2"></i>Edit Expense
    </h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('expenses.index') }}">Expenses</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol>
    </nav>
</div>

<form action="{{ route('expenses.update', $expense->id) }}" method="POST" id="expenseForm">
    @csrf
    @method('PUT')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Edit Expense Details</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Left Column -->
                        <div class="col-md-6">
                            <!-- Expense Date -->
                            <div class="form-group mb-3">
                                <label for="expense_date" class="form-label">Expense Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('expense_date') is-invalid @enderror" 
                                       id="expense_date" name="expense_date" 
                                       value="{{ old('expense_date', $expense->expense_date) }}" required>
                                @error('expense_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Category -->
                            <div class="form-group mb-3">
                                <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                                <select class="form-control select2 @error('category_id') is-invalid @enderror" 
                                        id="category_id" name="category_id" required>
                                    <option value="">-Select-</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" 
                                                {{ old('category_id', $expense->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->category_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Sub Category -->
                            <div class="form-group mb-3">
                                <label for="sub_category_id" class="form-label">Sub Category</label>
                                <select class="form-control select2" id="sub_category_id" name="sub_category_id">
                                    <option value="">-Select-</option>
                                </select>
                            </div>

                            <!-- Vehicle Number -->
                            <div class="form-group mb-3" id="vehicle_section">
                                <label for="vehicle_no" class="form-label">Vehicle Number</label>
                                <input type="text" class="form-control" id="vehicle_no" name="vehicle_no" 
                                       value="{{ old('vehicle_no', $expense->vehicle_no) }}" placeholder="Enter vehicle number">
                            </div>

                            <!-- Customer -->
                            <div class="form-group mb-3">
                                <label for="customer_id" class="form-label">Customer</label>
                                <select class="form-control select2" id="customer_id" name="customer_id">
                                    <option value="">Others</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" 
                                                {{ old('customer_id', $expense->customer_id) == $customer->id ? 'selected' : '' }}>
                                            {{ $customer->customer_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Expense For -->
                            <div class="form-group mb-3">
                                <label for="expense_for" class="form-label">Expense For <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('expense_for') is-invalid @enderror" 
                                       id="expense_for" name="expense_for" 
                                       value="{{ old('expense_for', $expense->expense_for) }}" 
                                       placeholder="Enter expense description" required>
                                @error('expense_for')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Amount -->
                            <div class="form-group mb-3">
                                <label for="expense_amount" class="form-label">Amount <span class="text-danger">*</span></label>
                                <input type="number" class="form-control only_currency @error('expense_amount') is-invalid @enderror" 
                                       id="expense_amount" name="expense_amount" 
                                       value="{{ old('expense_amount', $expense->expense_amount) }}" 
                                       placeholder="0.000" step="0.001" min="0" required>
                                @error('expense_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- VAT -->
                            <div class="form-group mb-3">
                                <label for="vat_type" class="form-label">VAT</label>
                                <select class="form-control" id="vat_type" name="vat_type" onchange="vatcal()">
                                    <option value="withoutvat" {{ old('vat_type', $expense->vat_type) == 'withoutvat' ? 'selected' : '' }}>Without VAT</option>
                                    <option value="vat" {{ old('vat_type', $expense->vat_type) == 'vat' ? 'selected' : '' }}>VAT</option>
                                </select>
                            </div>

                            <!-- Total Amount -->
                            <div class="form-group mb-3">
                                <label for="total_amount" class="form-label">Total Amount</label>
                                <input type="number" class="form-control only_currency" 
                                       id="total_amount" name="total_amount" 
                                       value="{{ old('total_amount', $expense->total_amount) }}" 
                                       placeholder="0.000" step="0.001" readonly>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="col-md-6">
                            <!-- Reference No -->
                            <div class="form-group mb-3">
                                <label for="reference_no" class="form-label">Reference No</label>
                                <input type="text" class="form-control" id="reference_no" name="reference_no" 
                                       value="{{ old('reference_no', $expense->reference_no) }}" placeholder="Enter reference number">
                            </div>

                            <!-- Note -->
                            <div class="form-group mb-3">
                                <label for="note" class="form-label">Note</label>
                                <textarea class="form-control" id="note" name="note" rows="4" 
                                          placeholder="Enter expense notes">{{ old('note', $expense->note) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-3 offset-md-3">
                            <button type="submit" class="btn btn-success btn-block" id="saveBtn">
                                <i class="fas fa-save me-2"></i>Update Expense
                            </button>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('expenses.index') }}" class="btn btn-warning btn-block">
                                <i class="fas fa-times me-2"></i>Close
                            </a>
                        </div>
                    </div>
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
    // Initialize Select2
    $('.select2').select2({
        theme: 'bootstrap-5',
        width: '100%'
    });
    
    // Load subcategories on page load if category is selected
    const selectedCategoryId = $('#category_id').val();
    const selectedSubCategoryId = {{ old('sub_category_id', $expense->sub_category_id) ?? 'null' }};
    
    if (selectedCategoryId) {
        loadSubCategories(selectedCategoryId, selectedSubCategoryId);
        
        // Show/hide vehicle section based on category
        if (selectedCategoryId == '8') { // Transport/Vehicle category
            $('#vehicle_section').show();
        } else {
            $('#vehicle_section').hide();
        }
    }
    
    // Category change event
    $('#category_id').on('change', function() {
        const categoryId = $(this).val();
        loadSubCategories(categoryId);
        
        // Show/hide vehicle section based on category
        if (categoryId == '8') {
            $('#vehicle_section').show();
        } else {
            $('#vehicle_section').hide();
        }
    });
    
    // Calculate total when amount or VAT changes
    $('#expense_amount, #vat_type').on('change keyup', function() {
        vatcal();
    });
    
    // Initial calculation
    vatcal();
});

function loadSubCategories(categoryId, selectedId = null) {
    const subCategorySelect = $('#sub_category_id');
    
    // Clear subcategories
    subCategorySelect.html('<option value="">-Select-</option>');
    
    if (categoryId) {
        // Load subcategories
        $.get(`/expenses/subcategories/${categoryId}`)
            .done(function(data) {
                data.forEach(function(subCategory) {
                    const isSelected = selectedId && selectedId == subCategory.id ? 'selected' : '';
                    subCategorySelect.append(`<option value="${subCategory.id}" ${isSelected}>${subCategory.sub_category_name}</option>`);
                });
            })
            .fail(function() {
                console.error('Failed to load subcategories');
            });
    }
}

function vatcal() {
    const expenseAmount = parseFloat($('#expense_amount').val()) || 0;
    const vatType = $('#vat_type').val();
    
    let totalAmount = expenseAmount;
    
    if (vatType === 'vat') {
        // Add 5% VAT
        totalAmount = expenseAmount * 1.05;
    }
    
    $('#total_amount').val(totalAmount.toFixed(3));
}

// Form validation
$('#expenseForm').on('submit', function(e) {
    const expenseAmount = parseFloat($('#expense_amount').val()) || 0;
    
    if (expenseAmount <= 0) {
        e.preventDefault();
        alert('Please enter a valid expense amount.');
        $('#expense_amount').focus();
        return false;
    }
});
</script>
@endpush