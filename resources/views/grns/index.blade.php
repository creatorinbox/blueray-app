@extends('layouts.app')

@section('title', 'Goods Receipt Notes (GRN)')

@push('styles')
<link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
@endpush

@section('content')
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-box me-2"></i>Goods Receipt Notes (GRN)
        <small class="text-muted d-block mt-1">View/Search GRNs</small>
    </h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item active">GRNs</li>
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
    <div class="col-md-6">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h5>Total GRNs</h5>
                <h2>{{ $stats['total'] }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h5>This Month</h5>
                <h2>{{ $stats['this_month'] }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title mb-0">
            <i class="fas fa-list me-2"></i>GRN List
        </h3>
        <a href="{{ route('purchase-orders.index') }}" class="btn btn-info">
            <i class="fas fa-shopping-cart me-2"></i>View Purchase Orders
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover" id="grnsTable" style="width:100%">
                <thead class="table-primary">
                    <tr>
                        <th style="width:12%">GRN No</th>
                        <th style="width:10%">GRN Date</th>
                        <th style="width:12%">PO No</th>
                        <th style="width:18%">Supplier</th>
                        <th style="width:12%">Invoice No</th>
                        <th style="width:8%">Currency</th>
                        <th style="width:10%">Exchange Rate</th>
                        <th style="width:10%">Remarks</th>
                        <th style="width:8%" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($grns as $grn)
                    <tr>
                        <td><strong>{{ $grn->grn_no }}</strong></td>
                        <td>{{ $grn->grn_date->format('d-m-Y') }}</td>
                        <td>
                            @if($grn->purchaseOrder)
                                <a href="{{ route('purchase-orders.show', $grn->purchase_order_id) }}" class="text-primary">
                                    {{ $grn->purchaseOrder->po_no }}
                                </a>
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $grn->supplier->supplier_name ?? 'N/A' }}</td>
                        <td>{{ $grn->invoice_no ?? '-' }}</td>
                        <td>{{ $grn->currency }}</td>
                        <td>{{ number_format($grn->exchange_rate, 4) }}</td>
                        <td>{{ $grn->remarks ?? '-' }}</td>
                        <td class="text-center">
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-bs-toggle="dropdown">
                                    Action
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('grns.show', $grn->id) }}"><i class="fas fa-eye me-2"></i>View</a></li>
                                    <li><a class="dropdown-item" href="{{ route('grns.print', $grn->id) }}" target="_blank"><i class="fas fa-print me-2"></i>Print</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('grns.destroy', $grn->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure? This will update the PO status back to Approved.')">
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
    $('#grnsTable').DataTable({
        order: [[1, 'desc']],
        pageLength: 25,
        responsive: true
    });
});
</script>
@endpush
