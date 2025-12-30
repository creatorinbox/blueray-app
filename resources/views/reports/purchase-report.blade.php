@extends('layouts.app')

@section('title', 'Purchase Report')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">Purchase Report</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="#">Reports</a></li>
                            <li class="breadcrumb-item active">Purchase Report</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">Filter Options</h5>
                </div>
                <div class="card-body">
                    <form id="filter-form">
                        @csrf
                        <div class="row">
                            <div class="col-md-3">
                                <label for="from_date" class="form-label">From Date:</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                    <input type="date" class="form-control" id="from_date" name="from_date" 
                                           value="{{ date('Y-m-d', strtotime('-30 days')) }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label for="to_date" class="form-label">To Date:</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                    <input type="date" class="form-control" id="to_date" name="to_date" 
                                           value="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="supplier_id" class="form-label">Supplier:</label>
                                <select class="form-control select2" id="supplier_id" name="supplier_id" style="width: 100%;">
                                    <option value="">-All Suppliers-</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->supplier_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="button" id="show-report" class="btn btn-success w-100">
                                    <i class="fas fa-search"></i> Show Report
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Results -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
                    <h5 class="card-title mb-0">Purchase Report Data</h5>
                    <button type="button" id="export-excel" class="btn btn-success btn-sm">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover" id="purchase-report-table">
                            <thead class="table-primary">
                                <tr>
                                    <th style="width: 5%;">#</th>
                                    <th style="width: 12%;">Invoice No</th>
                                    <th style="width: 12%;">Purchase Date</th>
                                    <th style="width: 8%;">Supplier ID</th>
                                    <th style="width: 18%;">Supplier Name</th>
                                    <th style="width: 10%;">Amount</th>
                                       <th style="width: 10%">Per Quantity Price</th>
                                    <th style="width: 8%;">VAT</th>
                                    <th style="width: 10%;">Invoice Total</th>
                                    <th style="width: 10%;">Paid Amount</th>
                                    <th style="width: 7%;">Due Amount</th>
                                </tr>
                            </thead>
                            <tbody id="report-tbody">
                                <tr>
                                    <td colspan="10" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-search fa-2x mb-2"></i>
                                            <p>Click "Show Report" to load purchase data</p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot id="report-footer" style="display: none;">
                                <tr class="table-info fw-bold">
                                    <td colspan="5" class="text-right"><strong>TOTALS:</strong></td>
                                    <td id="total-amount">0.00</td>
                                       <td id="total-per-qty-price">0.00</td>
                                    <td id="total-vat">0.00</td>
                                    <td id="total-invoice">0.00</td>
                                    <td id="total-paid">0.00</td>
                                    <td id="total-due">0.00</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Modal -->
<div class="modal fade" id="loadingModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 mb-0">Generating Report...</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<style>
    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
        border-bottom: 2px solid #dee2e6;
    }
    
    .card-header {
        background: linear-gradient(45deg, #007bff, #0056b3);
    }
    
    .btn-success:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(0,123,255,0.1);
    }
    
    .select2-container {
        z-index: 1051;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize Select2
    $('#supplier_id').select2({
        theme: 'bootstrap-5',
        placeholder: '-All Suppliers-',
        allowClear: true
    });

    // Show Report Button Click
    $('#show-report').click(function() {
        generateReport();
    });

    // Generate Report Function
    function generateReport() {
        var fromDate = $('#from_date').val();
        var toDate = $('#to_date').val();
        var supplierId = $('#supplier_id').val();

        if (!fromDate || !toDate) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Please select both from and to dates'
            });
            return;
        }

        if (fromDate > toDate) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'From date cannot be greater than To date'
            });
            return;
        }

        // Show loading modal
        var loadingModal = new bootstrap.Modal(document.getElementById('loadingModal'));
        loadingModal.show();

        // Make AJAX request
        $.ajax({
            url: '{{ route("reports.purchase-report") }}',
            method: 'GET',
            data: {
                from_date: fromDate,
                to_date: toDate,
                supplier_id: supplierId
            },
            success: function(response) {
                loadingModal.hide();
                if (response.success) {
                    populateTable(response.data);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to generate report'
                    });
                }
            },
            error: function() {
                loadingModal.hide();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while generating the report'
                });
            }
        });
    }

    // Populate Table Function
    function populateTable(data) {
        var tbody = $('#report-tbody');
        var footer = $('#report-footer');
        tbody.empty();

        if (data.length === 0) {
            tbody.append(`
                <tr>
                    <td colspan="10" class="text-center py-4">
                        <div class="text-muted">
                            <i class="fas fa-inbox fa-2x mb-2"></i>
                            <p>No purchase records found for the selected criteria</p>
                        </div>
                    </td>
                </tr>
            `);
            footer.hide();
            return;
        }

        var totalAmount = 0, totalVat = 0, totalInvoice = 0, totalPaid = 0, totalDue = 0;

        $.each(data, function(index, item) {
            var amount = parseFloat(item.amount) || 0;
            var vat = parseFloat(item.vat) || 0;
            var invoiceTotal = parseFloat(item.invoice_total) || 0;
            var paidAmount = parseFloat(item.paid_amount) || 0;
            var dueAmount = parseFloat(item.due_amount) || 0;

            totalAmount += amount;
            totalVat += vat;
            totalInvoice += invoiceTotal;
            totalPaid += paidAmount;
            totalDue += dueAmount;

            tbody.append(`
                <tr>
                    <td>${index + 1}</td>
                    <td>${item.invoice_no || '-'}</td>
                    <td>${formatDate(item.purchase_date)}</td>
                    <td>${item.supplier_id || '-'}</td>
                    <td>${item.supplier_name || '-'}</td>
                    <td class="text-end">${formatCurrency(amount)}</td>
                        <td class="text-end">${perQtyPrice.toFixed(3)}</td>
                    <td class="text-end">${formatCurrency(vat)}</td>
                    <td class="text-end">${formatCurrency(invoiceTotal)}</td>
                    <td class="text-end">${formatCurrency(paidAmount)}</td>
                    <td class="text-end">${formatCurrency(dueAmount)}</td>
                </tr>
            `);
        });

        // Update footer totals
        $('#total-amount').text(formatCurrency(totalAmount));
        $('#total-vat').text(formatCurrency(totalVat));
        $('#total-invoice').text(formatCurrency(totalInvoice));
        $('#total-paid').text(formatCurrency(totalPaid));
        $('#total-due').text(formatCurrency(totalDue));
        footer.show();
    }

    // Export Excel Function
    $('#export-excel').click(function() {
        var table = document.getElementById('purchase-report-table');
        var wb = XLSX.utils.table_to_book(table, {sheet: "Purchase Report"});
        var fileName = 'Purchase-Report-' + $('#from_date').val() + '-to-' + $('#to_date').val() + '.xlsx';
        XLSX.writeFile(wb, fileName);
    });

    // Helper Functions
    function formatDate(dateString) {
        if (!dateString) return '-';
        var date = new Date(dateString);
        return date.toLocaleDateString('en-GB');
    }

    function formatCurrency(amount) {
        return new Intl.NumberFormat('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(amount);
    }

    // Load initial data
    generateReport();
});
</script>
@endpush