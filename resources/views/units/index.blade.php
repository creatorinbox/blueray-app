@extends('layouts.app')

@section('title','Units')

@section('content')
<div class="page-header">
    <h1 class="page-title">Units</h1>
    <a href="{{ route('units.create') }}" class="btn btn-sm btn-primary">Add Unit</a>
</div>

<div class="card mt-3">
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Symbol</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($units as $unit)
                    <tr>
                        <td>{{ $unit->name }}</td>
                        <td>{{ $unit->symbol }}</td>
                        <td>
                            <a href="{{ route('units.edit', $unit) }}" class="btn btn-sm btn-secondary">Edit</a>
                            <form action="{{ route('units.destroy', $unit) }}" method="POST" style="display:inline" onsubmit="return confirm('Delete unit?');">
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
