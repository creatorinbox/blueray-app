@extends('layouts.app')

@section('title', 'Purchase Payment Report')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">Purchase Payment Report</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="#">Reports</a></li>
                            <li class="breadcrumb-item active">Purchase Payment Report</li>
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
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">Filter Options</h5>
                </div>
                <div class="card-body">
                    <form id="filter-form">
                        @csrf
                        <div class="row">
                            <div class="col-md-2">
                                <label for="from_date" class="form-label">From Date:</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                    <input type="date" class="form-control" id="from_date" name="from_date" 
                                           value="{{ date('Y-m-d', strtotime('-30 days')) }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label for="to_date" class="form-label">To Date:</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                    <input type="date" class="form-control" id="to_date" name="to_date" 
                                           value="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label for="supplier_id" class="form-label">Supplier:</label>
                                <select class="form-control select2" id="supplier_id" name="supplier_id" style="width: 100%;">
                                    <option value="">-All Suppliers-</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->supplier_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="payment_type" class="form-label">Payment Type:</label>
                                <select class="form-control select2" id="payment_type" name="payment_type" style="width: 100%;">
                                    <option value="">-All Payment Types-</option>
                                    @foreach($paymentModes as $mode)
                                        <option value="{{ $mode }}">{{ ucfirst($mode) }}</option>
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

    <!-- Report Results with Tabs -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">Payment Report Data</h5>
                </div>
                <div class="card-body p-0">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" id="reportTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="purchase-payments-tab" data-bs-toggle="tab" 
                                    data-bs-target="#purchase-payments" type="button" role="tab">
                                <i class="fas fa-shopping-cart me-2"></i>Purchase Payments
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="supplier-payments-tab" data-bs-toggle="tab" 
                                    data-bs-target="#supplier-payments" type="button" role="tab">
                                <i class="fas fa-users me-2"></i>Supplier Payments
                            </button>
                        </li>
                    </ul>

                    <!-- Tab content -->
                    <div class="tab-content" id="reportTabContent">
                        <!-- Purchase Payments Tab -->
                        <div class="tab-pane fade show active" id="purchase-payments" role="tabpanel">
                            <div class="p-3">
                                <div class="d-flex justify-content-end mb-3">
                                    <button type="button" id="export-purchase-payments" class="btn btn-success btn-sm">
                                        <i class="fas fa-file-excel"></i> Export Excel
                                    </button>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-hover" id="purchase-payments-table">
                                        <thead class="table-success">
                                            <tr>
                                                <th style="width: 5%;">#</th>
                                                <th style="width: 12%;">Purchase Invoice No</th>
                                                <th style="width: 10%;">Payment Date</th>
                                                <th style="width: 8%;">Supplier ID</th>
                                                <th style="width: 15%;">Supplier Name</th>
                                                <th style="width: 10%;">Amount</th>
                                                <th style="width: 8%;">VAT</th>
                                                <th style="width: 10%;">Payment Type</th>
                                                <th style="width: 12%;">Payment Note</th>
                                                <th style="width: 10%;">Paid Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody id="purchase-payments-tbody">
                                            <tr>
                                                <td colspan="10" class="text-center py-4">
                                                    <div class="text-muted">
                                                        <i class="fas fa-search fa-2x mb-2"></i>
                                                        <p>Click "Show Report" to load purchase payment data</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                        <tfoot id="purchase-payments-footer" style="display: none;">
                                            <tr class="table-info fw-bold">
                                                <td colspan="5" class="text-right"><strong>TOTALS:</strong></td>
                                                <td id="pp-total-amount">0.00</td>
                                                <td id="pp-total-vat">0.00</td>
                                                <td colspan="2"></td>
                                                <td id="pp-total-paid">0.00</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Supplier Payments Tab -->
                        <div class="tab-pane fade" id="supplier-payments" role="tabpanel">
                            <div class="p-3">
                                <div class="d-flex justify-content-end mb-3">
                                    <button type="button" id="export-supplier-payments" class="btn btn-success btn-sm">
                                        <i class="fas fa-file-excel"></i> Export Excel
                                    </button>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-hover" id="supplier-payments-table">
                                        <thead class="table-success">
                                            <tr>
                                                <th style="width: 8%;">#</th>
                                                <th style="width: 15%;">Payment Date</th>
                                                <th style="width: 25%;">Supplier Name</th>
                                                <th style="width: 15%;">Payment Type</th>
                                                <th style="width: 22%;">Payment Note</th>
                                                <th style="width: 15%;">Paid Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody id="supplier-payments-tbody">
                                            <tr>
                                                <td colspan="6" class="text-center py-4">
                                                    <div class="text-muted">
                                                        <i class="fas fa-search fa-2x mb-2"></i>
                                                        <p>Click "Show Report" to load supplier payment data</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                        <tfoot id="supplier-payments-footer" style="display: none;">
                                            <tr class="table-info fw-bold">
                                                <td colspan="5" class="text-right"><strong>TOTAL:</strong></td>
                                                <td id="sp-total-paid">0.00</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
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
                <div class="spinner-border text-success" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 mb-0">Generating Payment Report...</p>
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
        background: linear-gradient(45deg, #28a745, #20c997);
    }
    
    .btn-success:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(40,167,69,0.1);
    }
    
    .select2-container {
        z-index: 1051;
    }
    
    .nav-tabs .nav-link.active {
        background-color: #28a745;
        border-color: #28a745;
        color: white;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize Select2
    $('#supplier_id, #payment_type').select2({
        theme: 'bootstrap-5',
        allowClear: true
    });

    // Fix: Ensure supplier_id is always a string (not undefined/null)
    $('#supplier_id').on('change', function(e) {
        var val = $(this).val();
        if (val === null || typeof val === 'undefined') {
            $(this).val('');
        }
    });

    // Show Report Button Click
    $('#show-report').click(function() {
        generateReport('purchase_payments');
        generateReport('supplier_payments');
    });

    // Tab click events
    $('#purchase-payments-tab').click(function() {
        if ($('#purchase-payments-tbody tr').length === 1) {
            generateReport('purchase_payments');
        }
    });

    $('#supplier-payments-tab').click(function() {
        if ($('#supplier-payments-tbody tr').length === 1) {
            generateReport('supplier_payments');
        }
    });

    // Generate Report Function
    function generateReport(reportType) {
        var fromDate = $('#from_date').val();
        var toDate = $('#to_date').val();
        var supplierId = $('#supplier_id').val();
        if (supplierId === null || typeof supplierId === 'undefined') supplierId = '';
        var paymentType = $('#payment_type').val();

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
            url: '{{ route("reports.purchase-payment-report") }}',
            method: 'GET',
            data: {
                from_date: fromDate,
                to_date: toDate,
                supplier_id: supplierId,
                payment_type: paymentType,
                report_type: reportType
            },
            success: function(response) {
                loadingModal.hide();
                if (response.success) {
                    if (reportType === 'purchase_payments') {
                        populatePurchasePaymentsTable(response.data);
                    } else {
                        populateSupplierPaymentsTable(response.data);
                    }
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

    // Populate Purchase Payments Table Function
    function populatePurchasePaymentsTable(data) {
        var tbody = $('#purchase-payments-tbody');
        var footer = $('#purchase-payments-footer');
        tbody.empty();

        if (data.length === 0) {
            tbody.append(`
                <tr>
                    <td colspan="10" class="text-center py-4">
                        <div class="text-muted">
                            <i class="fas fa-inbox fa-2x mb-2"></i>
                            <p>No purchase payment records found for the selected criteria</p>
                        </div>
                    </td>
                </tr>
            `);
            footer.hide();
            return;
        }

        var totalAmount = 0, totalVat = 0, totalPaid = 0;

        $.each(data, function(index, item) {
            var amount = parseFloat(item.amount) || 0;
            var vat = parseFloat(item.vat) || 0;
            var paidAmount = parseFloat(item.paid_amount) || 0;

            totalAmount += amount;
            totalVat += vat;
            totalPaid += paidAmount;

            tbody.append(`
                <tr>
                    <td>${index + 1}</td>
                    <td>${item.invoice_no || '-'}</td>
                    <td>${formatDate(item.payment_date)}</td>
                    <td>${item.supplier_id || '-'}</td>
                    <td>${item.supplier_name || '-'}</td>
                    <td class="text-end">${formatCurrency(amount)}</td>
                    <td class="text-end">${formatCurrency(vat)}</td>
                    <td>${item.payment_type || '-'}</td>
                    <td>${item.payment_note || '-'}</td>
                    <td class="text-end">${formatCurrency(paidAmount)}</td>
                </tr>
            `);
        });

        // Update footer totals
        $('#pp-total-amount').text(formatCurrency(totalAmount));
        $('#pp-total-vat').text(formatCurrency(totalVat));
        $('#pp-total-paid').text(formatCurrency(totalPaid));
        footer.show();
    }

    // Populate Supplier Payments Table Function
    function populateSupplierPaymentsTable(data) {
        var tbody = $('#supplier-payments-tbody');
        var footer = $('#supplier-payments-footer');
        tbody.empty();

        if (data.length === 0) {
            tbody.append(`
                <tr>
                    <td colspan="6" class="text-center py-4">
                        <div class="text-muted">
                            <i class="fas fa-inbox fa-2x mb-2"></i>
                            <p>No supplier payment records found for the selected criteria</p>
                        </div>
                    </td>
                </tr>
            `);
            footer.hide();
            return;
        }

        var totalPaid = 0;

        $.each(data, function(index, item) {
            var paidAmount = parseFloat(item.paid_amount) || 0;
            totalPaid += paidAmount;

            tbody.append(`
                <tr>
                    <td>${index + 1}</td>
                    <td>${formatDate(item.payment_date)}</td>
                    <td>${item.supplier_name || '-'}</td>
                    <td>${item.payment_type || '-'}</td>
                    <td>${item.payment_note || '-'}</td>
                    <td class="text-end">${formatCurrency(paidAmount)}</td>
                </tr>
            `);
        });

        // Update footer total
        $('#sp-total-paid').text(formatCurrency(totalPaid));
        footer.show();
    }

    // Export Excel Functions
    $('#export-purchase-payments').click(function() {
        var table = document.getElementById('purchase-payments-table');
        var wb = XLSX.utils.table_to_book(table, {sheet: "Purchase Payments"});
        var fileName = 'Purchase-Payments-Report-' + $('#from_date').val() + '-to-' + $('#to_date').val() + '.xlsx';
        XLSX.writeFile(wb, fileName);
    });

    $('#export-supplier-payments').click(function() {
        var table = document.getElementById('supplier-payments-table');
        var wb = XLSX.utils.table_to_book(table, {sheet: "Supplier Payments"});
        var fileName = 'Supplier-Payments-Report-' + $('#from_date').val() + '-to-' + $('#to_date').val() + '.xlsx';
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
    generateReport('purchase_payments');
});
</script>
@endpush