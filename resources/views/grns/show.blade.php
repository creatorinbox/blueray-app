@extends('layouts.app')

@section('title', 'GRN Details - ' . $grn->grn_no)

@section('content')
<div class="page-header d-print-none">
    <div class="container-fluid">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    <i class="fas fa-file-import me-2"></i>GRN Details
                </h2>
                <div class="text-muted mt-1">{{ $grn->grn_no }}</div>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="{{ route('grns.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Back to List
                    </a>
                    
                    @if($grn->purchaseOrder)
                        <a href="{{ route('purchase-orders.show', $grn->purchaseOrder) }}" class="btn btn-info">
                            <i class="fas fa-file-invoice me-1"></i>View PO
                        </a>
                    @endif
                    
                    <div class="dropdown">
                        <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-cog me-1"></i>Actions
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-file-excel me-2 text-success"></i>Export Excel</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-file-pdf me-2 text-danger"></i>Export PDF</a></li>
                            <li><hr class="dropdown-divider"></li>
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
            <!-- GRN Header -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-info-circle me-2"></i>GRN Information
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td class="w-50"><strong>GRN No:</strong></td>
                                        <td>{{ $grn->grn_no }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>GRN Date:</strong></td>
                                        <td>{{ $grn->grn_date->format('d M Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Invoice No:</strong></td>
                                        <td>{{ $grn->invoice_no }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Currency:</strong></td>
                                        <td>{{ $grn->currency }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Exchange Rate:</strong></td>
                                        <td>{{ number_format($grn->exchange_rate, 3) }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Created By:</strong></td>
                                        <td>{{ $grn->creator->name ?? 'Unknown' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Created Date:</strong></td>
                                        <td>{{ $grn->created_at->format('d M Y, h:i A') }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h5 class="mb-3"><i class="fas fa-file-invoice me-2"></i>Purchase Order Information</h5>
                                @if($grn->purchaseOrder)
                                <table class="table table-borderless">
                                    <tr>
                                        <td class="w-50"><strong>PO No:</strong></td>
                                        <td>{{ $grn->purchaseOrder->po_no }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>PO Date:</strong></td>
                                        <td>{{ $grn->purchaseOrder->po_date->format('d M Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Supplier:</strong></td>
                                        <td>{{ $grn->purchaseOrder->supplier->supplier_name }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Supplier Email:</strong></td>
                                        <td>{{ $grn->purchaseOrder->supplier->email ?? 'Not provided' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Supplier Phone:</strong></td>
                                        <td>{{ $grn->purchaseOrder->supplier->phone ?? 'Not provided' }}</td>
                                    </tr>
                                </table>
                                @else
                                <p class="text-muted">No Purchase Order linked</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Items Received -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-boxes me-2"></i>Items Received
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-vcenter">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Item Name</th>
                                        <th>Lot No</th>
                                        <th>Expiry Date</th>
                                        <th class="text-center">Qty Received</th>
                                        <th class="text-end">Base Cost (OMR)</th>
                                        <th class="text-end">Duty (OMR)</th>
                                        <th class="text-end">Freight (OMR)</th>
                                        <th class="text-end">Landed Cost/Unit (OMR)</th>
                                        <th class="text-end">Total Landed Cost (OMR)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($grn->items as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $item->item->item_name ?? 'Unknown Item' }}</strong><br>
                                            <small class="text-muted">{{ $item->item->item_code ?? '-' }}</small>
                                        </td>
                                        <td>
                                            @if($item->stockLot)
                                                <span class="badge bg-info">{{ $item->stockLot->lot_no }}</span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if($item->stockLot && $item->stockLot->expiry_date)
                                                {{ $item->stockLot->expiry_date->format('d M Y') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="text-center">{{ number_format($item->qty_received, 2) }}</td>
                                        <td class="text-end">{{ number_format($item->base_cost, 3) }}</td>
                                        <td class="text-end">{{ number_format($item->duty_amount ?? 0, 3) }}</td>
                                        <td class="text-end">{{ number_format($item->freight_amount ?? 0, 3) }}</td>
                                        <td class="text-end"><strong>{{ number_format($item->landed_cost_per_unit, 3) }}</strong></td>
                                        <td class="text-end"><strong>{{ number_format($item->total_landed_cost, 3) }}</strong></td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="10" class="text-center text-muted">No items found</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                                <tfoot class="table-light">
                                    <tr class="table-primary">
                                        <td colspan="9" class="text-end"><h5 class="mb-0">Total Landed Cost:</h5></td>
                                        <td class="text-end"><h5 class="mb-0 text-success">OMR {{ number_format($grn->total_landed_cost ?? 0, 3) }}</h5></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Remarks -->
            @if($grn->remarks)
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-comment-alt me-2"></i>Remarks
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="text-muted">{!! nl2br(e($grn->remarks)) !!}</div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('payments')
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-money-check-alt me-2"></i>Payments</h3>
            </div>
            <div class="card-body">
                <h5>Previous Payments</h5>
                <div class="table-responsive mb-3">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr class="bg-primary text-white">
                                <th>#</th>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Paid Status</th>
                                <th>Cheque No</th>
                                <th>Cheque Date</th>
                                <th>Note</th>
                                <th class="text-end">Amount</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payments as $i => $payment)
                            <tr>
                                <td>{{ $i+1 }}</td>
                                <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}</td>
                                <td>{{ $payment->payment_type }}</td>
                                <td>{{ $payment->paid_status ?? '-' }}</td>
                                <td>{{ $payment->cheque_no ?? '-' }}</td>
                                <td>{{ $payment->cheque_date ? \Carbon\Carbon::parse($payment->cheque_date)->format('d M Y') : '-' }}</td>
                                <td>{{ $payment->payment_note ?? '-' }}</td>
                                <td class="text-end">{{ number_format($payment->amount, 3) }}</td>
                                <td>
                                    <form method="POST" action="{{ route('grns.payments.delete', [$grn->id, $payment->id]) }}" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this payment?')"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="9" class="text-center text-muted">No payments found</td></tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="table-light">
                                <td colspan="7" class="text-end"><strong>Total Paid:</strong></td>
                                <td class="text-end"><strong>{{ number_format($total_paid, 3) }}</strong></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <h5>Add Payment</h5>
                <form method="POST" action="{{ route('grns.payments.store', $grn->id) }}">
                    @csrf
                    <div class="row g-2 align-items-end">
                        <div class="col-md-2">
                            <label for="payment_date" class="form-label">Date</label>
                            <input type="date" name="payment_date" id="payment_date" class="form-control" value="{{ old('payment_date', date('Y-m-d')) }}" required>
                        </div>
                        <div class="col-md-2">
                            <label for="amount" class="form-label">Amount</label>
                            <input type="number" step="0.001" min="0.01" name="amount" id="amount" class="form-control" value="{{ old('amount') }}" required>
                        </div>
                        <div class="col-md-2">
                            <label for="payment_type" class="form-label">Type</label>
                            <select name="payment_type" id="payment_type" class="form-select" required>
                                <option value="">-Select-</option>
                                <option value="Cash">Cash</option>
                                <option value="Bank">Bank</option>
                                <option value="Card">Card</option>
                                <option value="Cheque">Cheque</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="paid_status" class="form-label">Paid Status</label>
                            <select name="paid_status" id="paid_status" class="form-select">
                                <option value="">-Select-</option>
                                <option value="Not Paid">Not Paid</option>
                                <option value="Paid">Paid</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="cheque_no" class="form-label">Cheque No</label>
                            <input type="text" name="cheque_no" id="cheque_no" class="form-control" value="{{ old('cheque_no') }}">
                        </div>
                        <div class="col-md-2">
                            <label for="cheque_date" class="form-label">Cheque Date</label>
                            <input type="date" name="cheque_date" id="cheque_date" class="form-control" value="{{ old('cheque_date') }}">
                        </div>
                        <div class="col-md-12 mt-2">
                            <label for="payment_note" class="form-label">Note</label>
                            <input type="text" name="payment_note" id="payment_note" class="form-control" value="{{ old('payment_note') }}">
                        </div>
                        <div class="col-md-12 mt-2">
                            <button type="submit" class="btn btn-success"><i class="fas fa-plus me-1"></i>Add Payment</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Show/hide cheque fields based on payment type
    document.addEventListener('DOMContentLoaded', function() {
        var paymentType = document.getElementById('payment_type');
        var chequeNo = document.getElementById('cheque_no');
        var chequeDate = document.getElementById('cheque_date');
        if (paymentType && chequeNo && chequeDate) {
            function toggleChequeFields() {
                var type = paymentType.value;
                chequeNo.disabled = (type !== 'Cheque');
                chequeDate.disabled = (type !== 'Cheque');
            }
            paymentType.addEventListener('change', toggleChequeFields);
            toggleChequeFields();
        }
    });
</script>
@endpush
@push('styles')
<style>
@media print {
    .page-header, .btn-list { display: none !important; }
    .card { border: none !important; box-shadow: none !important; }
}
</style>
@endpush
