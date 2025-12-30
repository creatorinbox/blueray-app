@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
    </h1>
    <p class="text-muted">Welcome to BluRay National ERP System</p>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="card-title mb-0">Total Quotations</h6>
                        <h2 class="mb-0">{{ $stats['total_quotations'] }}</h2>
                    </div>
                    <div class="ms-3">
                        <i class="fas fa-file-contract fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="card-title mb-0">Pending Approvals</h6>
                        <h2 class="mb-0">{{ $stats['pending_approvals'] }}</h2>
                    </div>
                    <div class="ms-3">
                        <i class="fas fa-clock fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="card-title mb-0">Active Customers</h6>
                        <h2 class="mb-0">{{ $stats['total_customers'] }}</h2>
                    </div>
                    <div class="ms-3">
                        <i class="fas fa-users fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="card-title mb-0">Active Items</h6>
                        <h2 class="mb-0">{{ $stats['total_items'] }}</h2>
                    </div>
                    <div class="ms-3">
                        <i class="fas fa-boxes fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Quotations -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-file-contract me-2"></i>Recent Quotations
                </h5>
                <a href="{{ route('quotations.index') }}" class="btn btn-sm btn-outline-primary">
                    View All
                </a>
            </div>
            <div class="card-body">
                @if($recentQuotations->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Quotation No</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentQuotations as $quotation)
                            <tr>
                                <td>
                                    <strong>{{ $quotation->quotation_no }}</strong>
                                </td>
                                <td>{{ $quotation->customer->customer_name ?? 'N/A' }}</td>
                                <td>{{ $quotation->quotation_date->format('d/m/Y') }}</td>
                                <td>
                                    <strong class="text-success">OMR {{ number_format($quotation->total_amount, 2) }}</strong>
                                </td>
                                <td>
                                    @php
                                        $badges = [
                                            'Draft' => 'bg-secondary',
                                            'Submitted' => 'bg-warning text-dark',
                                            'Approved' => 'bg-success',
                                            'Rejected' => 'bg-danger'
                                        ];
                                        $badgeClass = $badges[$quotation->approval_status] ?? 'bg-light';
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ $quotation->approval_status }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('quotations.show', $quotation) }}" 
                                       class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center text-muted py-4">
                    <i class="fas fa-file-contract fa-3x mb-3 opacity-50"></i>
                    <p>No quotations yet. <a href="{{ route('quotations.create') }}">Create your first quotation</a></p>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-bolt me-2"></i>Quick Actions
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('quotations.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>New Quotation
                    </a>
                    
                    <a href="{{ route('quotations.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-file-contract me-2"></i>View Quotations
                    </a>
                    
                    <a href="#" class="btn btn-outline-secondary">
                        <i class="fas fa-users me-2"></i>Manage Customers
                    </a>
                    
                    <a href="#" class="btn btn-outline-secondary">
                        <i class="fas fa-boxes me-2"></i>Manage Items
                    </a>
                </div>
            </div>
        </div>
        
        <!-- System Info -->
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>System Information
                </h5>
            </div>
            <div class="card-body">
                <div class="small text-muted">
                    <div class="d-flex justify-content-between mb-2">
                        <span>User:</span>
                        <span>{{ auth()->user()->name }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Role:</span>
                        <span class="badge bg-primary">{{ auth()->user()->roleModel->display_name ?? auth()->user()->role }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Company:</span>
                        <span>{{ auth()->user()->company->company_name ?? 'N/A' }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Login Time:</span>
                        <span>{{ now()->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($stats['pending_approvals'] > 0 && auth()->user()->hasRole('sales_manager'))
<div class="row mt-4">
    <div class="col-12">
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Attention!</strong> You have {{ $stats['pending_approvals'] }} quotations pending your approval.
            <a href="{{ route('quotations.index') }}?filter=pending" class="alert-link">Review now</a>
        </div>
    </div>
</div>
@endif
@endsection