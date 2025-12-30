@extends('layouts.app')

@section('title', 'Print Job Card')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Print Header -->
            <div class="text-center mb-4">
                <h2>Job Card</h2>
                <h4>{{ config('app.name', 'Company Name') }}</h4>
                <p class="mb-0">Job Card No: <strong>{{ $jobCard->job_card_no }}</strong></p>
                <p class="mb-0">Date: {{ $jobCard->created_at->format('d M Y') }}</p>
            </div>

            <!-- Job Card Details -->
            <div class="row mb-4">
                <div class="col-6">
                    <h6>Customer Details:</h6>
                    <p class="mb-1"><strong>{{ $jobCard->customer->customer_name ?? 'N/A' }}</strong></p>
                    <p class="mb-1">{{ $jobCard->customer->email ?? '' }}</p>
                    <p class="mb-1">{{ $jobCard->customer->phone ?? '' }}</p>
                </div>
                <div class="col-6">
                    <h6>Job Details:</h6>
                    <p class="mb-1">Priority: <span class="badge {{ $jobCard->priority == 'Urgent' ? 'bg-danger' : ($jobCard->priority == 'High' ? 'bg-warning' : 'bg-info') }}">{{ $jobCard->priority }}</span></p>
                    <p class="mb-1">Status: <span class="badge {{ $jobCard->status == 'Completed' ? 'bg-success' : ($jobCard->status == 'In Progress' ? 'bg-primary' : 'bg-secondary') }}">{{ $jobCard->status }}</span></p>
                    <p class="mb-1">Scheduled: {{ $jobCard->scheduled_date ? $jobCard->scheduled_date->format('d M Y') : 'Not set' }}</p>
                </div>
            </div>

            <!-- Job Description -->
            <div class="mb-4">
                <h6>Job Description:</h6>
                <p>{{ $jobCard->job_description }}</p>
            </div>

            <!-- Time Details -->
            @if($jobCard->estimated_hours || $jobCard->actual_hours)
            <div class="row mb-4">
                <div class="col-6">
                    <h6>Time Estimates:</h6>
                    <p class="mb-1">Estimated Hours: {{ $jobCard->estimated_hours ?? 'Not set' }}</p>
                    <p class="mb-1">Actual Hours: {{ $jobCard->actual_hours ?? 'Not recorded' }}</p>
                </div>
            </div>
            @endif

            <!-- Notes -->
            @if($jobCard->notes)
            <div class="mb-4">
                <h6>Notes:</h6>
                <p>{{ $jobCard->notes }}</p>
            </div>
            @endif

            <!-- Parts Used (if any) -->
            @if($jobCard->parts && $jobCard->parts->count() > 0)
            <div class="mb-4">
                <h6>Parts Used:</h6>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Quantity</th>
                            <th>Unit Cost</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($jobCard->parts as $part)
                        <tr>
                            <td>{{ $part->item->item_name ?? 'Unknown Item' }}</td>
                            <td>{{ $part->quantity_used }}</td>
                            <td>OMR {{ number_format($part->unit_cost, 3) }}</td>
                            <td>OMR {{ number_format($part->total_cost, 3) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

            <!-- Signatures -->
            <div class="row mt-5">
                <div class="col-4">
                    <div class="text-center">
                        <div style="border-bottom: 1px solid #000; margin-bottom: 5px; height: 50px;"></div>
                        <small>Engineer Signature</small>
                    </div>
                </div>
                <div class="col-4">
                    <div class="text-center">
                        <div style="border-bottom: 1px solid #000; margin-bottom: 5px; height: 50px;"></div>
                        <small>Customer Signature</small>
                    </div>
                </div>
                <div class="col-4">
                    <div class="text-center">
                        <div style="border-bottom: 1px solid #000; margin-bottom: 5px; height: 50px;"></div>
                        <small>Supervisor Signature</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
window.onload = function() {
    window.print();
}
</script>

<style>
@media print {
    body { margin: 0; }
    .container-fluid { margin: 20px; }
    .btn, .nav, .navbar { display: none !important; }
}
</style>
@endsection