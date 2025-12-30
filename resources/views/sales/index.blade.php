@extends('layouts.app')

@section('title', 'Sales Invoices')

@push('styles')
<link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/fixedheader/3.4.0/css/fixedHeader.bootstrap5.min.css" rel="stylesheet">
@endpush

@section('content')
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-file-invoice me-2"></i>Sales Invoices
        <small class="text-muted d-block mt-1">View/Search Sold Items</small>
    </h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item active">Sales Invoices</li>
        </ol>
    </nav>
</div>

<!-- Statistics Boxes -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-0">{{ $stats['total_invoices'] }}</h3>
                        <p class="mb-0">Total Invoices</p>
                    </div>
                    <div class="text-white-50">
                        <i class="fas fa-file-invoice fa-3x"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-top-0">
                <a href="{{ route('sales.index') }}" class="text-white text-decoration-none">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-0">OMR {{ number_format($stats['total_amount'], 3) }}</h3>
                        <p class="mb-0">Total Invoices Amount</p>
                    </div>
                    <div class="text-white-50">
                        <i class="fas fa-money-bill-wave fa-3x"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-top-0">
                <a href="{{ route('sales.index') }}" class="text-white text-decoration-none">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-0">OMR {{ number_format($stats['total_paid'], 3) }}</h3>
                        <p class="mb-0">Total Received Amount</p>
                    </div>
                    <div class="text-white-50">
                        <i class="fas fa-hand-holding-usd fa-3x"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-top-0">
                <a href="{{ route('sales.index') }}" class="text-white text-decoration-none">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-0">OMR {{ number_format($stats['total_due'], 3) }}</h3>
                        <p class="mb-0">Total Sales Due</p>
                    </div>
                    <div class="text-white-50">
                        <i class="fas fa-hourglass-half fa-3x"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-top-0">
                <a href="{{ route('sales.index') }}" class="text-white text-decoration-none">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Alert Messages -->
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

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title mb-0">
            <i class="fas fa-list me-2"></i>Sales Invoices List
        </h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover" id="salesTable" style="width:100%">
                <thead class="table-primary">
                    <tr>
                        <th class="text-center" style="width:3%"><input type="checkbox" id="select_all"></th>
                        <th style="width:10%">Sales Date</th>
                        <th style="width:12%">Invoice No</th>
                        <th style="width:10%">Status</th>
                        <th style="width:15%">Customer</th>
                        <th style="width:10%" class="text-end">Total (OMR)</th>
                        <th style="width:10%" class="text-end">Paid (OMR)</th>
                        <th style="width:10%" class="text-end">Due (OMR)</th>
                        <th style="width:10%">Payment Status</th>
                        <th style="width:10%">Delivery Status</th>
                        <th style="width:10%">Created By</th>
                        <th style="width:10%" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoices as $invoice)
                    <tr>
                        <td class="text-center">
                            <input type="checkbox" class="single_check" value="{{ $invoice->id }}">
                        </td>
                        <td>{{ date('d-m-Y', strtotime($invoice->invoice_date)) }}</td>
                        <td><strong>{{ $invoice->invoice_no }}</strong></td>
                        <td>
                            @php
                                $statusBadges = [
                                    'performance' => 'warning',
                                    'final' => 'success',
                                ];
                                $badgeClass = $statusBadges[$invoice->invoice_status] ?? 'secondary';
                            @endphp
                            <span class="badge bg-{{ $badgeClass }}">{{ ucfirst($invoice->invoice_status ?? 'N/A') }}</span>
                        </td>
                        <td>
                            <i class="fas fa-user me-1"></i>{{ $invoice->customer_name ?? 'N/A' }}
                        </td>
                        <td class="text-end"><strong>{{ number_format($invoice->total_amount, 3) }}</strong></td>
                        <td class="text-end">{{ number_format($invoice->paid_amount, 3) }}</td>
                        <td class="text-end">{{ number_format($invoice->total_amount - $invoice->paid_amount, 3) }}</td>
                        <td>
                            @php
                                $paidAmount = $invoice->paid_amount;
                                $totalAmount = $invoice->total_amount;
                                $paymentBadge = 'danger';
                                $paymentStatus = 'Unpaid';
                                
                                if ($paidAmount >= $totalAmount) {
                                    $paymentBadge = 'success';
                                    $paymentStatus = 'Paid';
                                } elseif ($paidAmount > 0) {
                                    $paymentBadge = 'warning';
                                    $paymentStatus = 'Partial';
                                }
                            @endphp
                            <span class="badge bg-{{ $paymentBadge }}">{{ $paymentStatus }}</span>
                        </td>
                        <td>
                            @php
                                $deliveryStatus = $invoice->delivery_status ?? 'Pending';
                                $deliveryBadge = match($deliveryStatus) {
                                    'Approved' => 'success',
                                    'Delivered' => 'info',
                                    default => 'warning'
                                };
                            @endphp
                            @if($deliveryStatus === 'Pending')
                                <button type="button" 
                                        class="btn btn-sm btn-warning"
                                        onclick="approveDelivery({{ $invoice->id }}, '{{ $invoice->invoice_no }}')">
                                    <i class="fas fa-truck me-1"></i>Approve Delivery
                                </button>
                            @else
                                <span class="badge bg-{{ $deliveryBadge }}">{{ $deliveryStatus }}</span>
                            @endif
                        </td>
                        <td>{{ $invoice->creator_name ?? 'System' }}</td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('sales.show', $invoice->id) }}" 
                                   class="btn btn-info" 
                                   title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                <button type="button" 
                                        class="btn btn-primary" 
                                        title="Create Delivery Order"
                                        onclick="window.location='{{ route('delivery_orders.create', ['invoice_id' => $invoice->id]) }}'">
                                    <i class="fas fa-truck"></i>
                                </button>

                                <button type="button" 
                                        class="btn btn-success" 
                                        onclick="printInvoice({{ $invoice->id }})"
                                        title="Print">
                                    <i class="fas fa-print"></i>
                                </button>

                                <a href="#" 
                                   class="btn btn-warning" 
                                   title="Email">
                                    <i class="fas fa-envelope"></i>
                                </a>
                                
                                @if(isset($invoice->invoice_status) && $invoice->invoice_status === 'Pending')
                                <form action="{{ route('sales.destroy', $invoice->id) }}" 
                                      method="POST" 
                                      class="d-inline"
                                      onsubmit="return confirm('Are you sure you want to delete this invoice?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-secondary">
                    <tr>
                        <th colspan="5" class="text-end"><strong>Total:</strong></th>
                        <th class="text-end"><strong id="footer_total">0.000</strong></th>
                        <th class="text-end"><strong id="footer_paid">0.000</strong></th>
                        <th class="text-end"><strong id="footer_due">0.000</strong></th>
                        <th colspan="3"></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/fixedheader/3.4.0/js/dataTables.fixedHeader.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize DataTable
    var table = $('#salesTable').DataTable({
        dom: '<"row mb-3"<"col-sm-12"<"float-start"l><"float-end"f><"float-end me-2"B>>>tip',
        buttons: [
            {
                extend: 'copy',
                className: 'btn btn-sm btn-secondary',
                text: '<i class="fas fa-copy"></i> Copy',
                exportOptions: { columns: [1,2,3,4,5,6,7,8,9] }
            },
            {
                extend: 'excel',
                className: 'btn btn-sm btn-success',
                text: '<i class="fas fa-file-excel"></i> Excel',
                exportOptions: { columns: [1,2,3,4,5,6,7,8,9] }
            },
            {
                extend: 'pdf',
                className: 'btn btn-sm btn-danger',
                text: '<i class="fas fa-file-pdf"></i> PDF',
                exportOptions: { columns: [1,2,3,4,5,6,7,8,9] }
            },
            {
                extend: 'print',
                className: 'btn btn-sm btn-info',
                text: '<i class="fas fa-print"></i> Print',
                exportOptions: { columns: [1,2,3,4,5,6,7,8,9] }
            },
            {
                extend: 'csv',
                className: 'btn btn-sm btn-warning',
                text: '<i class="fas fa-file-csv"></i> CSV',
                exportOptions: { columns: [1,2,3,4,5,6,7,8,9] }
            }
        ],
        order: [[1, 'desc']],
        pageLength: 25,
        responsive: true,
        columnDefs: [
            {
                targets: 0,
                orderable: false,
                className: 'text-center'
            },
            {
                targets: 11,
                orderable: false
            }
        ],
        footerCallback: function(row, data, start, end, display) {
            var api = this.api();
            
            var intVal = function(i) {
                return typeof i === 'string' ?
                    parseFloat(i.replace(/[\$,]/g, '')) || 0 :
                    typeof i === 'number' ? i : 0;
            };
            
            // Total over current page
            var total = api
                .column(5, {page: 'current'})
                .data()
                .reduce(function(a, b) {
                    return intVal(a) + intVal(b);
                }, 0);
            
            var paid = api
                .column(6, {page: 'current'})
                .data()
                .reduce(function(a, b) {
                    return intVal(a) + intVal(b);
                }, 0);
            
            var due = api
                .column(7, {page: 'current'})
                .data()
                .reduce(function(a, b) {
                    return intVal(a) + intVal(b);
                }, 0);
            
            // Update footer
            $('#footer_total').html(total.toFixed(3));
            $('#footer_paid').html(paid.toFixed(3));
            $('#footer_due').html(due.toFixed(3));
        }
    });
    
    // Fixed Header
    new $.fn.dataTable.FixedHeader(table);
    
    // Select all checkbox
    $('#select_all').on('click', function() {
        $('.single_check').prop('checked', this.checked);
    });
    
    // Individual checkbox
    $(document).on('click', '.single_check', function() {
        if ($('.single_check:checked').length === $('.single_check').length) {
            $('#select_all').prop('checked', true);
        } else {
            $('#select_all').prop('checked', false);
        }
    });
});

function printInvoice(id) {
    window.open('/sales/' + id + '/print', '_blank', 'width=800,height=600');
}

function approveDelivery(invoiceId, invoiceNo) {
    if (confirm('Are you sure you want to approve delivery for Invoice ' + invoiceNo + '?')) {
        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/sales/' + invoiceId + '/approve-delivery';
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush
