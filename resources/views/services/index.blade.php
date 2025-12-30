@extends('layouts.app')

@section('title', 'Services')

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fas fa-concierge-bell me-2"></i>Services</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Services</li>
        </ol>
    </nav>
</div>

<div class="mb-3">
    <a href="{{ route('services.create') }}" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Add Service</a>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($services as $s)
                <tr>
                    <td>{{ $s->id }}</td>
                    <td>{{ $s->item_name }}</td>
                    <td>{{ number_format($s->sale_price, 3) }}</td>
                    <td>{{ \Illuminate\Support\Str::limit($s->description, 80) }}</td>
                    <td>
                        <a href="{{ route('services.edit', $s->id) }}" class="btn btn-sm btn-secondary">Edit</a>
                        <form action="{{ route('services.destroy', $s->id) }}" method="POST" style="display:inline-block" onsubmit="return confirm('Delete this service?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection
