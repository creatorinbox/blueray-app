@extends('layouts.app')

@section('title', 'Stock Report')

@push('styles')
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css" rel="stylesheet">
@endpush

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">
                <i class="fas fa-chart-bar me-2"></i>Stock Report
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('items.index') }}">Items</a></li>
                    <li class="breadcrumb-item active">Stock Report</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('items.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Items
            </a>
        </div>
    </div>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Items in Stock</h5>
                        <h2 class="mb-0">{{ $items->count() }}</h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-boxes fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Total Stock Value</h5>
                        <h2 class="mb-0">OMR {{ number_format($items->sum('total_value'), 0) }}</h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-dollar-sign fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Low Stock Items</h5>
                        <h2 class="mb-0">{{ $items->where('current_stock', '<', 10)->count() }}</h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-exclamation-triangle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Avg Cost per Item</h5>
                        <h2 class="mb-0">OMR {{ $items->count() > 0 ? number_format($items->avg('avg_cost'), 2) : '0.00' }}</h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-calculator fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stock Report Table -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-list me-2"></i>Stock Details
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover" id="stockTable">
                <thead class="table-primary">
                    <tr>
                        <th>Item Code</th>
                        <th>Item Name</th>
                        <th>Brand</th>
                        <th>Unit</th>
                        <th>Current Stock</th>
                        <th>Avg Cost/Unit</th>
                        <th>Total Value</th>
                        <th>Stock Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                    <tr>
                        <td>{{ $item->item_code ?? 'N/A' }}</td>
                        <td>
                            <strong>{{ $item->item_name }}</strong>
                            @if($item->oem_part_no)
                                <br><small class="text-muted">OEM: {{ $item->oem_part_no }}</small>
                            @endif
                        </td>
                        <td>{{ $item->brand ?? '-' }}</td>
                        <td>{{ $item->unit }}</td>
                        <td>
                            <span class="badge {{ $item->current_stock > 10 ? 'bg-success' : ($item->current_stock > 0 ? 'bg-warning' : 'bg-danger') }}">
                                {{ number_format($item->current_stock, 2) }}
                            </span>
                        </td>
                        <td>OMR {{ number_format($item->avg_cost, 3) }}</td>
                        <td>OMR {{ number_format($item->total_value, 3) }}</td>
                        <td>
                            @if($item->current_stock > 10)
                                <span class="badge bg-success">Good Stock</span>
                            @elseif($item->current_stock > 0)
                                <span class="badge bg-warning">Low Stock</span>
                            @else
                                <span class="badge bg-danger">Out of Stock</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('items.show', $item) }}" 
                               class="btn btn-sm btn-outline-info" 
                               title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>

<script>
$(document).ready(function() {
    $('#stockTable').DataTable({
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excel',
                title: 'Stock Report - ' + new Date().toLocaleDateString()
            },
            {
                extend: 'pdf',
                title: 'Stock Report - ' + new Date().toLocaleDateString()
            },
            'print'
        ],
        pageLength: 25,
        responsive: true,
        order: [[4, 'desc']] // Order by current stock
    });
});
</script>
@endpush