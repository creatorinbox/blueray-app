@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Delivery Order #{{ $deliveryOrder->id }}</h1>
    <p><strong>Customer:</strong> {{ $deliveryOrder->customer->name ?? '-' }}</p>
    <p><strong>Date:</strong> {{ $deliveryOrder->delivery_date }}</p>
    <p><strong>Status:</strong> {{ ucfirst($deliveryOrder->status) }}</p>
    <p><strong>Remarks:</strong> {{ $deliveryOrder->remarks }}</p>
    <h4>Items</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Item</th>
                <th>Quantity</th>
            </tr>
        </thead>
        <tbody>
            @foreach($deliveryOrder->items as $item)
                <tr>
                    <td>{{ $item->item->item_name ?? '-' }}</td>
                    <td>{{ $item->quantity }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <a href="{{ route('delivery_orders.index') }}" class="btn btn-secondary">Back</a>
    @if($deliveryOrder->status !== 'completed')
    <form action="{{ route('delivery_orders.complete', $deliveryOrder) }}" method="POST" style="display:inline-block;">
        @csrf
        <button type="submit" class="btn btn-success">Complete Delivery</button>
    </form>
    @endif
</div>
@endsection
