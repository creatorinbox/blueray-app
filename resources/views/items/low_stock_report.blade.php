@extends('layouts.app')

@section('title', 'Low Stock Alert')

@push('styles')
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css" rel="stylesheet">
@endpush

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">
                <i class="fas fa-exclamation-triangle me-2 text-warning"></i>Low Stock Alert
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('items.index') }}">Items</a></li>
                    <li class="breadcrumb-item active">Low Stock Alert</li>
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

@if($items->count() == 0)
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
            <h3>All Good!</h3>
            <p class="text-muted">No items are currently running low on stock.</p>
            <a href="{{ route('items.index') }}" class="btn btn-primary">
                <i class="fas fa-boxes me-2"></i>View All Items
            </a>
        </div>
    </div>
@else
    <!-- Alert Summary -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">Out of Stock</h5>
                            <h2 class="mb-0">{{ $items->where('current_stock', '<=', 0)->count() }}</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-times-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">Low Stock</h5>
                            <h2 class="mb-0">{{ $items->where('current_stock', '>', 0)->where('current_stock', '<', 10)->count() }}</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">Total Items</h5>
                            <h2 class="mb-0">{{ $items->count() }}</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-list fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Low Stock Items Table -->
    <div class="card">
        <div class="card-header bg-warning">
            <h5 class="mb-0 text-dark">
                <i class="fas fa-exclamation-triangle me-2"></i>Items Requiring Attention
            </h5>
        </div>
        <div class="card-body">
            <div class="alert alert-warning" role="alert">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Notice:</strong> The following items are either out of stock or running low (less than 10 units). Consider placing purchase orders for these items.
            </div>
            
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="lowStockTable">
                    <thead class="table-warning">
                        <tr>
                            <th>Priority</th>
                            <th>Item Code</th>
                            <th>Item Name</th>
                            <th>Brand</th>
                            <th>Unit</th>
                            <th>Current Stock</th>
                            <th>Status</th>
                            <th>Last Sale Price</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items->sortBy('current_stock') as $item)
                        <tr class="{{ $item->current_stock <= 0 ? 'table-danger' : 'table-warning' }}">
                            <td>
                                @if($item->current_stock <= 0)
                                    <span class="badge bg-danger">
                                        <i class="fas fa-ban me-1"></i>URGENT
                                    </span>
                                @else
                                    <span class="badge bg-warning">
                                        <i class="fas fa-exclamation me-1"></i>LOW
                                    </span>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $item->item_code ?? 'N/A' }}</strong>
                            </td>
                            <td>
                                <strong>{{ $item->item_name }}</strong>
                                @if($item->oem_part_no)
                                    <br><small class="text-muted">OEM: {{ $item->oem_part_no }}</small>
                                @endif
                            </td>
                            <td>{{ $item->brand ?? '-' }}</td>
                            <td>{{ $item->unit }}</td>
                            <td>
                                <span class="badge {{ $item->current_stock <= 0 ? 'bg-danger' : 'bg-warning' }}">
                                    {{ number_format($item->current_stock, 2) }}
                                </span>
                            </td>
                            <td>
                                @if($item->current_stock <= 0)
                                    <span class="badge bg-danger">Out of Stock</span>
                                @else
                                    <span class="badge bg-warning">Low Stock</span>
                                @endif
                            </td>
                            <td>OMR {{ number_format($item->sale_price, 3) }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('items.show', $item) }}" 
                                       class="btn btn-sm btn-outline-info" 
                                       title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('items.edit', $item) }}" 
                                       class="btn btn-sm btn-outline-warning" 
                                       title="Edit Item">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-success" 
                                            onclick="suggestPurchaseOrder('{{ $item->item_name }}')"
                                            title="Create Purchase Order">
                                        <i class="fas fa-shopping-cart"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Action Suggestions -->
    <div class="card mt-3">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">
                <i class="fas fa-lightbulb me-2"></i>Recommended Actions
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0">
                            <i class="fas fa-shopping-cart fa-2x text-primary me-3"></i>
                        </div>
                        <div>
                            <h6>Create Purchase Orders</h6>
                            <p class="mb-0 text-muted">Generate purchase orders for out of stock items</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0">
                            <i class="fas fa-bell fa-2x text-warning me-3"></i>
                        </div>
                        <div>
                            <h6>Set Stock Alerts</h6>
                            <p class="mb-0 text-muted">Configure minimum stock levels for items</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0">
                            <i class="fas fa-chart-line fa-2x text-success me-3"></i>
                        </div>
                        <div>
                            <h6>Analyze Usage</h6>
                            <p class="mb-0 text-muted">Review item consumption patterns</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
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
    $('#lowStockTable').DataTable({
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excel',
                title: 'Low Stock Alert - ' + new Date().toLocaleDateString(),
                exportOptions: {
                    columns: [1,2,3,4,5,6,7] // Exclude priority and actions columns
                }
            },
            {
                extend: 'pdf',
                title: 'Low Stock Alert - ' + new Date().toLocaleDateString(),
                exportOptions: {
                    columns: [1,2,3,4,5,6,7]
                }
            },
            'print'
        ],
        pageLength: 25,
        responsive: true,
        order: [[5, 'asc']] // Order by current stock (lowest first)
    });
});

function suggestPurchaseOrder(itemName) {
    alert('Suggested Action: Create a Purchase Order for \"' + itemName + '\"\\n\\nYou can create a new Purchase Order from the Purchase module.');
}
</script>
@endpush