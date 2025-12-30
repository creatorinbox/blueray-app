@extends('layouts.app')

@section('title', 'Edit Service')

@section('content')
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-edit me-2"></i>Edit Service
    </h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('services.index') }}">Services</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol>
    </nav>
</div>

<form action="{{ route('services.update', $service->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Service Information</h5>
        </div>
        <div class="card-body">
            <div class="row mb-3 d-none">
                <label class="col-sm-2 col-form-label">Stock Type</label>
                <div class="col-sm-4">
                    <input type="hidden" name="stock_type" value="Service">
                    <input type="text" class="form-control" value="Service" readonly>
                </div>
            </div>

            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Service Name <span class="text-danger">*</span></label>
                <div class="col-sm-10">
                    <input type="text" name="item_name" value="{{ old('item_name', $service->item_name) }}" class="form-control @error('item_name') is-invalid @enderror" required>
                    @error('item_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Price <span class="text-danger">*</span></label>
                <div class="col-sm-4">
                    <input type="number" name="sale_price" value="{{ old('sale_price', $service->sale_price) }}" step="0.001" min="0" class="form-control @error('sale_price') is-invalid @enderror" required>
                    @error('sale_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Description</label>
                <div class="col-sm-10">
                    <textarea name="description" class="form-control">{{ old('description', $service->description) }}</textarea>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <div class="row">
                <div class="col-md-3 offset-md-3">
                    <button type="submit" class="btn btn-success w-100">Update Service</button>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('services.index') }}" class="btn btn-secondary w-100">Cancel</a>
                </div>
            </div>
        </div>
    </div>
</form>

@endsection
