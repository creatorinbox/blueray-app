@extends('layouts.app')

@section('title', 'Quotations')

@push('styles')
<link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/fixedheader/3.4.0/css/fixedHeader.bootstrap5.min.css" rel="stylesheet">
@endpush

@section('content')
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-file-contract me-2"></i>Quotations
        <small class="text-muted d-block mt-1">View/Search Quotations</small>
    </h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item active">Quotations</li>
        </ol>
    </nav>
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
            <i class="fas fa-list me-2"></i>Quotations List
        </h3>
        <a href="{{ route('quotations.create') }}" class="btn btn-info">
            <i class="fas fa-plus me-2"></i>New Quotation
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover" id="quotationsTable" style="width:100%">
                <thead class="table-primary">
                    <tr>
                        <th class="text-center" style="width:3%"><input type="checkbox" id="select_all"></th>
                        <th style="width:10%">Date</th>
                        <th style="width:12%">Quotation No</th>
                        <th style="width:10%">Status</th>
                        <th style="width:12%">Subject</th>
                        <th style="width:15%">Customer</th>
                        <th style="width:12%">Item Name</th>
                        <th style="width:12%" class="text-end">Total (OMR)</th>
                        <th style="width:10%">Created By</th>
                        <th style="width:8%" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($quotations as $quotation)
                    <tr>
                        <td class="text-center">
                            <input type="checkbox" class="single_check" value="{{ $quotation->id }}">
                        </td>
                        <td>{{ $quotation->quotation_date->format('d-m-Y') }}</td>
                        <td><strong>{{ $quotation->quotation_no }}</strong></td>
                        <td>
                            @php
                                $badges = [
                                    'Draft' => 'secondary',
                                    'Submitted' => 'warning',
                                    'Approved' => 'success',
                                    'Rejected' => 'danger'
                                ];
                                $badgeClass = $badges[$quotation->approval_status] ?? 'light';
                            @endphp
                            <span class="badge bg-{{ $badgeClass }}">{{ $quotation->approval_status }}</span>
                        </td>
                        <td>{{ $quotation->reference_no ?? '-' }}</td>
                        <td>
                            <i class="fas fa-user me-1"></i>{{ $quotation->customer->customer_name ?? 'N/A' }}
                        </td>
                        <td>
                            <small class="text-muted">
                                @php
                                    $itemNames = $quotation->items->pluck('item.item_name')->filter()->unique();
                                @endphp
                                {{ $itemNames->count() > 0 ? $itemNames->implode(', ') : '-' }}
                            </small>
                        </td>
                        <td class="text-end"><strong>{{ number_format($quotation->total_amount, 3) }}</strong></td>
                        <td>{{ $quotation->creator->name ?? 'System' }}</td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('quotations.show', $quotation) }}" 
                                   class="btn btn-info" 
                                   title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                @if($quotation->approval_status === 'Draft')
                                <a href="{{ route('quotations.edit', $quotation) }}" 
                                   class="btn btn-primary" 
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endif

                                <!-- Action Dropdown -->
                                <div class="btn-group" role="group">
                                    <button type="button" 
                                            class="btn btn-success dropdown-toggle" 
                                            data-bs-toggle="dropdown" 
                                            aria-expanded="false"
                                            title="Actions">
                                        <i class="fas fa-cog"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        @if($quotation->approval_status === 'Approved')
                                        <li>
                                            <a class="dropdown-item" href="{{ url('sales/invoice/' . $quotation->id . '?statusq=performance') }}">
                                                <i class="fas fa-file-invoice me-2 text-warning"></i>Create Performance Invoice
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ url('sales/invoice/' . $quotation->id . '?statusq=final') }}">
                                                <i class="fas fa-file-invoice-dollar me-2 text-success"></i>Create Invoice
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        @endif
                                        <li>
                                            <a class="dropdown-item" href="javascript:void(0)" onclick="printQuotation({{ $quotation->id }})">
                                                <i class="fas fa-print me-2 text-primary"></i>Print
                                            </a>
                                        </li>
                                    </ul>
                                </div>

                                <a href="#" 
                                   class="btn btn-warning" 
                                   title="Email">
                                    <i class="fas fa-envelope"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-secondary">
                    <tr>
                        <th colspan="7" class="text-end"><strong>Total:</strong></th>
                        <th class="text-end"><strong id="footer_total">0.000</strong></th>
                        <th colspan="2"></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<!-- Approval/Rejection Modal -->
<div class="modal fade" id="actionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="actionModalTitle">Confirm Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="actionForm">
                    @csrf
                    <div class="mb-3" id="reasonGroup" style="display:none;">
                        <label for="reason" class="form-label">Reason</label>
                        <textarea class="form-control" id="reason" name="reason" rows="3" required></textarea>
                    </div>
                    <div class="mb-3" id="remarksGroup" style="display:none;">
                        <label for="remarks" class="form-label">Remarks</label>
                        <textarea class="form-control" id="remarks" name="remarks" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmAction">Confirm</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
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
    var table = $('#quotationsTable').DataTable({
        dom: '<"row mb-3"<"col-sm-12"<"float-start"l><"float-end"f><"float-end me-2"B>>>tip',
        buttons: [
            {
                extend: 'copy',
                className: 'btn btn-sm btn-secondary',
                text: '<i class="fas fa-copy"></i> Copy',
                exportOptions: { columns: [1,2,3,4,5,6,7,8] }
            },
            {
                extend: 'excel',
                className: 'btn btn-sm btn-success',
                text: '<i class="fas fa-file-excel"></i> Excel',
                exportOptions: { columns: [1,2,3,4,5,6,7,8] }
            },
            {
                extend: 'pdf',
                className: 'btn btn-sm btn-danger',
                text: '<i class="fas fa-file-pdf"></i> PDF',
                exportOptions: { columns: [1,2,3,4,5,6,7,8] }
            },
            {
                extend: 'print',
                className: 'btn btn-sm btn-info',
                text: '<i class="fas fa-print"></i> Print',
                exportOptions: { columns: [1,2,3,4,5,6,7,8] }
            },
            {
                extend: 'csv',
                className: 'btn btn-sm btn-warning',
                text: '<i class="fas fa-file-csv"></i> CSV',
                exportOptions: { columns: [1,2,3,4,5,6,7,8] }
            }
        ],
        order: [[1, 'desc']],
        pageLength: 25,
        responsive: true,
        search: {
            caseInsensitive: true
        },
        language: {
            processing: '<div class="text-primary">Processing...</div>'
        },
        columnDefs: [
            {
                targets: 0,
                orderable: false,
                className: 'text-center'
            }
        ],
        footerCallback: function(row, data, start, end, display) {
            var api = this.api();
            
            // Remove formatting to get numeric data for summation
            var intVal = function(i) {
                return typeof i === 'string' ?
                    parseFloat(i.replace(/[\$,]/g, '')) || 0 :
                    typeof i === 'number' ? i : 0;
            };
            
            // Total over current page
            var total = api
                .column(7, {page: 'current'})
                .data()
                .reduce(function(a, b) {
                    return intVal(a) + intVal(b);
                }, 0);
            
            // Update footer
            $('#footer_total').html(total.toFixed(3));
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

function printQuotation(id) {
    window.open('/quotations/' + id + '/print', '_blank', 'width=800,height=600');
}


    function approveQuotation(id) {
        showActionModal('Approve Quotation', 'approve', id, false);
    }

    function rejectQuotation(id) {
        showActionModal('Reject Quotation', 'reject', id, true);
    }

    function showActionModal(title, action, id, showReason) {
        $('#actionModalTitle').text(title);
        $('#actionForm')[0].reset();
        
        if (showReason) {
            $('#reasonGroup').show();
            $('#remarksGroup').hide();
            $('#confirmAction').removeClass('btn-success').addClass('btn-danger');
        } else {
            $('#reasonGroup').hide();
            $('#remarksGroup').show();
            $('#confirmAction').removeClass('btn-danger').addClass('btn-success');
        }
        
        $('#confirmAction').off('click').on('click', function() {
            performAction(action, id);
        });
        
        $('#actionModal').modal('show');
    }

    function performAction(action, id) {
        const formData = new FormData($('#actionForm')[0]);
        const url = `/quotations/${id}/${action}`;
        
        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#actionModal').modal('hide');
                location.reload();
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                alert(response.error || 'An error occurred');
            }
        });
    }
</script>
@endpush