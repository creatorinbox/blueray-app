@extends('layouts.app')

@section('title', 'Delivery Notes')

@section('content')
<div class="page-header d-flex justify-content-between">
    <h1 class="page-title">
        <i class="fas fa-truck me-2"></i>Delivery Notes
    </h1>
    <a href="{{ route('deliveries.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> New Delivery Note
    </a>
</div>

<!-- Stats -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Total Deliveries</h5>
                        <h2 class="mb-0">{{ $stats['total_deliveries'] }}</h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-truck fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Pending Delivery</h5>
                        <h2 class="mb-0">{{ $stats['pending_delivery'] }}</h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Delivered</h5>
                        <h2 class="mb-0">{{ $stats['delivered'] }}</h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Deliveries Table -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Delivery Notes List</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped" id="deliveriesTable">
                <thead class="table-primary">
                    <tr>
                        <th>Delivery Note No</th>
                        <th>Customer</th>
                        <th>Delivery Date</th>
                        <th>Reference No</th>
                        <th>Status</th>
                        <th>Total Amount</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($deliveries as $delivery)
                    <tr>
                        <td>
                            <strong>{{ $delivery->delivery_note_number }}</strong>
                        </td>
                        <td>{{ $delivery->customer->customer_name ?? 'N/A' }}</td>
                        <td>{{ $delivery->formatted_delivery_date }}</td>
                        <td>{{ $delivery->reference_no ?? '-' }}</td>
                        <td>
                            <span class="badge {{ $delivery->delivery_status == 'Delivered' ? 'bg-success' : ($delivery->delivery_status == 'Pending' ? 'bg-warning' : 'bg-danger') }}">
                                {{ $delivery->delivery_status }}
                            </span>
                        </td>
                        <td>LKR {{ number_format($delivery->total_amount, 3) }}</td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('deliveries.show', $delivery) }}" 
                                   class="btn btn-sm btn-outline-info" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('deliveries.print', $delivery) }}" 
                                   class="btn btn-sm btn-outline-primary" title="Print" target="_blank">
                                    <i class="fas fa-print"></i>
                                </a>
                                @if($delivery->delivery_status == 'Pending')
                                    <button type="button" class="btn btn-sm btn-outline-warning" 
                                            onclick="updateStatus({{ $delivery->id }}, 'Delivered')" title="Mark as Delivered">
                                        <i class="fas fa-check"></i>
                                    </button>
                                @endif
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
    $('#deliveriesTable').DataTable({
        pageLength: 25,
        responsive: true,
        order: [[2, 'desc']] // Order by delivery date descending
    });
});

function updateStatus(deliveryId, status) {
    if (confirm(`Are you sure you want to mark this delivery as ${status}?`)) {
        fetch(`/deliveries/${deliveryId}/status`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                delivery_status: status
            })
        })
        .then(response => {
            if (response.ok) {
                location.reload();
            } else {
                alert('Error updating delivery status');
            }
        })
        .catch(error => {
            alert('Error updating delivery status');
            console.error(error);
        });
    }
}
</script>
@endpush