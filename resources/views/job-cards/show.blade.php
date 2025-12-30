@extends('layouts.app')

@section('title', 'Job Card Details')

@section('content')
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-wrench me-2"></i>Job Card Details
    </h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('job-cards.index') }}">Job Cards</a></li>
            <li class="breadcrumb-item active">{{ $jobCard->job_card_no }}</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Job Card Information</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td width="200"><strong>Job Card No:</strong></td>
                        <td>{{ $jobCard->job_card_no }}</td>
                    </tr>
                    <tr>
                        <td><strong>Customer:</strong></td>
                        <td>{{ $jobCard->customer->customer_name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Priority:</strong></td>
                        <td>
                            <span class="badge {{ $jobCard->priority == 'Urgent' ? 'bg-danger' : ($jobCard->priority == 'High' ? 'bg-warning' : 'bg-info') }}">
                                {{ $jobCard->priority }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Status:</strong></td>
                        <td>
                            <span class="badge {{ $jobCard->status == 'Completed' ? 'bg-success' : ($jobCard->status == 'In Progress' ? 'bg-primary' : 'bg-secondary') }}">
                                {{ $jobCard->status }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Scheduled Date:</strong></td>
                        <td>{{ $jobCard->scheduled_date ? $jobCard->scheduled_date->format('d M Y') : '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Estimated Hours:</strong></td>
                        <td>{{ $jobCard->estimated_hours ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Actual Hours:</strong></td>
                        <td>{{ $jobCard->actual_hours ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Job Description:</strong></td>
                        <td>{{ $jobCard->job_description }}</td>
                    </tr>
                    @if($jobCard->invoice_no)
                    <tr>
                        <td><strong>Invoice Number:</strong></td>
                        <td>{{ $jobCard->invoice_no }}</td>
                    </tr>
                    @endif
                    @if($jobCard->model_no)
                    <tr>
                        <td><strong>Model Number:</strong></td>
                        <td>{{ $jobCard->model_no }}</td>
                    </tr>
                    @endif
                    @if($jobCard->serial_no)
                    <tr>
                        <td><strong>Serial Number:</strong></td>
                        <td>{{ $jobCard->serial_no }}</td>
                    </tr>
                    @endif
                    @if($jobCard->service_attend)
                    <tr>
                        <td><strong>Service Technician:</strong></td>
                        <td>{{ $jobCard->service_attend }}
                            @if($jobCard->service_attend_mobile)
                                <br><small class="text-muted">{{ $jobCard->service_attend_mobile }}</small>
                            @endif
                        </td>
                    </tr>
                    @endif
                    @if($jobCard->loading_hr)
                    <tr>
                        <td><strong>Loading Hours:</strong></td>
                        <td>{{ $jobCard->loading_hr }}</td>
                    </tr>
                    @endif
                    @if($jobCard->service_start_time || $jobCard->service_end_time)
                    <tr>
                        <td><strong>Service Duration:</strong></td>
                        <td>
                            @if($jobCard->service_start_time)
                                {{ $jobCard->service_start_time }}
                            @endif
                            @if($jobCard->service_start_time && $jobCard->service_end_time)
                                to 
                            @endif
                            @if($jobCard->service_end_time)
                                {{ $jobCard->service_end_time }}
                            @endif
                        </td>
                    </tr>
                    @endif
                    @if($jobCard->reference_no)
                    <tr>
                        <td><strong>Reference Number:</strong></td>
                        <td>{{ $jobCard->reference_no }}</td>
                    </tr>
                    @endif
                    @if($jobCard->job_report_date || $jobCard->job_report_no)
                    <tr>
                        <td><strong>Job Report:</strong></td>
                        <td>
                            @if($jobCard->job_report_no)
                                {{ $jobCard->job_report_no }}
                            @endif
                            @if($jobCard->job_report_date)
                                <br><small class="text-muted">{{ $jobCard->job_report_date->format('d M Y') }}</small>
                            @endif
                        </td>
                    </tr>
                    @endif
                    @if($jobCard->service_remarks)
                    <tr>
                        <td><strong>Service Remarks:</strong></td>
                        <td>{{ $jobCard->service_remarks }}</td>
                    </tr>
                    @endif
                    @if($jobCard->customer_remarks)
                    <tr>
                        <td><strong>Customer Remarks:</strong></td>
                        <td>{{ $jobCard->customer_remarks }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td><strong>Notes:</strong></td>
                        <td>{{ $jobCard->notes ?: '-' }}</td>
                    </tr>
                </table>
            </div>
            <div class="card-footer">
                <a href="{{ route('job-cards.edit', $jobCard) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-1"></i>Edit Job Card
                </a>
                <a href="{{ route('job-cards.print', $jobCard) }}" class="btn btn-primary" target="_blank">
                    <i class="fas fa-print me-1"></i>Print
                </a>
                <a href="{{ route('job-cards.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Back to List
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header"><h5 class="mb-0">Parts / Items</h5></div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Code</th>
                                <th style="width:100px">Qty</th>
                                <th>Lot</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jobCard->parts as $part)
                            <tr>
                                <td>{{ $part->item->item_name ?? '-' }}</td>
                                <td>{{ $part->item->item_code ?? '-' }}</td>
                                <td>{{ number_format($part->qty_used ?? $part->quantity_used ?? 0, 2) }}</td>
                                <td>{{ $part->lot_id ? ($part->lot->lot_no ?? $part->lot_id) : '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection