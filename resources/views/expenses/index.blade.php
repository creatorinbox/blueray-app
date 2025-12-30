@extends('layouts.app')

@section('title', 'Expenses')

@section('content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h1 class="page-title">
                <i class="fas fa-money-bill-wave me-2"></i>Expenses
            </h1>
        </div>
        <div class="col-auto ms-auto">
            <a href="{{ route('expenses.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add Expense
            </a>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-3">
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Total Expenses</div>
                    <div class="ms-auto">
                        <span class="text-blue">
                            <i class="fas fa-list"></i>
                        </span>
                    </div>
                </div>
                <div class="h1 mb-1">{{ $stats['total_expenses'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Total Amount</div>
                    <div class="ms-auto">
                        <span class="text-green">
                            <i class="fas fa-money-bill-wave"></i>
                        </span>
                    </div>
                </div>
                <div class="h1 mb-1">OMR {{ number_format($stats['total_amount'], 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">This Month</div>
                    <div class="ms-auto">
                        <span class="text-yellow">
                            <i class="fas fa-calendar-month"></i>
                        </span>
                    </div>
                </div>
                <div class="h1 mb-1">OMR {{ number_format($stats['this_month'], 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">This Week</div>
                    <div class="ms-auto">
                        <span class="text-purple">
                            <i class="fas fa-calendar-week"></i>
                        </span>
                    </div>
                </div>
                <div class="h1 mb-1">OMR {{ number_format($stats['this_week'], 2) }}</div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>Expenses List
                </h5>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        {{ session('error') }}
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-primary">
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Category</th>
                                <th>Sub Category</th>
                                <th>Expense For</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>VAT</th>
                                <th>Total</th>
                                <th>Reference</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($expenses as $index => $expense)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <i class="fas fa-calendar-alt me-1"></i>
                                    {{ \Carbon\Carbon::parse($expense->expense_date)->format('d M Y') }}
                                </td>
                                <td>
                                    <span class="badge bg-info text-white">{{ $expense->category_name }}</span>
                                </td>
                                <td>
                                    @if($expense->sub_category_name)
                                        <span class="badge bg-secondary">{{ $expense->sub_category_name }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ $expense->expense_for }}</td>
                                <td>
                                    @if($expense->customer_name)
                                        {{ $expense->customer_name }}
                                    @else
                                        <span class="text-muted">Others</span>
                                    @endif
                                </td>
                                <td class="text-end">OMR {{ number_format($expense->expense_amount, 3) }}</td>
                                <td class="text-center">
                                    @if($expense->vat_type == 'vat')
                                        <span class="badge bg-warning">VAT</span>
                                    @else
                                        <span class="badge bg-light text-dark">No VAT</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <strong>OMR {{ number_format($expense->total_amount, 3) }}</strong>
                                </td>
                                <td>
                                    @if($expense->reference_no)
                                        <span class="badge bg-primary">{{ $expense->reference_no }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('expenses.show', $expense->id) }}" class="btn btn-outline-primary" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('expenses.edit', $expense->id) }}" class="btn btn-outline-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('expenses.destroy', $expense->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this expense?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="11" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-3x mb-3 opacity-50"></i>
                                    <br>
                                    No expenses found.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection