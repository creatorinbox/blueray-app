@extends('layouts.app')

@section('title', 'Purchase Order Details - ' . $purchaseOrder->po_no)

@section('content')
<div class="page-header d-print-none">
    <div class="container-fluid">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    <i class="fas fa-file-invoice me-2"></i>Purchase Order Details
                </h2>
                <div class="text-muted mt-1">{{ $purchaseOrder->po_no }}</div>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="{{ route('purchase-orders.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Back to List
                    </a>
                    
                    @if($purchaseOrder->status === 'Draft')
                        <a href="{{ route('purchase-orders.edit', $purchaseOrder) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-1"></i>Edit
                        </a>
                    @endif
                    
                    @if($purchaseOrder->status === 'Approved')
                        <a href="{{ route('grns.create_from_po', $purchaseOrder) }}" class="btn btn-success">
                            <i class="fas fa-file-import me-1"></i>Create GRN
                        </a>
                    @endif
                    
                    <div class="dropdown">
                        <button class="btn btn-info dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-cog me-1"></i>Actions
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-file-excel me-2 text-success"></i>Export Excel</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-file-pdf me-2 text-danger"></i>Export PDF</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#" onclick="window.print()"><i class="fas fa-print me-2"></i>Print</a></li>
                            @if($purchaseOrder->status === 'Draft')
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('purchase-orders.destroy', $purchaseOrder) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this purchase order?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="fas fa-trash me-2"></i>Delete
                                        </button>
                                    </form>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Page body -->
<div class="page-body">
    <div class="container-fluid">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible" role="alert">
                <div class="d-flex">
                    <div><i class="fas fa-check-circle me-2"></i></div>
                    <div>{{ session('success') }}</div>
                </div>
                <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible" role="alert">
                <div class="d-flex">
                    <div><i class="fas fa-exclamation-circle me-2"></i></div>
                    <div>{{ session('error') }}</div>
                </div>
                <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
            </div>
        @endif

        <div class="row row-deck row-cards">
            <!-- PO Header -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-info-circle me-2"></i>Purchase Order Information
                        </h3>
                        <div class="card-actions">
                            @php
                                $statusClass = match($purchaseOrder->status) {
                                    'Draft' => 'secondary',
                                    'Pending' => 'warning',
                                    'Approved' => 'success',
                                    'Received' => 'info',
                                    'Cancelled' => 'danger',
                                    default => 'secondary'
                                };
                            @endphp
                            <span class="badge bg-{{ $statusClass }}">{{ $purchaseOrder->status }}</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td class="w-50"><strong>PO No:</strong></td>
                                        <td>{{ $purchaseOrder->po_no }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>PO Date:</strong></td>
                                        <td>{{ $purchaseOrder->po_date->format('d M Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Currency:</strong></td>
                                        <td>{{ $purchaseOrder->currency }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Created By:</strong></td>
                                        <td>{{ $purchaseOrder->creator->name ?? 'Unknown' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Created Date:</strong></td>
                                        <td>{{ $purchaseOrder->created_at->format('d M Y, h:i A') }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h5 class="mb-3"><i class="fas fa-truck me-2"></i>Supplier Information</h5>
                                <table class="table table-borderless">
                                    <tr>
                                        <td class="w-50"><strong>Name:</strong></td>
                                        <td>{{ $purchaseOrder->supplier->supplier_name }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Email:</strong></td>
                                        <td>{{ $purchaseOrder->supplier->email ?? 'Not provided' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Phone:</strong></td>
                                        <td>{{ $purchaseOrder->supplier->phone ?? 'Not provided' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Address:</strong></td>
                                        <td>{{ $purchaseOrder->supplier->address ?? 'Not provided' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Items -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-list me-2"></i>Items
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-vcenter">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Item Name</th>
                                        <th>Item Code</th>
                                        <th class="text-center">Quantity</th>
                                        <th class="text-end">Rate ({{ $purchaseOrder->currency }})</th>
                                        <th class="text-end">Amount ({{ $purchaseOrder->currency }})</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($purchaseOrder->items as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $item->item->item_name ?? 'Unknown Item' }}</strong>
                                        </td>
                                        <td>
                                            <span class="text-muted">{{ $item->item->oem_part_no ?? '-' }}</span>
                                        </td>
                                        <td class="text-center">{{ number_format($item->qty, 2) }}</td>
                                        <td class="text-end">{{ number_format($item->rate, 3) }}</td>
                                        <td class="text-end"><strong>{{ number_format($item->amount, 3) }}</strong></td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">No items found</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                                <tfoot class="table-light">
                                    <tr class="table-primary">
                                        <td colspan="5" class="text-end"><h5 class="mb-0">Total Amount:</h5></td>
                                        <td class="text-end"><h5 class="mb-0 text-success">{{ $purchaseOrder->currency }} {{ number_format($purchaseOrder->total_amount, 3) }}</h5></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Remarks -->
            @if($purchaseOrder->remarks)
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-comment-alt me-2"></i>Remarks
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="text-muted">{!! nl2br(e($purchaseOrder->remarks)) !!}</div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
@media print {
    .page-header, .btn-list, .card-actions { display: none !important; }
    .card { border: none !important; box-shadow: none !important; }
}
</style>
@endpush
