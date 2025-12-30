@extends('layouts.app')

@section('title', 'Account Summary Report')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/daterangepicker/3.0.5/daterangepicker.css" />
@endpush

@section('content')
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-chart-line me-2"></i>Account Summary Report
    </h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Account Summary Report</li>
        </ol>
    </nav>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('reports.account-summary') }}" method="POST" class="row g-3">
                    @csrf
                    <div class="col-md-3">
                        <label for="start_date" class="form-label">From Date</label>
                        <input type="date" class="form-control" name="start_date" value="{{ $data['start_date'] }}">
                    </div>
                    <div class="col-md-3">
                        <label for="end_date" class="form-label">To Date</label>
                        <input type="date" class="form-control" name="end_date" value="{{ $data['end_date'] }}">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-1"></i>Generate Report
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0 d-flex justify-content-between">
                    <span>Trading Summary</span>
                    <button class="btn btn-success btn-sm btnExport" title="Download Data in Excel Format">
                        <i class="fas fa-file-excel me-1"></i>Excel
                    </button>
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="row" id="report-data">
                    <div class="col-md-6">
                        <table class="table table-striped mb-0">
                            <tbody>
                                <!-- Opening Stock -->
                                <tr>
                                    <td>Opening Stock</td>
                                    <td class="text-end fw-bold">{{ number_format($data['opening_stock_price'], 3) }}</td>
                                </tr>
                                
                                <!-- Purchase Section -->
                                <tr class="table-secondary">
                                    <th colspan="2">Purchase</th>
                                </tr>
                                <tr>
                                    <td>Total Purchase</td>
                                    <td class="text-end fw-bold">{{ number_format($data['purchase_total'], 3) }}</td>
                                </tr>
                                <tr>
                                    <td>Total Purchase Tax</td>
                                    <td class="text-end fw-bold">{{ number_format($data['purchase_tax_amt'], 3) }}</td>
                                </tr>
                                <tr>
                                    <td>Total Discount on Purchase</td>
                                    <td class="text-end fw-bold">{{ number_format($data['purchase_discount_amt'], 3) }}</td>
                                </tr>
                                
                                <!-- Purchase Return Section -->
                                <tr class="table-secondary">
                                    <th colspan="2">Purchase Return</th>
                                </tr>
                                <tr>
                                    <td>Total Purchase Return</td>
                                    <td class="text-end fw-bold">{{ number_format($data['purchase_return_total'], 3) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="col-md-6">
                        <table class="table table-striped mb-0">
                            <tbody>
                                <!-- Sales Section -->
                                <tr class="table-secondary">
                                    <th colspan="2">Sales</th>
                                </tr>
                                <tr>
                                    <td>Total Sales</td>
                                    <td class="text-end fw-bold">{{ number_format($data['sales_total'], 3) }}</td>
                                </tr>
                                <tr>
                                    <td>Total Sales Tax</td>
                                    <td class="text-end fw-bold">{{ number_format($data['sales_tax_amt'], 3) }}</td>
                                </tr>
                                <tr>
                                    <td>Total Discount on Sales</td>
                                    <td class="text-end fw-bold">{{ number_format($data['sales_discount_amt'], 3) }}</td>
                                </tr>
                                
                                <!-- Sales Return Section -->
                                <tr class="table-secondary">
                                    <th colspan="2">Sales Return</th>
                                </tr>
                                <tr>
                                    <td>Total Sales Return</td>
                                    <td class="text-end fw-bold">{{ number_format($data['sales_return_total'], 3) }}</td>
                                </tr>
                                
                                <!-- Profit & Loss Section -->
                                <tr class="table-secondary">
                                    <th colspan="2">Profit & Loss Summary</th>
                                </tr>
                                <tr>
                                    <td>Gross Profit / Loss</td>
                                    <td class="text-end fw-bold {{ $data['gross_profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($data['gross_profit'], 3) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>Total Expenses</td>
                                    <td class="text-end fw-bold">{{ number_format($data['expense_total'], 3) }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Net Profit / Loss</td>
                                    <td class="text-end fw-bold {{ $data['net_profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($data['net_profit'], 3) }}
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

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0 d-flex justify-content-between">
                    <span>Payment Summary</span>
                    <button class="btn btn-success btn-sm btnExport2" title="Download Data in Excel Format">
                        <i class="fas fa-file-excel me-1"></i>Excel
                    </button>
                </h5>
            </div>
            <div class="card-body p-0">
                <table class="table table-bordered table-hover mb-0" id="payment-summary">
                    <tbody>
                        <!-- Purchase Payments -->
                        <tr class="table-secondary">
                            <th colspan="2">Purchase</th>
                        </tr>
                        <tr>
                            <td>Purchase Paid Amount</td>
                            <td class="text-end fw-bold text-success">{{ number_format($data['purchase_paid_amount'], 3) }}</td>
                        </tr>
                        <tr>
                            <td>Purchase Return Paid Amount</td>
                            <td class="text-end fw-bold text-success">{{ number_format($data['purchase_return_paid_amount'], 3) }}</td>
                        </tr>
                        
                        <!-- Sales Payments -->
                        <tr class="table-secondary">
                            <th colspan="2">Sales</th>
                        </tr>
                        <tr>
                            <td>Sales Paid Amount</td>
                            <td class="text-end fw-bold text-success">{{ number_format($data['sales_paid_amount'], 3) }}</td>
                        </tr>
                        <tr>
                            <td>Sales Return Paid Amount</td>
                            <td class="text-end fw-bold text-success">{{ number_format($data['sales_return_paid_amount'], 3) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
function convertToExcel(fileName, tableId) {
    var table = document.getElementById(tableId);
    var wb = XLSX.utils.table_to_book(table, {sheet: "Sheet1"});
    return XLSX.writeFile(wb, fileName + '.xlsx');
}

$(document).ready(function() {
    $('.btnExport').click(function() {
        convertToExcel('Account-Summary-Report', 'report-data');
    });
    
    $('.btnExport2').click(function() {
        convertToExcel('Payment-Summary-Report', 'payment-summary');
    });
    
    // Auto-submit form when dates change
    $('input[name="start_date"], input[name="end_date"]').on('change', function() {
        // Optional: Auto-submit or add button to submit
    });
});
</script>
@endpush