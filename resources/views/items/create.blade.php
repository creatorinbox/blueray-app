@extends('layouts.app')

@section('title', 'Add New Item')

@section('content')
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-plus me-2"></i>Add New Item
    </h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('items.index') }}">Items</a></li>
            <li class="breadcrumb-item active">Add New Item</li>
        </ol>
    </nav>
</div>

<form action="{{ route('items.store') }}" method="POST" id="itemForm">
    @csrf
    
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-info-circle me-2"></i>Item Information
            </h5>
        </div>
        <div class="card-body">
                    <div class="row mb-3 d-none">
                                <label class="col-sm-2 col-form-label">Stock Type</label>
                                <div class="col-sm-4">
                                    <input type="hidden" name="stock_type" value="Stock">
                                    <input type="text" class="form-control" value="Stock" readonly>
                                </div>
                            </div>
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Item Name <span class="text-danger">*</span></label>
                <div class="col-sm-4">
                    <input type="text" 
                           class="form-control @error('item_name') is-invalid @enderror" 
                           name="item_name" 
                           value="{{ old('item_name') }}" 
                           placeholder="Enter item name"
                           required>
                    @error('item_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <label class="col-sm-2 col-form-label">Unit <span class="text-danger">*</span></label>
                <div class="col-sm-4">
                    <select name="unit" class="form-select @error('unit') is-invalid @enderror" required>
                        <option value="">-Select-</option>
                        @foreach($units as $u)
                            <option value="{{ $u->name }}" {{ old('unit') == $u->name ? 'selected' : '' }}>{{ $u->name }}{{ $u->symbol ? ' (' . $u->symbol . ')' : '' }}</option>
                        @endforeach
                    </select>
                    @error('unit')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">OEM Part No<span class="text-danger">*</span></label>
                <div class="col-sm-4">
                    <input type="text" 
                           class="form-control @error('oem_part_no') is-invalid @enderror" 
                           name="oem_part_no" 
                           value="{{ old('oem_part_no') }}" 
                           placeholder="Enter OEM part number"
                           required>
                    @error('oem_part_no')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <label class="col-sm-2 col-form-label">Duplicate Part No<span class="text-danger">*</span></label>
                <div class="col-sm-4">
                    <input type="text" 
                           class="form-control @error('duplicate_part_no') is-invalid @enderror" 
                           name="duplicate_part_no" 
                           value="{{ old('duplicate_part_no') }}" 
                           placeholder="Enter duplicate part number">
                    @error('duplicate_part_no')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">HSN Code<span class="text-danger">*</span></label>
                <div class="col-sm-4">
                    <input type="text" 
                           class="form-control @error('hsn_code') is-invalid @enderror" 
                           name="hsn_code" 
                           value="{{ old('hsn_code') }}" 
                           placeholder="Enter HSN code"
                           required>
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
                           value="{{ old('barcode') }}" 
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
                              placeholder="Enter item description">{{ old('description') }}</textarea>
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
                <label class="col-sm-2 col-form-label">MRP Price <span class="text-danger">*</span></label>
                <div class="col-sm-4">
                    <input type="number" 
                           class="form-control @error('sale_price') is-invalid @enderror" 
                           name="sale_price" 
                           id="sale_price"
                           value="{{ old('sale_price', '0') }}" 
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
                           value="{{ old('profit_margin', '0') }}" 
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
                <label class="col-sm-2 col-form-label">Purchase Price<span class="text-danger">*</span></label>
                <div class="col-sm-4">
                    <input type="number"
                           class="form-control @error('purchase_price') is-invalid @enderror"
                           name="purchase_price"
                           id="purchase_price"
                           value="{{ old('purchase_price', '0') }}"
                           step="0.001"
                           min="0"
                           placeholder="0.000"
                           required>
                    @error('purchase_price')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <label class="col-sm-2 col-form-label">Currency</label>
                <div class="col-sm-2">
                    <select name="currency" id="currency" class="form-select">
                        <option value="OMR" {{ old('currency', 'OMR') == 'OMR' ? 'selected' : '' }}>OMR</option>
                        <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>USD</option>
                        <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>EUR</option>
                        <option value="AED" {{ old('currency') == 'AED' ? 'selected' : '' }}>AED</option>
                    </select>
                </div>

                <div class="col-sm-2">
                    <input type="number"
                           class="form-control @error('currency_value') is-invalid @enderror"
                           name="currency_value"
                           id="currency_value"
                           value="{{ old('currency_value', '1.000') }}"
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
                    <input type="text" readonly id="purchase_price_omr" class="form-control" value="{{ old('purchase_price', '0.000') }}">
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
                        <i class="fas fa-save me-2"></i>Save Item
                    </button>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('items.index') }}" class="btn btn-secondary btn-block w-100">
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
    // Purchase price conversion handling with live exchange-rate lookup
    async function fetchRateToOMR(fromCurrency) {
        try {
            if (!fromCurrency || fromCurrency === 'OMR') return 1;

            // First try exchangerate.host (no API key required)
            try {
                const url = `https://api.exchangerate.host/convert?from=${encodeURIComponent(fromCurrency)}&to=OMR&amount=1`;
                console.debug('Trying exchangerate.host', url);
                const res = await fetch(url);
                if (res.ok) {
                    const data = await res.json();
                    const rate = (data && (data.result !== undefined)) ? parseFloat(data.result) : null;
                    if (rate && !isNaN(rate)) return rate;
                    console.warn('exchangerate.host returned no rate, falling back', data);
                } else {
                    console.warn('exchangerate.host responded not ok', res.status);
                }
            } catch (e) {
                console.warn('exchangerate.host request failed', e);
            }

            // Fallback: try open ER API which provides rates map (no key)
            try {
                const url2 = `https://open.er-api.com/v6/latest/${encodeURIComponent(fromCurrency)}`;
                console.debug('Trying open.er-api.com', url2);
                const r2 = await fetch(url2);
                if (r2.ok) {
                    const d2 = await r2.json();
                    if (d2 && d2.result === 'success' && d2.rates && d2.rates.OMR) {
                        const rate2 = parseFloat(d2.rates.OMR);
                        if (rate2 && !isNaN(rate2)) return rate2;
                    }
                    // Some providers use different shapes; try generic property
                    if (d2 && d2.rates && d2.rates['OMR']) {
                        const rate2b = parseFloat(d2.rates['OMR']);
                        if (rate2b && !isNaN(rate2b)) return rate2b;
                    }
                    console.warn('open.er-api.com returned unexpected shape', d2);
                } else {
                    console.warn('open.er-api.com responded not ok', r2.status);
                }
            } catch (e2) {
                console.warn('open.er-api.com request failed', e2);
            }

            // Last resort: return null so caller can fallback to manual value
            return null;
        } catch (err) {
            console.error('fetchRateToOMR final error', err);
            return null;
        }
    }

    async function updatePurchaseConverted() {
        const purchaseEl = document.getElementById('purchase_price');
        const currencyEl = document.getElementById('currency');
        const cvEl = document.getElementById('currency_value');
        const displayEl = document.getElementById('purchase_price_omr');

        const purchase = parseFloat(purchaseEl ? purchaseEl.value : 0) || 0;
        const currency = currencyEl ? currencyEl.value : 'OMR';

        if (currency === 'OMR') {
            if (cvEl) cvEl.parentElement.style.display = 'none';
            if (cvEl) cvEl.value = '1.000';
            const converted = purchase * 1;
            if (displayEl) displayEl.value = converted.toFixed(3);
            return;
        }

        // show loader state in currency_value while fetching
        if (cvEl) {
            cvEl.parentElement.style.display = '';
            cvEl.value = '...';
        }

        const rate = await fetchRateToOMR(currency);
        let cv = parseFloat(rate) || 0;
        if (!cv || cv <= 0) {
            // fallback to existing input value if API failed
            cv = parseFloat(cvEl ? cvEl.value : 1) || 1;
            console.warn('Using fallback currency value', cv);
        } else {
            // write back rate into currency_value so user sees it
            if (cvEl) cvEl.value = cv.toFixed(3);
        }

        const converted = purchase * cv;
        if (displayEl) displayEl.value = converted.toFixed(3);
    }

    // Wire events
    const purchaseEl = document.getElementById('purchase_price');
    const currencyEl = document.getElementById('currency');
    const cvEl = document.getElementById('currency_value');
    if (purchaseEl) purchaseEl.addEventListener('input', debounce(updatePurchaseConverted, 300));
    if (currencyEl) currencyEl.addEventListener('change', updatePurchaseConverted);
    if (cvEl) cvEl.addEventListener('input', debounce(updatePurchaseConverted, 500));
    updatePurchaseConverted();

    // simple debounce helper
    function debounce(fn, wait) {
        let t = null;
        return function() {
            const args = arguments;
            clearTimeout(t);
            t = setTimeout(function(){ fn.apply(null, args); }, wait);
        };
    }
});
</script>
@endsection