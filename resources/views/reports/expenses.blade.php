@extends('adminlte::page')

@section('title', 'Expenses Report')

@section('content_header')
    <h1>Expenses Report</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Business Expenses</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-sm btn-primary" data-toggle="collapse" data-target="#filterCollapse">
                    <i class="fas fa-filter"></i> Filters
                </button>
            </div>
        </div>
        <div class="card-body">
            <!-- Filters -->
            <div class="collapse mb-3" id="filterCollapse">
                <form method="GET" action="{{ route('admin.reports.expenses') }}">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Date From</label>
                                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Date To</label>
                                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
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
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary btn-block">Apply Filters</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Category Breakdown -->
            @if($categoryTotals->isNotEmpty())
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Expense Breakdown by Category</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach($categoryTotals as $category => $total)
                                <div class="col-md-3 mb-2">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-info">
                                            <i class="fas fa-tag"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">{{ $category }}</span>
                                            <span class="info-box-number">UGX {{ number_format($total, 0) }}</span>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <div class="text-center mt-2">
                                <h4>Total Expenses: <span class="text-danger">UGX {{ number_format($totalExpenses, 0) }}</span></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

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
                                <td>{{ \Illuminate\Support\Str::limit($expense->description ?? 'N/A', 40) }}</td>
                                <td><strong>UGX {{ number_format($expense->amount, 0) }}</strong></td>
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
                                    <a href="{{ route('admin.expenses.show', $expense) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No expenses found</td>
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
