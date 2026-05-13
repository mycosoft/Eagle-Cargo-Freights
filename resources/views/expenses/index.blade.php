@extends('adminlte::page')

@section('title', 'Expenses')

@section('content_header')
    <h1>Expenses Management</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">All Expenses</h3>
            <div class="card-tools">
                @can('create expenses')
                <a href="{{ route('admin.expenses.create') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus"></i> New Expense
                </a>
                @endcan
                <button type="button" class="btn btn-sm btn-info" data-toggle="collapse" data-target="#filterCollapse">
                    <i class="fas fa-filter"></i> Filters
                </button>
            </div>
        </div>
        <div class="card-body">
            <!-- Filters -->
            <div class="collapse mb-3" id="filterCollapse">
                <form method="GET" action="{{ route('admin.expenses.index') }}">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="">All Statuses</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Currency</label>
                                <select name="currency" class="form-control">
                                    <option value="">All Currencies</option>
                                    <option value="UGX" {{ request('currency') == 'UGX' ? 'selected' : '' }}>UGX</option>
                                    <option value="USD" {{ request('currency') == 'USD' ? 'selected' : '' }}>USD</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Category</label>
                                <select name="category_id" class="form-control">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>From Date</label>
                                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>To Date</label>
                                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Search</label>
                                <input type="text" name="search" class="form-control" placeholder="Number, Ref, Desc..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn btn-primary">Apply</button>
                                    <a href="{{ route('admin.expenses.index') }}" class="btn btn-secondary">Clear</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Summary Cards -->
            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="info-box bg-info">
                        <span class="info-box-icon"><i class="fas fa-money-bill-wave"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">UGX Total</span>
                            <span class="info-box-number">UGX {{ number_format($totalUGX, 0) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-box bg-success">
                        <span class="info-box-icon"><i class="fas fa-dollar-sign"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">USD Total</span>
                            <span class="info-box-number">${{ number_format($totalUSD, 0) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-box bg-warning">
                        <span class="info-box-icon"><i class="fas fa-clock"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Pending</span>
                            <span class="info-box-number">{{ $pendingCount }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Expenses Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Expense #</th>
                            <th>Date</th>
                            <th>Category</th>
                            <th>Description</th>
                            <th>Amount</th>
                            <th>Currency</th>
                            <th>Payment Method</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenses as $expense)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.expenses.show', $expense) }}">
                                        {{ $expense->expense_number }}
                                    </a>
                                </td>
                                <td>{{ $expense->expense_date->format('d M Y') }}</td>
                                <td>
                                    <span class="badge" style="background-color: {{ $expense->category->color }}">
                                        {{ $expense->category->name }}
                                    </span>
                                </td>
                                <td>{{ Str::limit($expense->description ?? 'N/A', 40) }}</td>
                                <td>
                                    @if($expense->currency === 'USD')
                                        <strong>${{ number_format($expense->amount, 0) }}</strong>
                                    @else
                                        <strong>UGX {{ number_format($expense->amount, 0) }}</strong>
                                    @endif
                                </td>
                                <td><span class="badge badge-secondary">{{ $expense->currency ?? 'UGX' }}</span></td>
                                <td>
                                    <span class="badge badge-secondary">
                                        {{ ucfirst(str_replace('_', ' ', $expense->payment_method)) }}
                                    </span>
                                </td>
                                <td>
                                    @if($expense->status == 'pending')
                                        <span class="badge badge-warning">Pending</span>
                                    @elseif($expense->status == 'approved')
                                        <span class="badge badge-primary">Approved</span>
                                    @elseif($expense->status == 'rejected')
                                        <span class="badge badge-danger">Rejected</span>
                                    @elseif($expense->status == 'paid')
                                        <span class="badge badge-success">Paid</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.expenses.show', $expense) }}" class="btn btn-sm btn-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($expense->canBeEdited())
                                            @can('edit expenses')
                                            <a href="{{ route('admin.expenses.edit', $expense) }}" class="btn btn-sm btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endcan
                                        @endif
                                        @if($expense->canBeApproved())
                                            @can('approve expenses')
                                            <form action="{{ route('admin.expenses.approve', $expense) }}" method="POST" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success" title="Approve"
                                                        onclick="event.preventDefault(); if(confirm('Approve this expense?')) this.form.submit();">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            @endcan
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No expenses found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $expenses->links() }}
            </div>
        </div>
    </div>
@stop

@section('footer')
    <strong>Copyright &copy; {{ date('Y') }} <a href="#">Eagle Cargo Freights</a>.</strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
        <b>Support Call</b> +256 200 991 118
    </div>
@stop
