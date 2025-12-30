@extends('layouts.app')

@section('title', 'Edit Expense Category')

@section('content')
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-edit me-2"></i>Edit Expense Category
        <small class="text-muted">Update Category Information</small>
    </h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('expense-categories.index') }}">Categories List</a></li>
            <li class="breadcrumb-item active">Edit Category</li>
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
                <form action="{{ route('expense-categories.update', $category->id) }}" method="POST" id="categoryForm">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-8 offset-md-2">
                            <!-- Category Name -->
                            <div class="form-group mb-4">
                                <label for="category_name" class="form-label">
                                    Category Name <span class="text-danger">*</span>
                                </label>
                                <div class="col-12">
                                    <input type="text" 
                                           class="form-control @error('category_name') is-invalid @enderror" 
                                           id="category_name" 
                                           name="category_name" 
                                           value="{{ old('category_name', $category->category_name) }}" 
                                           placeholder="Enter category name"
                                           autofocus>
                                    @error('category_name')
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
                                              placeholder="Enter category description">{{ old('description', $category->description) }}</textarea>
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
                                        form="categoryForm"
                                        class="btn btn-success btn-block" 
                                        title="Update Data">
                                    <i class="fas fa-save me-2"></i>Update Category
                                </button>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('expense-categories.index') }}" class="btn btn-warning btn-block" title="Go Back">
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
<script>
$(document).ready(function() {
    // Form validation
    $('#categoryForm').on('submit', function(e) {
        const categoryName = $('#category_name').val().trim();
        
        if (categoryName === '') {
            e.preventDefault();
            $('#category_name').addClass('is-invalid');
            $('#category_name').focus();
            return false;
        }
        
        $('#category_name').removeClass('is-invalid');
    });
    
    // Real-time validation
    $('#category_name').on('keyup blur', function() {
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