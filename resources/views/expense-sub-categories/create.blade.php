@extends('layouts.app')

@section('title', 'Create Expense Subcategory')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
@endpush

@section('content')
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-layer-group me-2"></i>Expense Subcategory
        <small class="text-muted">Add/Update Subcategory</small>
    </h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('expense-sub-categories.index') }}">Subcategories List</a></li>
            <li class="breadcrumb-item active">Create Subcategory</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Please Enter Valid Data</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('expense-sub-categories.store') }}" method="POST" id="subCategoryForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-8 offset-md-2">
                            <!-- Category Selection -->
                            <div class="form-group mb-4">
                                <label for="category_id" class="form-label">
                                    Category <span class="text-danger">*</span>
                                </label>
                                <div class="col-12">
                                    <select class="form-control select2 @error('category_id') is-invalid @enderror" 
                                            id="category_id" name="category_id" required>
                                        <option value="">-Select Category-</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->category_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Subcategory Name -->
                            <div class="form-group mb-4">
                                <label for="sub_category_name" class="form-label">
                                    Subcategory Name <span class="text-danger">*</span>
                                </label>
                                <div class="col-12">
                                    <input type="text" 
                                           class="form-control @error('sub_category_name') is-invalid @enderror" 
                                           id="sub_category_name" 
                                           name="sub_category_name" 
                                           value="{{ old('sub_category_name') }}" 
                                           placeholder="Enter subcategory name"
                                           required>
                                    @error('sub_category_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="form-group mb-4">
                                <label for="description" class="form-label">Description</label>
                                <div class="col-12">
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" 
                                              name="description" 
                                              rows="4"
                                              placeholder="Enter subcategory description">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            
            <div class="card-footer">
                <div class="row">
                    <div class="col-md-8 offset-md-2 text-center">
                        <div class="row">
                            <div class="col-md-3 offset-md-3">
                                <button type="submit" 
                                        form="subCategoryForm"
                                        class="btn btn-success btn-block" 
                                        title="Save Data">
                                    <i class="fas fa-save me-2"></i>Save Subcategory
                                </button>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('expense-sub-categories.index') }}" class="btn btn-warning btn-block" title="Go Back">
                                    <i class="fas fa-times me-2"></i>Close
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
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

    // Form validation
    $('#subCategoryForm').on('submit', function(e) {
        const categoryId = $('#category_id').val();
        const subCategoryName = $('#sub_category_name').val().trim();
        
        if (categoryId === '') {
            e.preventDefault();
            $('#category_id').addClass('is-invalid');
            $('#category_id').focus();
            return false;
        }
        
        if (subCategoryName === '') {
            e.preventDefault();
            $('#sub_category_name').addClass('is-invalid');
            $('#sub_category_name').focus();
            return false;
        }
        
        $('#category_id, #sub_category_name').removeClass('is-invalid');
    });
    
    // Real-time validation
    $('#category_id').on('change', function() {
        const value = $(this).val();
        if (value === '') {
            $(this).addClass('is-invalid');
        } else {
            $(this).removeClass('is-invalid');
        }
    });
    
    $('#sub_category_name').on('keyup blur', function() {
        const value = $(this).val().trim();
        if (value === '') {
            $(this).addClass('is-invalid');
        } else {
            $(this).removeClass('is-invalid');
        }
    });
});
</script>
@endpush