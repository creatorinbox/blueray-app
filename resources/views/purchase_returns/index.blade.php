@extends('layouts.app')
@section('title', 'Purchase Returns')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3">Purchase Returns</h1>
        <a href="{{ route('purchase-returns.create') }}" class="btn btn-primary">New Return</a>
    </div>
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead class="table-primary">
                    <tr>
                        <th>Return No</th>
                        <th>Date</th>
                        <th>Supplier</th>
                        <th>Amount</th>
                        <th>VAT Reversal</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($returns as $return)
                    <tr>
                        <td>{{ $return->return_no }}</td>
                        <td>{{ $return->date->format('d-m-Y') }}</td>
                        <td>{{ $return->supplier->supplier_name ?? '-' }}</td>
                        <td>{{ number_format($return->total_amount, 3) }}</td>
                        <td>{{ number_format($return->vat_reversal, 3) }}</td>
                        <td>
                            <a href="{{ route('purchase-returns.show', $return) }}" class="btn btn-sm btn-info">View</a>
                            <form action="{{ route('purchase-returns.destroy', $return) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this return?")">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center">No returns found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
