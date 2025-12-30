@extends('layouts.app')
@section('title', 'Purchase Return Details')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3">Purchase Return #{{ $return->return_no }}</h1>
        <a href="{{ route('purchase-returns.index') }}" class="btn btn-secondary">Back to List</a>
    </div>
    <div class="card mb-3">
        <div class="card-body">
            <div class="row mb-2">
                <div class="col-md-3"><strong>Date:</strong> {{ $return->date->format('d-m-Y') }}</div>
                <div class="col-md-3"><strong>Supplier:</strong> {{ $return->supplier->supplier_name ?? '-' }}</div>
                <div class="col-md-3"><strong>GRN Ref:</strong> {{ $return->grn?->grn_no ?? '-' }}</div>
                <div class="col-md-3"><strong>VAT Reversal:</strong> {{ number_format($return->vat_reversal, 3) }}</div>
            </div>
            <div class="row mb-2">
                <div class="col-md-12"><strong>Reason:</strong> {{ $return->reason ?? '-' }}</div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead class="table-primary">
                    <tr>
                        <th>Item</th>
                        <th>Lot No</th>
                        <th>Quantity</th>
                        <th>Rate</th>
                        <th>Amount</th>
                        <th>Reason</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($return->items as $item)
                    <tr>
                        <td>{{ $item->item->item_name ?? '-' }}</td>
                        <td>{{ $item->lot?->lot_no ?? '-' }}</td>
                        <td>{{ number_format($item->qty, 2) }}</td>
                        <td>{{ number_format($item->rate, 3) }}</td>
                        <td>{{ number_format($item->amount, 3) }}</td>
                        <td>{{ $item->reason ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
