@extends('adminlte::page')

@section('title', 'Create Role')

@section('content_header')
    <h1>Create New Role</h1>
@stop

@section('content')
    <div class="card">
        <form action="{{ url('admin/roles') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="form-group">
                    <label>Role Name</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                    @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label>Permissions</label>
                    <div class="row">
                        @php
                            $groupedPermissions = [
                                'Clients' => $permissions->filter(fn($p) => str_contains($p->name, 'clients')),
                                'Shipments' => $permissions->filter(fn($p) => str_contains($p->name, 'shipments')),
                                'Air Cargo' => $permissions->filter(fn($p) => str_contains($p->name, 'air cargo')),
                                'Sea Cargo' => $permissions->filter(fn($p) => str_contains($p->name, 'sea cargo')),
                                'Batches' => $permissions->filter(fn($p) => str_contains($p->name, 'batches')),
                                'Invoices' => $permissions->filter(fn($p) => str_contains($p->name, 'invoices')),
                                'Payments' => $permissions->filter(fn($p) => str_contains($p->name, 'payments')),
                                'Expenses' => $permissions->filter(fn($p) => str_contains($p->name, 'expenses') && !str_contains($p->name, 'categories')),
                                'Expense Categories' => $permissions->filter(fn($p) => str_contains($p->name, 'expense categories')),
                                'Transactions' => $permissions->filter(fn($p) => str_contains($p->name, 'transactions')),
                                'Reports' => $permissions->filter(fn($p) => str_contains($p->name, 'reports') || str_contains($p->name, 'outstanding balance') || str_contains($p->name, 'paid invoices')),
                                'Users' => $permissions->filter(fn($p) => str_contains($p->name, 'users')),
                                'Roles' => $permissions->filter(fn($p) => str_contains($p->name, 'roles')),
                                'Notifications' => $permissions->filter(fn($p) => str_contains($p->name, 'notifications') || str_contains($p->name, 'bulk messages') || str_contains($p->name, 'broadcast')),
                                'Settings & Tracking' => $permissions->filter(fn($p) => str_contains($p->name, 'settings') || str_contains($p->name, 'tracking') || str_contains($p->name, 'status updates')),
                            ];
                        @endphp

                        @foreach($groupedPermissions as $groupName => $groupPermissions)
                            @if($groupPermissions->count() > 0)
                                <div class="col-12 mt-3">
                                    <h6><i class="fas fa-folder-open"></i> {{ ucfirst($groupName) }}</h6>
                                </div>
                                @foreach($groupPermissions as $permission)
                                    <div class="col-md-3">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" class="custom-control-input" id="perm_{{ $permission->id }}">
                                            <label class="custom-control-label" for="perm_{{ $permission->id }}">{{ $permission->name }}</label>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Create Role</button>
                <a href="{{ url('admin/roles') }}" class="btn btn-default">Cancel</a>
            </div>
        </form>
    </div>
@stop



@section('footer')
    <strong>Copyright &copy; {{ date('Y') }} <a href="#">Eagle Cargo Freights</a>.</strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
        <b>Support Call</b> +256 200 991 118
    </div>
@stop

