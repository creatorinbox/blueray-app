@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Delivery Order - Customer Copy</h2>
        <div>
            <button class="btn btn-secondary" onclick="window.print()">Print</button>
        </div>
    </div>

    <div class="mb-2">
        <strong>DO #:</strong> {{ $order->id }}<br>
        <strong>Invoice #:</strong> {{ $order->invoice_no ?? '-' }}<br>
        <strong>Date:</strong> {{ $order->delivery_date }}<br>
        <strong>Customer:</strong> {{ $order->customer->customer_name ?? ($order->customer->name ?? '-') }}
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Item</th>
                <th>Quantity</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $it)
                <tr>
                    <td>{{ $it->item->item_name ?? '-' }}</td>
                    <td>{{ $it->quantity }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
