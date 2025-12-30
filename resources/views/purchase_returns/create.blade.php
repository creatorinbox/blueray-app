@extends('layouts.app')
@section('title', 'New Purchase Return')
@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-3">New Purchase Return</h1>
    <form action="{{ route('purchase-returns.store') }}" method="POST">
        @csrf
        <div class="card mb-3">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label>Return No</label>
                        <input type="text" name="return_no" class="form-control" required value="{{ old('return_no') }}">
                    </div>
                    <div class="col-md-3">
                        <label>Date</label>
                        <input type="date" name="date" class="form-control" required value="{{ old('date', date('Y-m-d')) }}">
                    </div>
                    <div class="col-md-3">
                        <label>Supplier</label>
                        <select name="supplier_id" class="form-control" required>
                            <option value="">Select</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->supplier_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>GRN Reference</label>
                        <select name="grn_id" class="form-control">
                            <option value="">Select</option>
                            @foreach($grns as $grn)
                                <option value="{{ $grn->id }}" {{ old('grn_id') == $grn->id ? 'selected' : '' }}>{{ $grn->grn_no }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label>Reason</label>
                        <textarea name="reason" class="form-control">{{ old('reason') }}</textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mb-3">
            <div class="card-header"><strong>Return Items</strong></div>
            <div class="card-body">
                <table class="table table-bordered" id="itemsTable">
                    <thead class="table-primary">
                        <tr>
                            <th>Item</th>
                            <th>Lot No</th>
                            <th>Quantity</th>
                            <th>Rate</th>
                            <th>Amount</th>
                            <th>Reason</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(old('items'))
                            @foreach(old('items') as $i => $item)
                                <tr>
                                    <td>
                                        <select name="items[{{ $i }}][item_id]" class="form-control" required>
                                            <option value="">Select</option>
                                            @foreach($items as $itm)
                                                <option value="{{ $itm->id }}" {{ $item['item_id'] == $itm->id ? 'selected' : '' }}>{{ $itm->item_name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td><input type="text" name="items[{{ $i }}][lot_id]" class="form-control" value="{{ $item['lot_id'] }}"></td>
                                    <td><input type="number" step="0.01" name="items[{{ $i }}][qty]" class="form-control" value="{{ $item['qty'] }}" required></td>
                                    <td><input type="number" step="0.01" name="items[{{ $i }}][rate]" class="form-control" value="{{ $item['rate'] }}" required></td>
                                    <td><input type="number" step="0.01" name="items[{{ $i }}][amount]" class="form-control" value="{{ $item['amount'] }}" required></td>
                                    <td><input type="text" name="items[{{ $i }}][reason]" class="form-control" value="{{ $item['reason'] }}"></td>
                                    <td><button type="button" class="btn btn-danger btn-sm remove-row">Remove</button></td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
                <button type="button" class="btn btn-success btn-sm" id="addRowBtn">Add Item</button>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Save Return</button>
    </form>
</div>
@push('scripts')
<script>
    let rowIdx = {{ old('items') ? count(old('items')) : 0 }};
    const items = @json($items);
    $('#addRowBtn').on('click', function() {
        let row = `<tr>
            <td><select name="items[
                ${rowIdx}][item_id]" class="form-control" required><option value="">Select</option>`;
        items.forEach(function(itm) {
            row += `<option value="${itm.id}">${itm.item_name}</option>`;
        });
        row += `</select></td>
            <td><input type="text" name="items[${rowIdx}][lot_id]" class="form-control"></td>
            <td><input type="number" step="0.01" name="items[${rowIdx}][qty]" class="form-control" required></td>
            <td><input type="number" step="0.01" name="items[${rowIdx}][rate]" class="form-control" required></td>
            <td><input type="number" step="0.01" name="items[${rowIdx}][amount]" class="form-control" required></td>
            <td><input type="text" name="items[${rowIdx}][reason]" class="form-control"></td>
            <td><button type="button" class="btn btn-danger btn-sm remove-row">Remove</button></td>
        </tr>`;
        $('#itemsTable tbody').append(row);
        rowIdx++;
    });
    $(document).on('click', '.remove-row', function() {
        $(this).closest('tr').remove();
    });
</script>
@endpush
@endsection
