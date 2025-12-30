@extends('layouts.app')

@section('title', 'Quotation Details - ' . $quotation->quotation_no)

@section('content')
<div class="page-header d-print-none">
    <div class="container-fluid">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    <i class="fas fa-file-contract me-2"></i>Quotation Details
                </h2>
                <div class="text-muted mt-1">{{ $quotation->quotation_no }}</div>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="{{ route('quotations.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Back to List
                    </a>
                    
                    @if($quotation->approval_status === 'Draft')
                        <a href="{{ route('quotations.edit', $quotation) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-1"></i>Edit
                        </a>
                        <form action="{{ route('quotations.submit', $quotation) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success" onclick="return confirm('Submit for approval?')">
                                <i class="fas fa-paper-plane me-1"></i>Submit for Approval
                            </button>
                        </form>
                    @endif
                    
                    @if($quotation->approval_status === 'Submitted')
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveModal">
                            <i class="fas fa-check me-1"></i>Approve
                        </button>
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                            <i class="fas fa-times me-1"></i>Reject
                        </button>
                    @endif
                    
                    <div class="dropdown">
                        <button class="btn btn-info dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-cog me-1"></i>Actions
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-file-invoice me-2"></i>Proforma Invoice</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-file-invoice-dollar me-2"></i>Invoice Creation</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-file-excel me-2 text-success"></i>Export Excel</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-file-pdf me-2 text-danger"></i>Export PDF</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-file-word me-2 text-primary"></i>Export Word</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-envelope me-2"></i>Send Email</a></li>
                            <li><a class="dropdown-item" href="#" onclick="window.print()"><i class="fas fa-print me-2"></i>Print</a></li>
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
            <!-- Quotation Header -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-info-circle me-2"></i>Quotation Information
                        </h3>
                        <div class="card-actions">
                            @php
                                $statusClass = match($quotation->status) {
                                    'Draft' => 'secondary',
                                    'Sent' => 'primary',
                                    'Accepted' => 'success',
                                    'Rejected' => 'danger',
                                    'Expired' => 'warning',
                                    default => 'secondary'
                                };
                                $approvalClass = match($quotation->approval_status) {
                                    'Draft' => 'secondary',
                                    'Submitted' => 'warning',
                                    'Approved' => 'success',
                                    'Rejected' => 'danger',
                                    default => 'secondary'
                                };
                            @endphp
                            <span class="badge bg-{{ $statusClass }} me-2">{{ $quotation->status }}</span>
                            <span class="badge bg-{{ $approvalClass }}">{{ $quotation->approval_status }}</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td class="w-50"><strong>Quotation No:</strong></td>
                                        <td>{{ $quotation->quotation_no }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Date:</strong></td>
                                        <td>{{ $quotation->quotation_date->format('d M Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Created By:</strong></td>
                                        <td>{{ $quotation->creator->name ?? 'Unknown' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Created Date:</strong></td>
                                        <td>{{ $quotation->created_at->format('d M Y, h:i A') }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h5 class="mb-3"><i class="fas fa-user me-2"></i>Customer Information</h5>
                                <table class="table table-borderless">
                                    <tr>
                                        <td class="w-50"><strong>Name:</strong></td>
                                        <td>{{ $quotation->customer->customer_name }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Email:</strong></td>
                                        <td>{{ $quotation->customer->email ?? 'Not provided' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Phone:</strong></td>
                                        <td>{{ $quotation->customer->phone ?? 'Not provided' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Address:</strong></td>
                                        <td>{{ $quotation->customer->address ?? 'Not provided' }}</td>
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
                                        <th>Item</th>
                                        <th>Supplier</th>
                                        <th>Description</th>
                                        <th class="text-center">Quantity</th>
                                        <th class="text-end">Rate (OMR)</th>
                                        <th class="text-center">VAT %</th>
                                        <th class="text-end">VAT Amount (OMR)</th>
                                        <th class="text-end">Total (OMR)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($quotation->items as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $item->item->item_name ?? 'Unknown Item' }}</strong>
                                            <div class="text-muted text-sm">{{ $item->item->item_code ?? '' }}</div>
                                        </td>
                                        <td>
                                            <span class="text-muted">{{ $item->item->supplier->supplier_name ?? '-' }}</span>
                                        </td>
                                        <td>{{ $item->description ?: '-' }}</td>
                                        <td class="text-center">{{ number_format($item->qty, 2) }}</td>
                                        <td class="text-end">{{ number_format($item->rate, 2) }}</td>
                                        <td class="text-center">{{ $item->vat_rate }}%</td>
                                        <td class="text-end">{{ number_format($item->vat_amount, 2) }}</td>
                                        <td class="text-end"><strong>{{ number_format($item->total_amount, 2) }}</strong></td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted">No items found</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <td colspan="8" class="text-end"><strong>Subtotal:</strong></td>
                                        <td class="text-end"><strong>OMR {{ number_format($quotation->subtotal ?? 0, 2) }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td colspan="8" class="text-end"><strong>VAT (5%):</strong></td>
                                        <td class="text-end"><strong>OMR {{ number_format($quotation->vat_amount ?? 0, 2) }}</strong></td>
                                    </tr>
                                    <tr class="table-primary">
                                        <td colspan="8" class="text-end"><h5 class="mb-0">Total Amount:</h5></td>
                                        <td class="text-end"><h5 class="mb-0 text-success">OMR {{ number_format($quotation->total_amount, 2) }}</h5></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Terms & Conditions -->
                @if($quotation->terms_conditions)
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-file-contract me-2"></i>Terms & Conditions
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="text-muted">{!! nl2br(e($quotation->terms_conditions)) !!}</div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Approval History -->
                <div class="col-md-{{ $quotation->terms_conditions ? '6' : '12' }}">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-history me-2"></i>Approval History
                            </h3>
                        </div>
                        <div class="card-body">
                            @forelse($approvalLogs as $log)
                            <div class="d-flex align-items-center mb-3">
                                <div class="me-3">
                                    @php
                                        $iconClass = match($log->action) {
                                            'Created' => 'fas fa-plus-circle text-primary',
                                            'Submitted' => 'fas fa-paper-plane text-warning',
                                            'Approved' => 'fas fa-check-circle text-success',
                                            'Rejected' => 'fas fa-times-circle text-danger',
                                            default => 'fas fa-info-circle text-info'
                                        };
                                    @endphp
                                    <i class="{{ $iconClass }}"></i>
                                </div>
                                <div class="flex-fill">
                                    <div>
                                        <strong>{{ $log->action }}</strong> by {{ $log->actionBy->name ?? 'Unknown' }}
                                    </div>
                                    <div class="text-muted text-sm">
                                        {{ \Carbon\Carbon::parse($log->action_date)->format('d M Y, h:i A') }}
                                    </div>
                                    @if($log->remarks)
                                        <div class="text-muted text-sm mt-1">{{ $log->remarks }}</div>
                                    @endif
                                </div>
                            </div>
                            @empty
                            <div class="text-center text-muted">
                                <i class="fas fa-history fa-2x mb-2 opacity-50"></i>
                                <p>No approval history found</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('quotations.approve', $quotation) }}" method="POST">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="approveModalLabel">
                        <i class="fas fa-check-circle me-2"></i>Approve Quotation
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Are you sure you want to approve this quotation?
                    </div>
                    <div class="mb-3">
                        <label for="approve_remarks" class="form-label">Remarks (Optional)</label>
                        <textarea class="form-control" id="approve_remarks" name="remarks" rows="3" placeholder="Add any approval remarks..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-1"></i>Approve Quotation
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('quotations.reject', $quotation) }}" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="rejectModalLabel">
                        <i class="fas fa-times-circle me-2"></i>Reject Quotation
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Please provide a reason for rejection.
                    </div>
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="rejection_reason" name="reason" rows="4" placeholder="Enter rejection reason..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times-circle me-1"></i>Reject Quotation
                    </button>
                </div>
            </form>
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