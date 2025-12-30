@extends('layouts.app')

@section('title', 'Invoice Details - ' . $invoice->invoice_no)

@section('content')
<div class="page-header d-print-none">
    <div class="container-fluid">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    <i class="fas fa-file-invoice me-2"></i>Sales Invoice Details
                </h2>
                <div class="text-muted mt-1">{{ $invoice->invoice_no }}</div>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Back to List
                    </a>
                    
                    <a href="{{ route('sales-return.create_from_invoice', $invoice->id) }}" class="btn btn-warning">
                        <i class="fas fa-undo me-1"></i>Sales Return
                    </a>
                    
                    <a href="{{ route('sales.print', $invoice->id) }}" class="btn btn-primary" target="_blank">
                        <i class="fas fa-print me-1"></i>Print
                    </a>
                    
                    <button type="button" class="btn btn-info" onclick="window.print()">
                        <i class="fas fa-print me-1"></i>Quick Print
                    </button>
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
            <!-- Invoice Header -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-info-circle me-2"></i>Invoice Information
                        </h3>
                        <div class="card-actions">
                            @php
                                $statusClass = match($invoice->invoice_status ?? 'final') {
                                    'performance' => 'warning',
                                    'final' => 'success',
                                    default => 'secondary'
                                };
                            @endphp
                            <span class="badge bg-{{ $statusClass }}">{{ ucfirst($invoice->invoice_status ?? 'Final') }}</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td class="text-muted" width="35%"><strong>Invoice No:</strong></td>
                                        <td>{{ $invoice->invoice_no }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted"><strong>Invoice Date:</strong></td>
                                        <td>{{ date('d-m-Y', strtotime($invoice->invoice_date)) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted"><strong>Customer:</strong></td>
                                        <td>{{ $invoice->customer_name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted"><strong>Phone:</strong></td>
                                        <td>{{ $invoice->phone ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted"><strong>Email:</strong></td>
                                        <td>{{ $invoice->email ?? '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td class="text-muted" width="35%"><strong>Reference No:</strong></td>
                                        <td>{{ $invoice->reference_no ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted"><strong>Status:</strong></td>
                                        <td>
                                            <span class="badge bg-{{ $statusClass }}">
                                                {{ ucfirst($invoice->invoice_status ?? 'Final') }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted"><strong>Created By:</strong></td>
                                        <td>{{ $invoice->creator_name ?? 'System' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted"><strong>Created At:</strong></td>
                                        <td>{{ date('d-m-Y H:i', strtotime($invoice->created_at)) }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        
                        @if($invoice->notes)
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <strong><i class="fas fa-sticky-note me-2"></i>Notes:</strong><br>
                                    {{ $invoice->notes }}
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Invoice Items -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-boxes me-2"></i>Invoice Items
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped mb-0">
                                <thead class="table-primary">
                                    <tr>
                                        <th style="width: 5%">#</th>
                                        <th style="width: 30%">Item Name</th>
                                        <th style="width: 15%">Part No</th>
                                        <th style="width: 10%" class="text-center">Unit</th>
                                        <th style="width: 10%" class="text-end">Quantity</th>
                                        <th style="width: 12%" class="text-end">Price (OMR)</th>
                                        <th style="width: 12%" class="text-end">Amount (OMR)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $total = 0; @endphp
                                    @foreach($items as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item->item_name ?? 'N/A' }}</td>
                                        <td>{{ $item->oem_part_no ?? '-' }}</td>
                                        <td class="text-center">{{ $item->unit ?? '-' }}</td>
                                        <td class="text-end">{{ number_format($item->qty, 2) }}</td>
                                        <td class="text-end">{{ number_format($item->sale_price, 3) }}</td>
                                        <td class="text-end">{{ number_format($item->amount, 3) }}</td>
                                    </tr>
                                    @php $total += $item->amount; @endphp
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <td colspan="6" class="text-end"><strong>Subtotal:</strong></td>
                                        <td class="text-end"><strong>{{ number_format($invoice->subtotal ?? $total, 3) }}</strong></td>
                                    </tr>
                                    @if($invoice->discount > 0)
                                    <tr>
                                        <td colspan="6" class="text-end">Discount:</td>
                                        <td class="text-end">{{ number_format($invoice->discount, 3) }}</td>
                                    </tr>
                                    @endif
                                    @if($invoice->vat_amount > 0)
                                    <tr>
                                        <td colspan="6" class="text-end">VAT (5%):</td>
                                        <td class="text-end">{{ number_format($invoice->vat_amount, 3) }}</td>
                                    </tr>
                                    @endif
                                    @if($invoice->other_charges > 0)
                                    <tr>
                                        <td colspan="6" class="text-end">Other Charges:</td>
                                        <td class="text-end">{{ number_format($invoice->other_charges, 3) }}</td>
                                    </tr>
                                    @endif
                                    <tr class="table-primary">
                                        <td colspan="6" class="text-end"><strong>Total Amount:</strong></td>
                                        <td class="text-end"><strong>{{ number_format($invoice->total_amount, 3) }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td colspan="6" class="text-end">Paid Amount:</td>
                                        <td class="text-end text-success">{{ number_format($invoice->paid_amount ?? 0, 3) }}</td>
                                    </tr>
                                    <tr class="table-warning">
                                        <td colspan="6" class="text-end"><strong>Due Amount:</strong></td>
                                        <td class="text-end"><strong>{{ number_format($invoice->total_amount - ($invoice->paid_amount ?? 0), 3) }}</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment History -->
            @if(count($payments) > 0)
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-money-bill-wave me-2"></i>Payment History
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped mb-0">
                                <thead class="table-primary">
                                    <tr>
                                        <th style="width: 10%">Date</th>
                                        <th style="width: 15%">Mode</th>
                                        <th style="width: 15%" class="text-end">Amount (OMR)</th>
                                        <th style="width: 20%">Transaction Ref</th>
                                        <th style="width: 40%">Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payments as $payment)
                                    <tr>
                                        <td>{{ date('d-m-Y', strtotime($payment->payment_date)) }}</td>
                                        <td>
                                            <span class="badge bg-info">{{ $payment->mode }}</span>
                                        </td>
                                        <td class="text-end">{{ number_format($payment->amount, 3) }}</td>
                                        <td>{{ $payment->transaction_ref ?? '-' }}</td>
                                        <td>{{ $payment->remarks ?? '-' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
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
    .page-header,
    .btn-list,
    .card-actions {
        display: none !important;
    }
}
</style>
@endpush
