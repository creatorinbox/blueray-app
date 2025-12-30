@extends('layouts.app')

@section('title', 'Purchase Orders')

@push('styles')
<link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
@endpush

@section('content')
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-shopping-cart me-2"></i>Purchase Orders
        <small class="text-muted d-block mt-1">View/Search Purchase Orders</small>
    </h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item active">Purchase Orders</li>
        </ol>
    </nav>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h5>Total POs</h5>
                <h2>{{ $stats['total'] }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-secondary text-white">
            <div class="card-body">
                <h5>Draft</h5>
                <h2>{{ $stats['draft'] }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <h5>Pending</h5>
                <h2>{{ $stats['pending'] }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h5>Approved</h5>
                <h2>{{ $stats['approved'] }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title mb-0">
            <i class="fas fa-list me-2"></i>Purchase Orders List
        </h3>
        <a href="{{ route('purchase-orders.create') }}" class="btn btn-info">
            <i class="fas fa-plus me-2"></i>New Purchase Order
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover" id="purchaseOrdersTable" style="width:100%">
                <thead class="table-primary">
                    <tr>
                        <th style="width:10%">PO No</th>
                        <th style="width:10%">PO Date</th>
                        <th style="width:20%">Supplier</th>
                        <th style="width:12%">Status</th>
                        <th style="width:8%">Currency</th>
                        <th style="width:15%" class="text-end">Total Amount</th>
                        <th style="width:15%">Remarks</th>
                        <th style="width:10%" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchaseOrders as $po)
                    <tr>
                        <td><strong>{{ $po->po_no }}</strong></td>
                        <td>{{ $po->po_date->format('d-m-Y') }}</td>
                        <td>{{ $po->supplier->supplier_name ?? 'N/A' }}</td>
                        <td>
                            @php
                                $badges = [
                                    'Draft' => 'secondary',
                                    'Pending' => 'warning',
                                    'Approved' => 'success',
                                    'Received' => 'info',
                                    'Cancelled' => 'danger'
                                ];
                                $badgeClass = $badges[$po->status] ?? 'light';
                            @endphp
                            <span class="badge bg-{{ $badgeClass }}">{{ $po->status }}</span>
                        </td>
                        <td>{{ $po->currency }}</td>
                        <td class="text-end"><strong>{{ number_format($po->total_amount, 3) }}</strong></td>
                        <td>{{ $po->remarks ?? '-' }}</td>
                        <td class="text-center">
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-bs-toggle="dropdown">
                                    Action
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('purchase-orders.show', $po->id) }}"><i class="fas fa-eye me-2"></i>View</a></li>
                                    @if($po->status === 'Draft')
                                    <li><a class="dropdown-item" href="{{ route('purchase-orders.edit', $po->id) }}"><i class="fas fa-edit me-2"></i>Edit</a></li>
                                    @endif
                                    @if($po->status === 'Approved')
                                    <li><a class="dropdown-item" href="{{ route('grns.create_from_po', $po->id) }}"><i class="fas fa-box me-2"></i>Create GRN</a></li>
                                    @endif
                                    <li><a class="dropdown-item" href="{{ route('purchase-orders.print', $po->id) }}" target="_blank"><i class="fas fa-print me-2"></i>Print</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('purchase-orders.destroy', $po->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure?')">
                                                <i class="fas fa-trash me-2"></i>Delete
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
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
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function() {
    $('#purchaseOrdersTable').DataTable({
        order: [[1, 'desc']],
        pageLength: 25,
        responsive: true
    });
});
</script>
@endpush
