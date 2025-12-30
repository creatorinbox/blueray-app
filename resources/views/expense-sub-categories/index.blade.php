@extends('layouts.app')

@section('title', 'Expense Subcategories')

@section('content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h1 class="page-title">
                <i class="fas fa-layer-group me-2"></i>Expense Subcategories
            </h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('expense-sub-categories.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add New Subcategory
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <!-- Alerts -->
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Subcategories List</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="subCategoriesTable">
                        <thead class="table-light">
                            <tr>
                                <th width="8%">#</th>
                                <th width="25%">Category</th>
                                <th width="25%">Subcategory Name</th>
                                <th width="32%">Description</th>
                                <th width="10%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($subCategories as $index => $subCategory)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <span class="badge bg-primary">{{ $subCategory->category_name ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <strong>{{ $subCategory->sub_category_name }}</strong>
                                </td>
                                <td>
                                    @if($subCategory->description)
                                        {{ Str::limit($subCategory->description, 100) }}
                                    @else
                                        <span class="text-muted">No description</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('expense-sub-categories.edit', $subCategory->id) }}" class="btn btn-outline-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('expense-sub-categories.destroy', $subCategory->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this subcategory?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-3x mb-3 opacity-50"></i>
                                    <br>
                                    No subcategories found. 
                                    <a href="{{ route('expense-sub-categories.create') }}" class="text-primary">Create your first subcategory</a>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#subCategoriesTable').DataTable({
        responsive: true,
        pageLength: 25,
        order: [[1, 'asc'], [2, 'asc']],
        columnDefs: [
            { orderable: false, targets: [4] } // Disable sorting for Actions column
        ],
        language: {
            search: "Search subcategories:",
            lengthMenu: "Show _MENU_ subcategories per page",
            info: "Showing _START_ to _END_ of _TOTAL_ subcategories",
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            }
        }
    });
});
</script>
@endpush