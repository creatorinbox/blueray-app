@extends('layouts.app')

@section('title', 'Item Details')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">
                <i class="fas fa-box me-2"></i>Item Details
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('items.index') }}">Items</a></li>
                    <li class="breadcrumb-item active">{{ $item->item_name }}</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('items.edit', $item) }}" class="btn btn-warning me-2">
                <i class="fas fa-edit me-2"></i>Edit Item
            </a>
            <a href="{{ route('items.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to List
            </a>
        </div>
    </div>
</div>

<!-- Item Information -->
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>Item Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-sm-3"><strong>Item Code:</strong></div>
                    <div class="col-sm-3">{{ $item->item_code ?? 'N/A' }}</div>
                    <div class="col-sm-3"><strong>Item Name:</strong></div>
                    <div class="col-sm-3">{{ $item->item_name }}</div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-sm-3"><strong>Company:</strong></div>
                    <div class="col-sm-3">{{ $item->company->company_name ?? 'N/A' }}</div>
                    <div class="col-sm-3"><strong>Supplier:</strong></div>
                    <div class="col-sm-3">{{ $item->supplier->supplier_name ?? 'Not Assigned' }}</div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-sm-3"><strong>Item Type:</strong></div>
                    <div class="col-sm-3">
                        <span class="badge bg-secondary">{{ $item->item_type }}</span>
                    </div>
                    <div class="col-sm-3"><strong>Stock Type:</strong></div>
                    <div class="col-sm-3">
                        <span class="badge {{ $item->stock_type == 'Stock' ? 'bg-success' : 'bg-info' }}">
                            {{ $item->stock_type }}
                        </span>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-sm-3"><strong>Brand:</strong></div>
                    <div class="col-sm-3">{{ $item->brand ?? '-' }}</div>
                    <div class="col-sm-3"><strong>Unit:</strong></div>
                    <div class="col-sm-3">{{ $item->unit }}</div>
                </div>
                
                @if($item->oem_part_no || $item->duplicate_part_no)
                <div class="row mb-3">
                    @if($item->oem_part_no)
                        <div class="col-sm-3"><strong>OEM Part No:</strong></div>
                        <div class="col-sm-3">{{ $item->oem_part_no }}</div>
                    @endif
                    @if($item->duplicate_part_no)
                        <div class="col-sm-3"><strong>Duplicate Part No:</strong></div>
                        <div class="col-sm-3">{{ $item->duplicate_part_no }}</div>
                    @endif
                </div>
                @endif
                
                <div class="row mb-3">
                    <div class="col-sm-3"><strong>Sale Price:</strong></div>
                    <div class="col-sm-3">OMR {{ number_format($item->sale_price, 3) }}</div>
                    @if($item->min_sale_price)
                        <div class="col-sm-3"><strong>Min Sale Price:</strong></div>
                        <div class="col-sm-3">OMR {{ number_format($item->min_sale_price, 3) }}</div>
                    @endif
                </div>
                
                <div class="row mb-3">
                    <div class="col-sm-3"><strong>VAT:</strong></div>
                    <div class="col-sm-3">
                        @if($item->vat_applicable)
                            <span class="badge bg-warning">{{ $item->vat_rate }}%</span>
                        @else
                            <span class="badge bg-secondary">Not Applicable</span>
                        @endif
                    </div>
                    <div class="col-sm-3"><strong>Status:</strong></div>
                    <div class="col-sm-3">
                        @if($item->is_active)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </div>
                </div>
                
                @if($item->description)
                <div class="row mb-3">
                    <div class="col-sm-3"><strong>Description:</strong></div>
                    <div class="col-sm-9">{{ $item->description }}</div>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Stock Summary -->
    <div class="col-md-4">
        @if($item->stock_type == 'Stock')
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                    <i class="fas fa-chart-bar me-2"></i>Stock Summary
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>Current Stock:</span>
                        <span class="badge {{ $stockSummary['current_stock'] > 10 ? 'bg-success' : ($stockSummary['current_stock'] > 0 ? 'bg-warning' : 'bg-danger') }}">
                            {{ number_format($stockSummary['current_stock'], 2) }} {{ $item->unit }}
                        </span>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>Total Value:</span>
                        <span><strong>OMR {{ number_format($stockSummary['total_value'], 3) }}</strong></span>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>Average Cost:</span>
                        <span>OMR {{ number_format($stockSummary['avg_cost'], 3) }}</span>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>Stock Lots:</span>
                        <span>{{ $stockSummary['stock_lots'] }}</span>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@if($item->stock_type == 'Stock' && $item->stockLots->count() > 0)
<!-- Stock Lots -->
<div class="card mt-3">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-boxes me-2"></i>Stock Lots
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-sm">
                <thead class="table-primary">
                    <tr>
                        <th>Lot No</th>
                        <th>Expiry Date</th>
                        <th>Quantity on Hand</th>
                        <th>Landed Cost/Unit</th>
                        <th>Total Value</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($item->stockLots->where('qty_available', '>', 0) as $lot)
                    <tr>
                        <td>{{ $lot->lot_no }}</td>
                        <td>
                            @if($lot->expiry_date)
                                {{ $lot->expiry_date->format('d M Y') }}
                                @if($lot->expiry_date->isPast())
                                    <span class="badge bg-danger">Expired</span>
                                @elseif($lot->expiry_date->diffInDays() < 30)
                                    <span class="badge bg-warning">Expiring Soon</span>
                                @endif
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ number_format($lot->qty_available, 2) }}</td>
                        <td>OMR {{ number_format($lot->cost_price, 3) }}</td>
                        <td>OMR {{ number_format($lot->qty_available * $lot->cost_price, 3) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

@if($item->quotationItems->count() > 0)
<!-- Recent Quotations -->
<div class="card mt-3">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-file-alt me-2"></i>Recent Quotations (Last 10)
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-sm">
                <thead class="table-primary">
                    <tr>
                        <th>Quotation No</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Quantity</th>
                        <th>Rate</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($item->quotationItems->take(10) as $quotationItem)
                    <tr>
                        <td>{{ $quotationItem->quotation->quotation_no }}</td>
                        <td>{{ $quotationItem->quotation->quotation_date->format('d M Y') }}</td>
                        <td>{{ $quotationItem->quotation->customer_name }}</td>
                        <td>{{ number_format($quotationItem->qty, 2) }}</td>
                        <td>OMR {{ number_format($quotationItem->rate, 3) }}</td>
                        <td>
                            <span class="badge bg-{{ $quotationItem->quotation->status == 'Approved' ? 'success' : ($quotationItem->quotation->status == 'Pending' ? 'warning' : 'secondary') }}">
                                {{ $quotationItem->quotation->status }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif
@endsection