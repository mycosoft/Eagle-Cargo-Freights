@extends('adminlte::page')

@section('title', 'Profit & Loss Report')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1><i class="fas fa-chart-line mr-2"></i>Profit & Loss Report</h1>
        </div>
        <div class="col-sm-6">
            <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary float-right">
                <i class="fas fa-arrow-left"></i> Back to Reports
            </a>
        </div>
    </div>
@stop

@section('content')
    <!-- Filters -->
    <div class="card">
        <div class="card-header"><h3 class="card-title">Filter</h3></div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reports.profit-loss') }}">
                <div class="row">
                    <div class="col-md-3">
                        <label>From Date</label>
                        <input type="date" name="from_date" class="form-control" value="{{ $fromDate }}">
                    </div>
                    <div class="col-md-3">
                        <label>To Date</label>
                        <input type="date" name="to_date" class="form-control" value="{{ $toDate }}">
                    </div>
                    <div class="col-md-3">
                        <label>Cargo Type</label>
                        <select name="type" class="form-control">
                            <option value="all" {{ $type === 'all' ? 'selected' : '' }}>All Cargo</option>
                            <option value="air" {{ $type === 'air' ? 'selected' : '' }}>Air Cargo</option>
                            <option value="sea" {{ $type === 'sea' ? 'selected' : '' }}>Sea Cargo</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>&nbsp;</label>
                        <div><button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Filter</button></div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary -->
    <div class="row">
        <div class="col-lg-4 col-12">
            <div class="info-box">
                <span class="info-box-icon bg-success"><i class="fas fa-money-bill-wave"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Revenue</span>
                    <span class="info-box-number">${{ number_format($totalRevenue, 0) }}</span>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-12">
            <div class="info-box">
                <span class="info-box-icon bg-warning"><i class="fas fa-dollar-sign"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Costs</span>
                    <span class="info-box-number">${{ number_format($totalCost, 0) }}</span>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-12">
            <div class="info-box">
                <span class="info-box-icon {{ $totalProfit >= 0 ? 'bg-success' : 'bg-danger' }}"><i class="fas fa-coins"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Profit</span>
                    <span class="info-box-number">${{ number_format($totalProfit, 0) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Counts -->
    <div class="row mb-3">
        <div class="col-md-2"><span class="badge badge-info">Air: {{ $airTotal }} shipments</span></div>
        <div class="col-md-2"><span class="badge badge-primary">Sea: {{ $seaTotal }} shipments</span></div>
        <div class="col-md-2"><span class="badge badge-secondary">Total: {{ count($data) }} shipments</span></div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-header"><h3 class="card-title">Shipment Profit Breakdown</h3></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Tracking #</th>
                            <th>Client</th>
                            <th>Type</th>
                            <th>Date</th>
                            <th>Revenue</th>
                            <th>Cost</th>
                            <th>Profit</th>
                            <th>Margin</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $row)
                            @php $margin = $row['revenue'] > 0 ? round(($row['profit'] / $row['revenue']) * 100, 1) : 0; @endphp
                            <tr>
                                <td><strong>{{ $row['tracking'] }}</strong></td>
                                <td>{{ $row['client'] }}</td>
                                <td><span class="badge badge-{{ $row['type'] === 'air' ? 'info' : 'primary' }}">{{ ucfirst($row['type']) }}</span></td>
                                <td>{{ $row['date'] }}</td>
                                <td>${{ number_format($row['revenue'], 0) }}</td>
                                <td>${{ number_format($row['cost'], 0) }}</td>
                                <td class="{{ $row['profit'] >= 0 ? 'text-success' : 'text-danger' }}">${{ number_format($row['profit'], 0) }}</td>
                                <td><span class="badge badge-{{ $margin >= 0 ? 'success' : 'danger' }}">{{ $margin }}%</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center text-muted">No data for selected period</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop

@section('footer')
    <strong>Copyright &copy; {{ date('Y') }} <a href="#">Eagle Cargo Freights</a>.</strong> All rights reserved.
@stop