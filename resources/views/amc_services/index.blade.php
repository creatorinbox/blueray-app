@extends('layouts.app')
@section('title', 'AMC Services')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3">AMC Services</h1>
        <a href="{{ route('amc-services.create') }}" class="btn btn-primary">New AMC</a>
    </div>
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead class="table-primary">
                    <tr>
                        <th>AMC No</th>
                        <th>Customer</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($amcs as $amc)
                    <tr>
                        <td>{{ $amc->amc_no }}</td>
                        <td>{{ $amc->customer->customer_name ?? '-' }}</td>
                        <td>{{ $amc->start_date->format('d-m-Y') }}</td>
                        <td>{{ $amc->end_date->format('d-m-Y') }}</td>
                        <td>{{ $amc->status }}</td>
                        <td>
                            <!-- Actions: View/Edit/Delete can be added here -->
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center">No AMC records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
