@extends('layouts.app')

@section('title', 'Sales Returns')

@section('content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h1 class="page-title">
                <i class="fas fa-undo me-2"></i>Sales Returns
            </h1>
        </div>
        <div class="col-auto ms-auto">
            <a href="{{ route('sales.index') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Create Return from Sale
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>Sales Returns List
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
                                <th>Return Code</th>
                                <th>Sales Invoice</th>
                                <th>Customer</th>
                                <th>Return Date</th>
                                <th>Status</th>
                                <th>Total Amount</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($returns as $index => $return)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <strong class="text-primary">{{ $return->return_code }}</strong>
                                </td>
                                <td>
                                    @if($return->invoice_no)
                                        <span class="badge bg-info text-white">{{ $return->invoice_no }}</span>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @if($return->customer_name)
                                        {{ $return->customer_name }}
                                    @else
                                        <span class="text-muted">Customer ID: {{ $return->customer_id }}</span>
                                    @endif
                                </td>
                                <td>
                                    <i class="fas fa-calendar-alt me-1"></i>
                                    {{ \Carbon\Carbon::parse($return->return_date)->format('d M Y') }}
                                </td>
                                <td>
                                    @if($return->return_status == 'Return')
                                        <span class="badge bg-success">{{ $return->return_status }}</span>
                                    @else
                                        <span class="badge bg-danger">{{ $return->return_status }}</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if($return->total_amount && $return->total_amount > 0)
                                        <strong>OMR {{ number_format($return->total_amount, 3) }}</strong>
                                    @else
                                        <span class="text-muted">OMR 0.000</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="#" class="btn btn-outline-primary" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="#" class="btn btn-outline-success" title="Print">
                                            <i class="fas fa-print"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-3x mb-3 opacity-50"></i>
                                    <br>
                                    No sales returns found.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($returns->count() > 0)
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="text-center">
                                            <h4 class="text-success mb-1">{{ $returns->count() }}</h4>
                                            <p class="text-muted mb-0">Total Returns</p>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="text-center">
                                            <h4 class="text-primary mb-1">OMR {{ number_format($returns->where('total_amount', '>', 0)->sum('total_amount'), 2) }}</h4>
                                            <p class="text-muted mb-0">Total Value</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection