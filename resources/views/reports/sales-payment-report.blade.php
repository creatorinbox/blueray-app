@extends('layouts.app')

@section('title', 'Sales Payment Report')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">Sales Payment Report</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="#">Reports</a></li>
                            <li class="breadcrumb-item active">Sales Payment Report</li>
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
                    <h5 class="card-title mb-0">Please Enter Valid Information</h5>
                </div>
                <div class="card-body">
                    <form id="filter-form">
                        @csrf
                        <div class="row">
                            <div class="col-md-3">
                                <label for="from_date" class="form-label">From Date:</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                    <input type="date" class="form-control" id="from_date" name="from_date" value="{{ date('Y-m-d', strtotime('-30 days')) }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label for="to_date" class="form-label">To Date:</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                    <input type="date" class="form-control" id="to_date" name="to_date" value="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label for="customer_id" class="form-label">Customer Name:</label>
                                <select class="form-control select2" id="customer_id" name="customer_id" style="width: 100%;">
                                    <option value="">-All-</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->customer_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="payment_type" class="form-label">Payment Type:</label>
                                <select class="form-control select2" id="payment_type" name="payment_type" style="width: 100%;">
                                    <option value="">-All-</option>
                                    @foreach($paymentTypes as $type)
                                        <option value="{{ $type->payment_type }}">{{ $type->payment_type }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6 offset-md-3 d-flex justify-content-between">
                                <button type="button" id="show-report" class="btn btn-success w-50">
                                    <i class="fas fa-search"></i> Show
                                </button>
                                <button type="button" id="export-excel" class="btn btn-info w-50 ms-2">
                                    <i class="fas fa-file-excel"></i> Excel
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs for Sales Payments and Customer Payments -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs" id="paymentTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="sales-payments-tab" data-bs-toggle="tab" data-bs-target="#sales-payments" type="button" role="tab">Sales Payments</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="customer-payments-tab" data-bs-toggle="tab" data-bs-target="#customer-payments" type="button" role="tab">Customer Payments</button>
                        </li>
                    </ul>
                    <div class="tab-content mt-3" id="paymentTabsContent">
                        <div class="tab-pane fade show active" id="sales-payments" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="report-data-1">
                                    <thead class="table-primary">
                                        <tr>
                                            <th>#</th>
                                            <th>Invoice No</th>
                                            <th>Payment Date</th>
                                            <th>Customer ID</th>
                                            <th>Customer Name</th>
                                            <th>Customer VAT Number</th>
                                            <th>Payment Type</th>
                                            <th>Payment Note</th>
                                            <th>Subtotal</th>
                                            <th>VAT</th>
                                            <th>Paid Amount ({{ $currency }})</th>
                                        </tr>
                                    </thead>
                                    <tbody id="sales-payments-tbody">
                                        <tr>
                                            <td colspan="11" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="fas fa-search fa-2x mb-2"></i>
                                                    <p>Click "Show" to load sales payments data</p>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="customer-payments" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="report-data-2">
                                    <thead class="table-primary">
                                        <tr>
                                            <th>#</th>
                                            <th>Payment Date</th>
                                            <th>Customer Name</th>
                                            <th>Payment Type</th>
                                            <th>Payment Note</th>
                                            <th>Paid Amount ({{ $currency }})</th>
                                        </tr>
                                    </thead>
                                    <tbody id="customer-payments-tbody">
                                        <tr>
                                            <td colspan="6" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="fas fa-search fa-2x mb-2"></i>
                                                    <p>Click "Show" to load customer payments data</p>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
$(document).ready(function() {
    $('#customer_id, #payment_type').select2({
        theme: 'bootstrap-5',
        allowClear: true
    });
    $('#show-report').click(function() {
        generateReport();
    });
    $('#export-excel').click(function() {
        var activeTab = $('.tab-pane.active').attr('id');
        var tableId = activeTab === 'sales-payments' ? 'report-data-1' : 'report-data-2';
        var table = document.getElementById(tableId);
        var wb = XLSX.utils.table_to_book(table, {sheet: "Payments Report"});
        var fileName = 'Payments-Report-' + $('#from_date').val() + '-to-' + $('#to_date').val() + '.xlsx';
        XLSX.writeFile(wb, fileName);
    });
    function generateReport() {
        var fromDate = $('#from_date').val();
        var toDate = $('#to_date').val();
        var customerId = $('#customer_id').val();
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
        $.ajax({
            url: '{{ route('reports.sales-payment-report.data') }}',
            method: 'GET',
            data: {
                from_date: fromDate,
                to_date: toDate,
                customer_id: customerId,
                payment_type: paymentType
            },
            success: function(response) {
                populateSalesPayments(response.sales_payments);
                populateCustomerPayments(response.customer_payments);
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while generating the report'
                });
            }
        });
    }
    function populateSalesPayments(data) {
        var tbody = $('#sales-payments-tbody');
        tbody.empty();
        if (data.length === 0) {
            tbody.append(`
                <tr>
                    <td colspan="11" class="text-center py-4">
                        <div class="text-muted">
                            <i class="fas fa-inbox fa-2x mb-2"></i>
                            <p>No sales payments records found for the selected criteria</p>
                        </div>
                    </td>
                </tr>
            `);
            return;
        }
        $.each(data, function(index, item) {
            tbody.append(`
                <tr>
                    <td>${index + 1}</td>
                    <td>${item.invoice_no || '-'}</td>
                    <td>${formatDate(item.payment_date)}</td>
                    <td>${item.customer_id || '-'}</td>
                    <td>${item.customer_name || '-'}</td>
                    <td>${item.customer_vat_number || '-'}</td>
                    <td>${item.payment_type || '-'}</td>
                    <td>${item.payment_note || '-'}</td>
                    <td class="text-end">${formatCurrency(item.subtotal)}</td>
                    <td class="text-end">${formatCurrency(item.vat)}</td>
                    <td class="text-end">${formatCurrency(item.paid_amount)}</td>
                </tr>
            `);
        });
    }
    function populateCustomerPayments(data) {
        var tbody = $('#customer-payments-tbody');
        tbody.empty();
        if (data.length === 0) {
            tbody.append(`
                <tr>
                    <td colspan="6" class="text-center py-4">
                        <div class="text-muted">
                            <i class="fas fa-inbox fa-2x mb-2"></i>
                            <p>No customer payments records found for the selected criteria</p>
                        </div>
                    </td>
                </tr>
            `);
            return;
        }
        $.each(data, function(index, item) {
            tbody.append(`
                <tr>
                    <td>${index + 1}</td>
                    <td>${formatDate(item.payment_date)}</td>
                    <td>${item.customer_name || '-'}</td>
                    <td>${item.payment_type || '-'}</td>
                    <td>${item.payment_note || '-'}</td>
                    <td class="text-end">${formatCurrency(item.paid_amount)}</td>
                </tr>
            `);
        });
    }
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
});
</script>
@endpush
