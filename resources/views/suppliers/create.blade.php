@extends('layouts.app')

@section('title', 'Add Supplier')

@section('content')
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-truck-loading me-2"></i>Add Supplier
    </h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('suppliers.index') }}">Suppliers</a></li>
            <li class="breadcrumb-item active">Add Supplier</li>
        </ol>
    </nav>
</div>

<form action="{{ route('suppliers.store') }}" method="POST">
    @csrf
    
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Supplier Information</h5>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Supplier Name <span class="text-danger">*</span></label>
                <div class="col-sm-4">
                    <input type="text" 
                           class="form-control @error('supplier_name') is-invalid @enderror" 
                           name="supplier_name" 
                           value="{{ old('supplier_name') }}" 
                           required>
                    @error('supplier_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <label class="col-sm-2 col-form-label">Contact Person</label>
                <div class="col-sm-4">
                    <input type="text" 
                           class="form-control @error('contact_person') is-invalid @enderror" 
                           name="contact_person" 
                           value="{{ old('contact_person') }}">
                    @error('contact_person')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Email</label>
                <div class="col-sm-4">
                    <input type="email" 
                           class="form-control @error('email') is-invalid @enderror" 
                           name="email" 
                           value="{{ old('email') }}">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <label class="col-sm-2 col-form-label">Phone</label>
                <div class="col-sm-4">
                    <input type="text" 
                           class="form-control @error('phone') is-invalid @enderror" 
                           name="phone" 
                           value="{{ old('phone') }}">
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Address</label>
                <div class="col-sm-10">
                    <textarea class="form-control @error('address') is-invalid @enderror" 
                              name="address" 
                              rows="2">{{ old('address') }}</textarea>
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">City</label>
                <div class="col-sm-4">
                    <input type="text" 
                           class="form-control @error('city') is-invalid @enderror" 
                           name="city" 
                           value="{{ old('city') }}">
                    @error('city')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <label class="col-sm-2 col-form-label">Country</label>
                <div class="col-sm-4">
                    <input type="text" 
                           class="form-control @error('country') is-invalid @enderror" 
                           name="country" 
                           value="{{ old('country') }}">
                    @error('country')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">VAT Number</label>
                <div class="col-sm-4">
                    <input type="text" 
                           class="form-control @error('vat_number') is-invalid @enderror" 
                           name="vat_number" 
                           value="{{ old('vat_number') }}">
                    @error('vat_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <label class="col-sm-2 col-form-label">Payment Terms</label>
                <div class="col-sm-4">
                    <input type="text" 
                           class="form-control @error('payment_terms') is-invalid @enderror" 
                           name="payment_terms" 
                           value="{{ old('payment_terms') }}" 
                           placeholder="e.g., 30 days">
                    @error('payment_terms')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-sm-2"></div>
                <div class="col-sm-4">
                    <div class="form-check">
                        <input class="form-check-input" 
                               type="checkbox" 
                               name="is_active" 
                               value="1" 
                               id="is_active"
                               {{ old('is_active', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            Active Supplier
                        </label>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i>Save Supplier
            </button>
            <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">
                <i class="fas fa-times me-1"></i>Cancel
            </a>
        </div>
    </div>
</form>
@endsection