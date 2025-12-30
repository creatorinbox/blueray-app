@extends('layouts.app')

@section('title', 'Edit Customer')

@section('content')
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-user-edit me-2"></i>Edit Customer
    </h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('customers.index') }}">Customers</a></li>
            <li class="breadcrumb-item active">Edit Customer</li>
        </ol>
    </nav>
</div>

<form action="{{ route('customers.update', $customer->id) }}" method="POST" id="customers-form">
    @csrf
    @method('PUT')
    
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-info-circle me-2"></i>Customer Information
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Left Column -->
                <div class="col-md-6">
                    <!-- Customer Name -->
                    <div class="mb-3">
                        <label class="form-label">Customer Name <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('customer_name') is-invalid @enderror" 
                               name="customer_name" 
                               value="{{ old('customer_name', $customer->customer_name) }}" 
                               required>
                        @error('customer_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Username -->
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" 
                               class="form-control @error('customer_username') is-invalid @enderror" 
                               name="customer_username" 
                               value="{{ old('customer_username', $customer->customer_username) }}">
                        @error('customer_username')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Designation -->
                    <div class="mb-3">
                        <label class="form-label">Designation</label>
                        <input type="text" 
                               class="form-control @error('designation') is-invalid @enderror" 
                               name="designation" 
                               value="{{ old('designation', $customer->designation) }}">
                        @error('designation')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Mobile -->
                    <div class="mb-3">
                        <label class="form-label">Mobile</label>
                        <input type="text" 
                               class="form-control @error('mobile') is-invalid @enderror" 
                               name="mobile" 
                               value="{{ old('mobile', $customer->mobile) }}">
                        @error('mobile')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" 
                               class="form-control @error('phone') is-invalid @enderror" 
                               name="phone" 
                               value="{{ old('phone', $customer->phone) }}">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" 
                               class="form-control @error('email') is-invalid @enderror" 
                               name="email" 
                               value="{{ old('email', $customer->email) }}">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Alternative Email -->
                    <div class="mb-3">
                        <label class="form-label">Alternative Email</label>
                        <input type="email" 
                               class="form-control @error('alt_email') is-invalid @enderror" 
                               name="alt_email" 
                               value="{{ old('alt_email', $customer->alt_email) }}">
                        @error('alt_email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- GST Number -->
                    <div class="mb-3">
                        <label class="form-label">GST Number <span class="text-danger" id="gstin_required_indicator" style="display:none;">*</span></label>
                        <input type="text" 
                               class="form-control @error('gstin') is-invalid @enderror" 
                               name="gstin" 
                               id="gstin"
                               value="{{ old('gstin', $customer->gstin) }}"
                               onchange="validateGstTaxFields()">
                        @error('gstin')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <span id="gstin_msg" style="display:none" class="text-danger"></span>
                    </div>

                    <!-- Tax Number -->
                    <div class="mb-3">
                        <label class="form-label">Tax Number <span class="text-danger" id="tax_number_required_indicator" style="display:none;">*</span></label>
                        <input type="text" 
                               class="form-control @error('tax_number') is-invalid @enderror" 
                               name="tax_number" 
                               id="tax_number"
                               value="{{ old('tax_number', $customer->tax_number) }}"
                               onchange="validateGstTaxFields()">
                        @error('tax_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <span id="tax_number_msg" style="display:none" class="text-danger"></span>
                    </div>

                    <!-- TRN -->
                    <div class="mb-3">
                        <label class="form-label">TRN</label>
                        <input type="text" 
                               class="form-control @error('trn') is-invalid @enderror" 
                               name="trn" 
                               value="{{ old('trn', $customer->trn) }}">
                        @error('trn')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Right Column -->
                <div class="col-md-6">
                    <!-- Credit Limit -->
                    <div class="mb-3">
                        <label class="form-label">Credit Limit</label>
                        <input type="number" 
                               step="0.01"
                               class="form-control @error('credit_limit') is-invalid @enderror" 
                               name="credit_limit" 
                               value="{{ old('credit_limit', $customer->credit_limit) }}">
                        @error('credit_limit')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Opening Balance -->
                    <div class="mb-3">
                        <label class="form-label">Opening Balance</label>
                        <input type="number" 
                               step="0.01"
                               class="form-control @error('opening_balance') is-invalid @enderror" 
                               name="opening_balance" 
                               value="{{ old('opening_balance', $customer->opening_balance) }}">
                        @error('opening_balance')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Payment Terms -->
                    <div class="mb-3">
                        <label class="form-label">Payment Terms (Days)</label>
                        <input type="number" 
                               class="form-control @error('payment_terms_days') is-invalid @enderror" 
                               name="payment_terms_days" 
                               value="{{ old('payment_terms_days', $customer->payment_terms_days) }}">
                        @error('payment_terms_days')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Custom Period -->
                    <div class="mb-3">
                        <label class="form-label">Custom Period</label>
                        <input type="text" 
                               class="form-control @error('custom_period') is-invalid @enderror" 
                               name="custom_period" 
                               value="{{ old('custom_period', $customer->custom_period) }}">
                        @error('custom_period')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Country -->
                    <div class="mb-3">
                        <label class="form-label">Country</label>
                        <input type="text" 
                               class="form-control @error('country') is-invalid @enderror" 
                               name="country" 
                               value="{{ old('country', $customer->country) }}">
                        @error('country')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- State -->
                    <div class="mb-3">
                        <label class="form-label">State</label>
                        <input type="text" 
                               class="form-control @error('state') is-invalid @enderror" 
                               name="state" 
                               value="{{ old('state', $customer->state) }}">
                        @error('state')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- City -->
                    <div class="mb-3">
                        <label class="form-label">City</label>
                        <input type="text" 
                               class="form-control @error('city') is-invalid @enderror" 
                               name="city" 
                               value="{{ old('city', $customer->city) }}">
                        @error('city')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Postcode -->
                    <div class="mb-3">
                        <label class="form-label">Postcode</label>
                        <input type="text" 
                               class="form-control @error('postcode') is-invalid @enderror" 
                               name="postcode" 
                               value="{{ old('postcode', $customer->postcode) }}">
                        @error('postcode')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Address -->
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea class="form-control @error('address') is-invalid @enderror" 
                                  name="address" 
                                  rows="3">{{ old('address', $customer->address) }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Active Status -->
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   name="is_active" 
                                   id="is_active"
                                   value="1"
                                   {{ old('is_active', $customer->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Active Customer
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customer Notes - Full Width -->
            <div class="row">
                <div class="col-12">
                    <div class="mb-3">
                        <label class="form-label">Customer Notes</label>
                        <textarea class="form-control @error('customer_notes') is-invalid @enderror" 
                                  name="customer_notes" 
                                  rows="4">{{ old('customer_notes', $customer->customer_notes) }}</textarea>
                        @error('customer_notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card-footer">
            <div class="d-flex justify-content-center">
                <button type="submit" class="btn btn-success me-3">
                    <i class="fas fa-save me-2"></i>Update Customer
                </button>
                <a href="{{ route('customers.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i>Cancel
                </a>
            </div>
        </div>
    </div>
</form>
@endsection

@section('scripts')
<script>
// Function to validate GST and Tax number fields
function validateGstTaxFields() {
    var gstin = $('#gstin').val().trim();
    var tax_number = $('#tax_number').val().trim();
    
    // If both fields are empty, make GST required
    if (gstin === '' && tax_number === '') {
        $('#gstin_required_indicator').show();
        $('#tax_number_required_indicator').show();
    } else {
        $('#gstin_required_indicator').hide();
        $('#tax_number_required_indicator').hide();
    }
}

// Call validation on page load
$(document).ready(function() {
    validateGstTaxFields();
    
    // Override the form submission to add custom validation
    $('#customers-form').on('submit', function(e) {
        var gstin = $('#gstin').val().trim();
        var tax_number = $('#tax_number').val().trim();
        
        // Check if both GST and Tax number are empty
        if (gstin === '' && tax_number === '') {
            $('#gstin_msg').text('Either GST Number or Tax Number is required').show();
            $('#tax_number_msg').text('Either GST Number or Tax Number is required').show();
            e.preventDefault();
            return false;
        } else {
            $('#gstin_msg').hide();
            $('#tax_number_msg').hide();
        }
    });
    
    // Clear validation messages when user types
    $('#gstin, #tax_number').on('input', function() {
        validateGstTaxFields();
        $('#gstin_msg').hide();
        $('#tax_number_msg').hide();
    });
});
</script>
@endsection