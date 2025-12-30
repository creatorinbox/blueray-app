@extends('layouts.app')

@section('title','Add Unit')

@section('content')
<div class="page-header">
    <h1 class="page-title">Add Unit</h1>
</div>

<form action="{{ route('units.store') }}" method="POST">
    @csrf
    <div class="card">
        <div class="card-body">
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Name <span class="text-danger">*</span></label>
                <div class="col-sm-4">
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <label class="col-sm-2 col-form-label">Symbol</label>
                <div class="col-sm-4">
                    <input type="text" name="symbol" class="form-control @error('symbol') is-invalid @enderror" value="{{ old('symbol') }}">
                    @error('symbol')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button class="btn btn-primary">Save</button>
            <a href="{{ route('units.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </div>
</form>
@endsection
