@extends('layouts.app')

@section('title', 'Sales Return Report')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">Sales Return Report</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="#">Reports</a></li>
                            <li class="breadcrumb-item active">Sales Return Report</li>
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
                            <div class="col-md-4">
                                <label for="customer_id" class="form-label">Customer Name:</label>
                                <select class="form-control select2" id="customer_id" name="customer_id" style="width: 100%;">
                                    <option value="">-All-</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->customer_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="button" id="show-report" class="btn btn-success w-100">
                                    <i class="fas fa-search"></i> Show
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
                    <h5 class="card-title mb-0">Records Table</h5>
                    <button type="button" id="export-excel" class="btn btn-info btn-sm">
                        <i class="fas fa-file-excel"></i> Excel
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover" id="sales-return-report-table">
                            <thead class="table-primary">
                                <tr>
                                    <th>#</th>
                                    <th>Invoice No</th>
                                    <th>Return Date</th>
                                    <th>Sales Code</th>
                                    <th>Customer Name</th>
                                    <th>Without VAT</th>
                                    <th>VAT</th>
                                    <th>Invoice Total ({{ $currency }})</th>
                                    <th>Paid Amount ({{ $currency }})</th>
                                    <th>Due Amount ({{ $currency }})</th>
                                </tr>
                            </thead>
                            <tbody id="report-tbody">
                                <tr>
                                    <td colspan="10" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-search fa-2x mb-2"></i>
                                            <p>Click "Show" to load sales return data</p>
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
    $('#customer_id').select2({
        theme: 'bootstrap-5',
        placeholder: '-All-',
        allowClear: true
    });

    $('#show-report').click(function() {
        generateReport();
    });

    function generateReport() {
        var fromDate = $('#from_date').val();
        var toDate = $('#to_date').val();
        var customerId = $('#customer_id').val();

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
        // Show loading modal or spinner if needed
        $.ajax({
            url: '{{ route('reports.sales-return-report') }}',
            method: 'GET',
            data: {
                from_date: fromDate,
                to_date: toDate,
                customer_id: customerId
            },
            success: function(response) {
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
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while generating the report'
                });
            }
        });
    }

    function populateTable(data) {
        var tbody = $('#report-tbody');
        tbody.empty();
        if (data.length === 0) {
            tbody.append(`
                <tr>
                    <td colspan="10" class="text-center py-4">
                        <div class="text-muted">
                            <i class="fas fa-inbox fa-2x mb-2"></i>
                            <p>No sales return records found for the selected criteria</p>
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
                    <td>${formatDate(item.return_date)}</td>
                    <td>${item.sales_code || '-'}</td>
                    <td>${item.customer_name || '-'}</td>
                    <td class="text-end">${formatCurrency(item.without_vat)}</td>
                    <td class="text-end">${formatCurrency(item.vat)}</td>
                    <td class="text-end">${formatCurrency(item.invoice_total)}</td>
                    <td class="text-end">${formatCurrency(item.paid_amount)}</td>
                    <td class="text-end">${formatCurrency(item.due_amount)}</td>
                </tr>
            `);
        });
    }

    $('#export-excel').click(function() {
        var table = document.getElementById('sales-return-report-table');
        var wb = XLSX.utils.table_to_book(table, {sheet: "Sales Return Report"});
        var fileName = 'Sales-Return-Report-' + $('#from_date').val() + '-to-' + $('#to_date').val() + '.xlsx';
        XLSX.writeFile(wb, fileName);
    });

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
    // Optionally, load initial data
    // generateReport();
});
</script>
@endpush
