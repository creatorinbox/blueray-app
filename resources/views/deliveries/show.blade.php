@extends('layouts.app')

@section('title', 'Delivery Note Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h3 class="card-title">
                        Delivery Note: {{ $deliveryNote->delivery_note_number }}
                    </h3>
                    <div>
                        <a href="{{ route('deliveries.print', $deliveryNote->id) }}" class="btn btn-primary" target="_blank">
                            <i class="fas fa-print"></i> Print
                        </a>
                        <a href="{{ route('deliveries.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Delivery Note Details -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Delivery Details</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Delivery Note No:</strong></td>
                                    <td>{{ $deliveryNote->delivery_note_number }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Delivery Date:</strong></td>
                                    <td>{{ $deliveryNote->formatted_delivery_date }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Reference No:</strong></td>
                                    <td>{{ $deliveryNote->reference_no ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Subject:</strong></td>
                                    <td>{{ $deliveryNote->subject ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        <span class="badge 
                                            @if($deliveryNote->delivery_status == 'Pending') bg-warning 
                                            @elseif($deliveryNote->delivery_status == 'Delivered') bg-success 
                                            @else bg-danger @endif">
                                            {{ $deliveryNote->delivery_status }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Customer Details</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Customer Name:</strong></td>
                                    <td>{{ $deliveryNote->customer->customer_name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Customer Code:</strong></td>
                                    <td>{{ $deliveryNote->customer->customer_code ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Mobile:</strong></td>
                                    <td>{{ $deliveryNote->customer->customer_mobile ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td>{{ $deliveryNote->customer->customer_email ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Address:</strong></td>
                                    <td>{{ $deliveryNote->customer->address ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Items Table -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Items</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-primary">
                                        <tr>
                                            <th width="25%">Item Name</th>
                                            <th width="10%">Quantity</th>
                                            <th width="10%">Unit Price</th>
                                            <th width="10%">Discount</th>
                                            <th width="10%">Tax Rate</th>
                                            <th width="10%">Tax Amount</th>
                                            <th width="12%">Total Amount</th>
                                            <th width="13%">Description</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($deliveryNote->items as $item)
                                            <tr>
                                                <td>{{ $item->item_name }}</td>
                                                <td>{{ number_format($item->quantity, 3) }}</td>
                                                <td>{{ number_format($item->unit_price, 3) }}</td>
                                                <td>{{ number_format($item->discount_amount, 3) }}</td>
                                                <td>{{ number_format($item->tax_rate, 2) }}%</td>
                                                <td>{{ number_format($item->tax_amount, 3) }}</td>
                                                <td>{{ number_format($item->total_amount, 3) }}</td>
                                                <td>{{ $item->description ?? '-' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center">No items found</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Summary -->
                    <div class="row mt-4">
                        <div class="col-md-6">
                            @if($deliveryNote->delivery_notes)
                                <h5>Delivery Notes</h5>
                                <p class="border p-3 bg-light">{{ $deliveryNote->delivery_notes }}</p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <table class="table">
                                <tr>
                                    <th>Subtotal:</th>
                                    <td class="text-end">{{ number_format($deliveryNote->subtotal, 3) }}</td>
                                </tr>
                                <tr>
                                    <th>Total Discount:</th>
                                    <td class="text-end">{{ number_format($deliveryNote->discount_amount, 3) }}</td>
                                </tr>
                                <tr>
                                    <th>Total Tax:</th>
                                    <td class="text-end">{{ number_format($deliveryNote->tax_amount, 3) }}</td>
                                </tr>
                                <tr class="table-active">
                                    <th>Grand Total:</th>
                                    <td class="text-end">
                                        <h4>{{ number_format($deliveryNote->total_amount, 3) }}</h4>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Status Update -->
                    @if($deliveryNote->delivery_status == 'Pending')
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="card border-warning">
                                    <div class="card-header bg-warning text-dark">
                                        <h6 class="mb-0">Update Delivery Status</h6>
                                    </div>
                                    <div class="card-body">
                                        <form action="{{ route('deliveries.update_status', $deliveryNote->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <select class="form-select" name="delivery_status" required>
                                                        <option value="Pending" {{ $deliveryNote->delivery_status == 'Pending' ? 'selected' : '' }}>Pending</option>
                                                        <option value="Delivered">Delivered</option>
                                                        <option value="Cancelled">Cancelled</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <textarea class="form-control" name="delivery_notes" placeholder="Additional notes..." rows="1">{{ $deliveryNote->delivery_notes }}</textarea>
                                                </div>
                                                <div class="col-md-2">
                                                    <button type="submit" class="btn btn-warning w-100">
                                                        <i class="fas fa-save"></i> Update
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection