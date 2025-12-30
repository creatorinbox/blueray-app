@extends('layouts.app')

@section('title', 'Job Cards')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="page-title">
            <i class="fas fa-wrench me-2"></i>Job Cards
        </h1>
        <a href="{{ route('job-cards.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i>New Job Card
        </a>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Job Cards</li>
        </ol>
    </nav>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Total Jobs</h5>
                        <h2 class="mb-0">{{ $stats['total_jobs'] }}</h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-clipboard-list fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Pending</h5>
                        <h2 class="mb-0">{{ $stats['pending_jobs'] }}</h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">In Progress</h5>
                        <h2 class="mb-0">{{ $stats['in_progress'] }}</h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-cog fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Completed</h5>
                        <h2 class="mb-0">{{ $stats['completed'] }}</h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Job Cards Table -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-list me-2"></i>Job Cards List
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped" id="jobCardsTable">
                <thead class="table-primary">
                    <tr>
                        <th>Job Card #</th>
                        <th>Customer</th>
                        <th>Description</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Scheduled Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($jobCards as $jobCard)
                    <tr>
                        <td>
                            <strong>{{ $jobCard->job_card_no }}</strong>
                        </td>
                        <td>{{ $jobCard->customer->customer_name ?? 'N/A' }}</td>
                        <td>
                            {{ Str::limit($jobCard->job_description, 50) }}
                        </td>
                        <td>
                            <span class="badge {{ $jobCard->priority == 'Urgent' ? 'bg-danger' : ($jobCard->priority == 'High' ? 'bg-warning' : 'bg-info') }}">
                                {{ $jobCard->priority }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $jobCard->status == 'Completed' ? 'bg-success' : ($jobCard->status == 'In Progress' ? 'bg-primary' : 'bg-secondary') }}">
                                {{ $jobCard->status }}
                            </span>
                        </td>
                        <td>
                            {{ $jobCard->scheduled_date ? $jobCard->scheduled_date->format('d M Y') : '-' }}
                        </td>
                        <td class="text-center">
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-bs-toggle="dropdown">
                                    Action
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('job-cards.show', $jobCard) }}"><i class="fas fa-eye me-2"></i>View</a></li>
                                    <li><a class="dropdown-item" href="{{ route('job-cards.edit', $jobCard) }}"><i class="fas fa-edit me-2"></i>Edit</a></li>
                                    <li><a class="dropdown-item" href="{{ route('job-cards.print', $jobCard) }}" target="_blank"><i class="fas fa-print me-2"></i>Print</a></li>
                                    <li><a class="dropdown-item" href="{{ route('job-cards.duplicate', $jobCard) }}"><i class="fas fa-copy me-2"></i>Duplicate</a></li>
                                    <li><a class="dropdown-item" href="{{ route('job-cards.create_quote', $jobCard) }}"><i class="fas fa-file-contract me-2"></i>Create Quote</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        @if($jobCard->status !== 'Completed')
                                        <form method="POST" action="{{ route('job-cards.destroy', $jobCard) }}" class="m-0" onsubmit="return confirm('Are you sure you want to delete this job card?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger"><i class="fas fa-trash me-2"></i>Delete</button>
                                        </form>
                                        @else
                                        <span class="dropdown-item text-muted">Deletion disabled</span>
                                        @endif
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#jobCardsTable').DataTable({
        pageLength: 25,
        responsive: true,
        order: [[0, 'desc']] // Order by job card number descending
    });
});
</script>
@endpush