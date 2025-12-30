@extends('layouts.app')
@section('content')
<div class="container">
    <h2>Search Items by Lot</h2>
    <form method="GET" action="{{ route('items.lot_search') }}" class="mb-4">
        <div class="row">
            <div class="col-md-2">
                <input type="text" name="lot_no" class="form-control" placeholder="Lot Number" value="{{ request('lot_no') }}">
            </div>
            <div class="col-md-2">
                <input type="date" name="expiry_date" class="form-control" placeholder="Expiry Date" value="{{ request('expiry_date') }}">
            </div>
            <div class="col-md-2">
                <input type="number" name="price_min" class="form-control" placeholder="Min Price" value="{{ request('price_min') }}">
            </div>
            <div class="col-md-2">
                <input type="number" name="price_max" class="form-control" placeholder="Max Price" value="{{ request('price_max') }}">
            </div>
            <div class="col-md-2">
                <input type="number" name="qty_min" class="form-control" placeholder="Min Qty" value="{{ request('qty_min') }}">
            </div>
            <div class="col-md-2">
                <input type="number" name="qty_max" class="form-control" placeholder="Max Qty" value="{{ request('qty_max') }}">
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-md-2">
                <select name="supplier_id" class="form-control">
                    <option value="">Supplier</option>
                    @foreach(App\Models\Supplier::all() as $supplier)
                        <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->supplier_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">Search</button>
            </div>
        </div>
    </form>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Lot No</th>
                    <th>Item Name</th>
                    <th>Expiry Date</th>
                    <th>Quantity</th>
                    <th>Supplier</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                @forelse($lots as $lot)
                <tr>
                    <td>{{ $lot->lot_no }}</td>
                    <td>{{ $lot->item->item_name ?? '-' }}</td>
                    <td>{{ $lot->expiry_date ? $lot->expiry_date->format('d M Y') : '-' }}</td>
                    <td>{{ $lot->qty_available }}</td>
                    <td>{{ $lot->item->supplier->supplier_name ?? '-' }}</td>
                    <td>{{ number_format($lot->cost_price, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center">No lots found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
