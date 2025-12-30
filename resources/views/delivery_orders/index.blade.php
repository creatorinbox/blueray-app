@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Delivery Orders</h1>
    <a href="{{ route('delivery_orders.create') }}" class="btn btn-primary mb-3">Create Delivery Order</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Customer</th>
                <th>Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
                <tr>
                    <td>{{ $order->id }}</td>
                    <td>{{ $order->customer->name ?? '-' }}</td>
                    <td>{{ $order->delivery_date }}</td>
                    <td>{{ ucfirst($order->status) }}</td>
                    <td>
                        <a href="{{ route('delivery_orders.show', $order) }}" class="btn btn-info btn-sm">View</a>
                        @if($order->status !== 'completed')
                        <form action="{{ route('delivery_orders.complete', $order) }}" method="POST" style="display:inline-block;">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm">Complete</button>
                        </form>
                        @endif
                        <a href="{{ route('delivery_orders.print_store', $order) }}" target="_blank" class="btn btn-secondary btn-sm">Print (Store Copy)</a>
                        <a href="{{ route('delivery_orders.print_customer', $order) }}" target="_blank" class="btn btn-outline-secondary btn-sm">Print (Customer Copy)</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
