@extends('layouts.app')

@section('title', 'Edit Item')

@section('content')
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-edit me-2"></i>Edit Item
    </h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('items.index') }}">Items</a></li>
            <li class="breadcrumb-item"><a href="{{ route('items.show', $item) }}">{{ $item->item_name }}</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol>
    </nav>
</div>

<form action="{{ route('items.update', $item) }}" method="POST" id="itemForm">
    @csrf
    @method('PUT')
    
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-info-circle me-2"></i>Item Information
            </h5>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Item Name <span class="text-danger">*</span></label>
                <div class="col-sm-4">
                    <input type="text" 
                           class="form-control @error('item_name') is-invalid @enderror" 
                           name="item_name" 
                           value="{{ old('item_name', $item->item_name) }}" 
                           placeholder="Enter item name"
                           required>
                    @error('item_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <label class="col-sm-2 col-form-label">Unit <span class="text-danger">*</span></label>
                <div class="col-sm-4">
                    @php $units = ['PCS','KG','LTR','M','BOX','SET']; @endphp
                    <select name="unit" class="form-select @error('unit') is-invalid @enderror" required>
                        <option value="">-Select-</option>
                        @foreach($units as $u)
                            <option value="{{ $u }}" {{ old('unit', $item->unit) == $u ? 'selected' : '' }}>{{ $u }}</option>
                        @endforeach
                    </select>
                    @error('unit')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">OEM Part No</label>
                <div class="col-sm-4">
                    <input type="text" 
                           class="form-control @error('oem_part_no') is-invalid @enderror" 
                           name="oem_part_no" 
                           value="{{ old('oem_part_no', $item->oem_part_no) }}" 
                           placeholder="Enter OEM part number">
                    @error('oem_part_no')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <label class="col-sm-2 col-form-label">Duplicate Part No</label>
                <div class="col-sm-4">
                    <input type="text" 
                           class="form-control @error('duplicate_part_no') is-invalid @enderror" 
                           name="duplicate_part_no" 
                           value="{{ old('duplicate_part_no', $item->duplicate_part_no) }}" 
                           placeholder="Enter duplicate part number">
                    @error('duplicate_part_no')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">HSN Code</label>
                <div class="col-sm-4">
                    <input type="text" 
                           class="form-control @error('hsn_code') is-invalid @enderror" 
                           name="hsn_code" 
                           value="{{ old('hsn_code', $item->hsn_code) }}" 
                           placeholder="Enter HSN code">
                    @error('hsn_code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <!-- Minimum Quantity removed per request -->
            </div>
            
            <!-- Opening Stock removed per request -->
                
                <label class="col-sm-2 col-form-label">Barcode</label>
                <div class="col-sm-4">
                    <input type="text" 
                           class="form-control @error('barcode') is-invalid @enderror" 
                           name="barcode" 
                           value="{{ old('barcode', $item->barcode) }}" 
                           placeholder="Enter barcode">
                    @error('barcode')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Description</label>
                <div class="col-sm-10">
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              name="description" 
                              rows="3" 
                              placeholder="Enter item description">{{ old('description', $item->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <!-- Pricing Section -->
    <div class="card mt-3">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-dollar-sign me-2"></i>Pricing Information
            </h5>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Sales Price <span class="text-danger">*</span></label>
                <div class="col-sm-4">
                    <input type="number" 
                           class="form-control @error('sale_price') is-invalid @enderror" 
                           name="sale_price" 
                           id="sale_price"
                           value="{{ old('sale_price', $item->sale_price) }}" 
                           step="0.001" 
                           min="0"
                           placeholder="0.000"
                           onchange="calculateFinalPrice()"
                           required>
                    @error('sale_price')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <label class="col-sm-2 col-form-label">Profit Margin (%)</label>
                <div class="col-sm-4">
                    <input type="number" 
                           class="form-control @error('profit_margin') is-invalid @enderror" 
                           name="profit_margin" 
                           id="profit_margin"
                           value="{{ old('profit_margin', $item->profit_margin ?? 0) }}" 
                           step="0.01" 
                           min="0"
                           max="100"
                           placeholder="0.00"
                           onchange="calculateFinalPrice()">
                    @error('profit_margin')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Purchase Price</label>
                <div class="col-sm-4">
                    <input type="number"
                           class="form-control @error('purchase_price') is-invalid @enderror"
                           name="purchase_price"
                           id="purchase_price"
                           value="{{ old('purchase_price', $item->purchase_price ?? 0) }}"
                           step="0.001"
                           min="0"
                           placeholder="0.000">
                    @error('purchase_price')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <label class="col-sm-2 col-form-label">Currency</label>
                <div class="col-sm-2">
                    <select name="currency" id="currency" class="form-select">
                        <option value="OMR" {{ old('currency', $item->currency ?? 'OMR') == 'OMR' ? 'selected' : '' }}>OMR</option>
                        <option value="USD" {{ old('currency', $item->currency ?? '') == 'USD' ? 'selected' : '' }}>USD</option>
                        <option value="EUR" {{ old('currency', $item->currency ?? '') == 'EUR' ? 'selected' : '' }}>EUR</option>
                        <option value="AED" {{ old('currency', $item->currency ?? '') == 'AED' ? 'selected' : '' }}>AED</option>
                    </select>
                </div>

                <div class="col-sm-2">
                    <input type="number"
                           class="form-control @error('currency_value') is-invalid @enderror"
                           name="currency_value"
                           id="currency_value"
                           value="{{ old('currency_value', $item->currency_value ?? 1.000) }}"
                           step="0.001"
                           min="0"
                           placeholder="1.000">
                    @error('currency_value')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Purchase Price (OMR)</label>
                <div class="col-sm-4">
                    <input type="text" readonly id="purchase_price_omr" class="form-control" value="{{ old('purchase_price', $item->purchase_price ?? 0) }}">
                </div>
            </div>
            
            <!-- Final Price removed per request -->
        </div>
    </div>

    <!-- Form Buttons -->
    <div class="card mt-3">
        <div class="card-footer">
            <div class="row">
                <div class="col-md-3 offset-md-3">
                    <button type="submit" class="btn btn-success btn-block w-100">
                        <i class="fas fa-save me-2"></i>Update Item
                    </button>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('items.show', $item) }}" class="btn btn-secondary btn-block w-100">
                        <i class="fas fa-times me-2"></i>Cancel
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
function calculateFinalPrice() {
    const salePrice = parseFloat(document.getElementById('sale_price').value) || 0;
    const profitMargin = parseFloat(document.getElementById('profit_margin').value) || 0;
    
    // Calculate final price: sale_price + (sale_price * profit_margin / 100)
    const finalPrice = salePrice + (salePrice * profitMargin / 100);
    
    const finalEl = document.getElementById('final_price');
    if (finalEl) {
        finalEl.value = finalPrice.toFixed(3);
    }
}

// Calculate on page load
document.addEventListener('DOMContentLoaded', function() {
    calculateFinalPrice();
    // Purchase price conversion handling
    function updatePurchaseConverted() {
        const purchaseEl = document.getElementById('purchase_price');
        const currencyEl = document.getElementById('currency');
        const cvEl = document.getElementById('currency_value');
        const displayEl = document.getElementById('purchase_price_omr');

        const purchase = parseFloat(purchaseEl ? purchaseEl.value : 0) || 0;
        const currency = currencyEl ? currencyEl.value : 'OMR';
        let cv = parseFloat(cvEl ? cvEl.value : 1) || 1;

        if (currency === 'OMR') {
            if (cvEl) cvEl.parentElement.style.display = 'none';
            cv = 1;
            if (cvEl) cvEl.value = '1.000';
        } else {
            if (cvEl) cvEl.parentElement.style.display = '';
        }

        const converted = purchase * cv;
        if (displayEl) displayEl.value = converted.toFixed(3);
    }

    // Wire events
    const purchaseEl = document.getElementById('purchase_price');
    const currencyEl = document.getElementById('currency');
    const cvEl = document.getElementById('currency_value');
    if (purchaseEl) purchaseEl.addEventListener('input', updatePurchaseConverted);
    if (currencyEl) currencyEl.addEventListener('change', updatePurchaseConverted);
    if (cvEl) cvEl.addEventListener('input', updatePurchaseConverted);
    updatePurchaseConverted();
});
</script>
@endsection