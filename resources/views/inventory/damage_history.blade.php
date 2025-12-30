@extends('layouts.app')

@section('title', 'Damage History')

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fas fa-history me-2"></i>Damage History</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('stock.report') }}">Inventory</a></li>
            <li class="breadcrumb-item active">Damage History</li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-body">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-4">
                <select name="item_id" class="form-select">
                    <option value="">-- All Items --</option>
                    @foreach($items as $it)
                        <option value="{{ $it->id }}" {{ request('item_id') == $it->id ? 'selected' : '' }}>{{ $it->item_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <input type="date" name="from" class="form-control" value="{{ request('from') }}">
            </div>
            <div class="col-md-3">
                <input type="date" name="to" class="form-control" value="{{ request('to') }}">
            </div>
            <div class="col-md-2 text-end">
                <button class="btn btn-primary">Filter</button>
                <a href="{{ route('inventory.damage.history') }}" class="btn btn-secondary">Reset</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Item</th>
                        <th>Unit</th>
                        <th>Qty</th>
                        <th>Previous Stock</th>
                        <th>After Stock</th>
                        <th>User</th>
                        <th>Reason</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($histories as $h)
                        <tr>
                            <td>{{ $h->created_at->format('Y-m-d H:i') }}</td>
                            <td>{{ $h->item->item_name ?? '-' }}</td>
                            <td class="text-center">{{ $h->item->unit ?? '-' }}</td>
                            <td class="text-end">{{ number_format($h->qty, 2) }}</td>
                            <td class="text-end">{{ number_format($h->previous_stock ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($h->after_stock ?? 0, 2) }}</td>
                            <td>{{ $h->user->name ?? '-' }}</td>
                            <td>{{ $h->reason }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center">No records found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $histories->links() }}
        </div>
    </div>
</div>

@endsection
