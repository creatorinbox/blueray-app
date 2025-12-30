@extends('layouts.app')

@section('title', 'Expense Details')

@section('content')
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-money-bill-wave me-2"></i>Expense Details
    </h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('expenses.index') }}">Expenses</a></li>
            <li class="breadcrumb-item active">Details</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Expense Information</h5>
                <div class="btn-group">
                    <a href="{{ route('expenses.edit', $expense->id) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit me-1"></i>Edit
                    </a>
                    <a href="{{ route('expenses.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Back to List
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td class="fw-bold" width="30%">Expense Date:</td>
                                <td>{{ date('d M Y', strtotime($expense->expense_date)) }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Category:</td>
                                <td>{{ $expense->category_name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Sub Category:</td>
                                <td>{{ $expense->sub_category_name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Customer:</td>
                                <td>{{ $expense->customer_name ?? 'Others' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Expense For:</td>
                                <td>{{ $expense->expense_for }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Amount:</td>
                                <td><span class="badge bg-primary">{{ number_format($expense->expense_amount, 3) }} AED</span></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td class="fw-bold" width="30%">VAT Type:</td>
                                <td>
                                    @if($expense->vat_type == 'vat')
                                        <span class="badge bg-info">With VAT</span>
                                    @else
                                        <span class="badge bg-secondary">Without VAT</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Total Amount:</td>
                                <td><span class="badge bg-success fs-6">{{ number_format($expense->total_amount, 3) }} AED</span></td>
                            </tr>
                            @if($expense->vehicle_no)
                            <tr>
                                <td class="fw-bold">Vehicle Number:</td>
                                <td>{{ $expense->vehicle_no }}</td>
                            </tr>
                            @endif
                            @if($expense->reference_no)
                            <tr>
                                <td class="fw-bold">Reference No:</td>
                                <td>{{ $expense->reference_no }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td class="fw-bold">Created By:</td>
                                <td>{{ $expense->created_by_name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Created At:</td>
                                <td>{{ date('d M Y, h:i A', strtotime($expense->created_at)) }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                @if($expense->note)
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="border rounded p-3 bg-light">
                            <h6 class="fw-bold mb-2">Notes:</h6>
                            <p class="mb-0">{{ $expense->note }}</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection