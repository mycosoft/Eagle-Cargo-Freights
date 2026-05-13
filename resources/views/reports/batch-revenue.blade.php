@extends('adminlte::page')

@section('title', 'Batch Revenue Report')

@section('content_header')
    <h1>Batch Revenue Report</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Revenue by Batch</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-sm btn-primary" data-toggle="collapse" data-target="#filterCollapse">
                    <i class="fas fa-filter"></i> Filters
                </button>
            </div>
        </div>
        <div class="card-body">
            <!-- Filters -->
            <div class="collapse mb-3" id="filterCollapse">
                <form method="GET" action="{{ route('admin.reports.batch-revenue') }}">
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
                                <label>Cargo Type</label>
                                <select name="cargo_type" class="form-control">
                                    <option value="">All Types</option>
                                    <option value="air" {{ request('cargo_type') == 'air' ? 'selected' : '' }}>Air Cargo</option>
                                    <option value="sea" {{ request('cargo_type') == 'sea' ? 'selected' : '' }}>Sea Cargo</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="">All Statuses</option>
                                    <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="In Transit" {{ request('status') == 'In Transit' ? 'selected' : '' }}>In Transit</option>
                                    <option value="Delivered" {{ request('status') == 'Delivered' ? 'selected' : '' }}>Delivered</option>
                                    <option value="Cancelled" {{ request('status') == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">Apply Filters</button>
                            <a href="{{ route('admin.reports.batch-revenue') }}" class="btn btn-secondary">Clear Filters</a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Summary Cards -->
            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="info-box bg-gradient-success">
                        <span class="info-box-icon"><i class="fas fa-money-bill-wave"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Revenue</span>
                             <span class="info-box-number">{{ \App\Models\Setting::getCurrencySymbol() }} {{ number_format($totalRevenue, 0) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-box bg-gradient-info">
                        <span class="info-box-icon"><i class="fas fa-file-invoice-dollar"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Invoiced</span>
                             <span class="info-box-number">{{ \App\Models\Setting::getCurrencySymbol() }} {{ number_format($totalInvoiced, 0) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-box bg-gradient-warning">
                        <span class="info-box-icon"><i class="fas fa-exclamation-triangle"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Outstanding</span>
                             <span class="info-box-number">{{ \App\Models\Setting::getCurrencySymbol() }} {{ number_format($totalOutstanding, 0) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Batches Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Batch #</th>
                            <th>Name</th>
                            <th>Cargo Type</th>
                            <th>Status</th>
                            <th>Shipments</th>
                            <th>Invoiced</th>
                            <th>Collected</th>
                            <th>Outstanding</th>
                            <th>Collection Rate</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($batches as $batch)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.batches.show', $batch) }}">
                                        {{ $batch->batch_number }}
                                    </a>
                                </td>
                                <td>{{ $batch->name }}</td>
                                <td>
                                    <span class="badge badge-{{ $batch->cargo_type == 'air' ? 'info' : 'primary' }}">
                                        {{ ucfirst($batch->cargo_type) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $batch->current_status == 'Delivered' ? 'success' : ($batch->current_status == 'Pending' ? 'warning' : 'info') }}">
                                        {{ $batch->current_status }}
                                    </span>
                                </td>
                                <td class="text-center">{{ $batch->shipments->count() }}</td>
                                 <td>{{ \App\Models\Setting::getCurrencySymbol() }} {{ number_format($batch->invoiced_amount, 0) }}</td>
                                 <td>{{ \App\Models\Setting::getCurrencySymbol() }} {{ number_format($batch->revenue, 0) }}</td>
                                 <td>{{ \App\Models\Setting::getCurrencySymbol() }} {{ number_format($batch->outstanding_amount, 0) }}</td>
                                <td>
                                    @if($batch->invoiced_amount > 0)
                                        <div class="progress">
                                            <div class="progress-bar bg-{{ $batch->revenue >= $batch->invoiced_amount ? 'success' : 'warning' }}"
                                                 role="progressbar"
                                                 style="width: {{ ($batch->revenue / $batch->invoiced_amount) * 100 }}%"
                                                 aria-valuenow="{{ $batch->revenue }}"
                                                 aria-valuemin="0"
                                                 aria-valuemax="{{ $batch->invoiced_amount }}">
                                                {{ round(($batch->revenue / $batch->invoiced_amount) * 100) }}%
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ $batch->created_at->format('d M Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.batches.show', $batch) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center">No batches found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $batches->links() }}
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
