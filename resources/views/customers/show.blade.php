@extends('layouts.app')

@section('title', 'Customer Details')

@section('content')
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-user me-2"></i>Customer Details
    </h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('customers.index') }}">Customers</a></li>
            <li class="breadcrumb-item active">{{ $customer->customer_name }}</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Customer Information</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td width="200"><strong>Customer Name:</strong></td>
                        <td>{{ $customer->customer_name }}</td>
                    </tr>
                    <tr>
                        <td><strong>Contact Person:</strong></td>
                        <td>{{ $customer->contact_person ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Email:</strong></td>
                        <td>{{ $customer->email ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Phone:</strong></td>
                        <td>{{ $customer->phone ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Address:</strong></td>
                        <td>{{ $customer->address ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>City:</strong></td>
                        <td>{{ $customer->city ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Country:</strong></td>
                        <td>{{ $customer->country ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>VAT Number:</strong></td>
                        <td>{{ $customer->vat_number ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Credit Limit:</strong></td>
                        <td>OMR {{ number_format($customer->credit_limit ?? 0, 2) }}</td>
                    </tr>
                    <tr>
                        <td><strong>Status:</strong></td>
                        <td>
                            @if($customer->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
            <div class="card-footer">
                <a href="{{ route('customers.edit', $customer) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-1"></i>Edit Customer
                </a>
                <a href="{{ route('customers.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Back to List
                </a>
            </div>
        </div>
    </div>
</div>
@endsection