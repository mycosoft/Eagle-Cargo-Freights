@extends('adminlte::page')

@section('title', 'Expense Categories')

@section('content_header')
    <h1>Expense Categories</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Manage Categories</h3>
        </div>
        <div class="card-body">
            <!-- Create Category Form -->
            @can('create expense categories')
            <div class="row mb-4">
                <div class="col-md-12">
                    <h5>Create New Category</h5>
                    <form method="POST" action="{{ route('admin.expense-categories.store') }}" class="form-inline">
                        @csrf
                        <div class="form-group mr-2">
                            <input type="text" name="name" class="form-control" placeholder="Category Name" required>
                        </div>
                        <div class="form-group mr-2">
                            <input type="text" name="color" class="form-control color-picker" placeholder="#000000" value="#6c757d" required>
                        </div>
                        <div class="form-group mr-2">
                            <input type="text" name="description" class="form-control" placeholder="Description (optional)">
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add Category
                        </button>
                    </form>
                </div>
            </div>
            @endcan

            <!-- Categories Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th width="5%">Color</th>
                            <th width="25%">Name</th>
                            <th width="35%">Description</th>
                            <th width="15%">Expense Count</th>
                            <th width="10%">Status</th>
                            <th width="10%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                            <tr>
                                <td>
                                    <div style="width: 30px; height: 30px; background-color: {{ $category->color }}; border-radius: 5px; border: 1px solid #ddd;"></div>
                                </td>
                                <td><strong>{{ $category->name }}</strong></td>
                                <td>{{ $category->description ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge badge-info">{{ $category->expenses_count }}</span>
                                </td>
                                <td>
                                    @if($category->active)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        @can('edit expense categories')
                                        <button type="button" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#editModal{{ $category->id }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        @endcan
                                        @can('delete expense categories')
                                        @if($category->expenses_count == 0)
                                            <form method="POST" action="{{ route('admin.expense-categories.destroy', $category) }}" style="display: inline;" class="delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                        @endcan
                                    </div>
                                </td>
                            </tr>

                            @can('edit expense categories')
                            <!-- Edit Modal -->
                            <div class="modal fade" id="editModal{{ $category->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form method="POST" action="{{ route('admin.expense-categories.update', $category) }}">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Category</h5>
                                                <button type="button" class="close" data-dismiss="modal">
                                                    <span>&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label>Name</label>
                                                    <input type="text" name="name" class="form-control" value="{{ $category->name }}" required>
                                                </div>
                                                <div class="form-group">
                                                    <label>Color</label>
                                                    <input type="text" name="color" class="form-control color-picker" value="{{ $category->color }}" required>
                                                </div>
                                                <div class="form-group">
                                                    <label>Description</label>
                                                    <textarea name="description" class="form-control" rows="2">{{ $category->description ?? '' }}</textarea>
                                                </div>
                                                <div class="form-check">
                                                    <input type="checkbox" name="active" class="form-check-input" {{ $category->active ? 'checked' : '' }} value="1">
                                                    <label class="form-check-label">Active</label>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary">Save Changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endcan
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No categories found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $categories->links() }}
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.delete-form').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});
</script>
@stop

@section('footer')
    <strong>Copyright &copy; {{ date('Y') }} <a href="#">Eagle Cargo Freights</a>.</strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
        <b>Support Call</b> +256 200 991 118
    </div>
@stop
