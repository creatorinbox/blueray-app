@extends('layouts.app')
@section('title', 'Create AMC Service')
@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-3">New AMC Service</h1>
    <form action="{{ route('amc-services.store') }}" method="POST">
        @csrf
        <div class="card mb-3">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label>AMC No</label>
                        <input type="text" name="amc_no" class="form-control" required value="{{ old('amc_no') }}">
                    </div>
                    <div class="col-md-3">
                        <label>Customer</label>
                        <select name="customer_id" class="form-control" required>
                            <option value="">Select</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>{{ $customer->customer_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Service Item</label>
                        <input type="text" name="service_item" class="form-control" required value="{{ old('service_item') }}">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label>Start Date</label>
                        <input type="date" name="start_date" class="form-control" required value="{{ old('start_date', date('Y-m-d')) }}">
                    </div>
                    <div class="col-md-3">
                        <label>End Date</label>
                        <input type="date" name="end_date" class="form-control" required value="{{ old('end_date', date('Y-m-d', strtotime('+1 year'))) }}">
                    </div>
                    <div class="col-md-3">
                        <label>AMC Type</label>
                        <select name="amc_type" class="form-control" required>
                            <option value="">Select</option>
                            <option value="Labour" {{ old('amc_type') == 'Labour' ? 'selected' : '' }}>Labour</option>
                            <option value="Comprehensive" {{ old('amc_type') == 'Comprehensive' ? 'selected' : '' }}>Comprehensive</option>
                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label>Contract Value</label>
                        <input type="number" step="0.01" name="contract_value" class="form-control" required value="{{ old('contract_value') }}">
                    </div>
                    <div class="col-md-3">
                        <label>VAT</label>
                        <input type="number" step="0.01" name="vat" class="form-control" required value="{{ old('vat', 0) }}">
                    </div>
                    <div class="col-md-3">
                        <label>Invoice Ref</label>
                        <input type="text" name="invoice_ref" class="form-control" value="{{ old('invoice_ref') }}">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Save AMC</button>
            </div>
        </div>
    </form>
</div>
@endsection
