@extends('adminlte::page')

@section('title', 'Outstanding Invoices')

@section('content_header')
    <h1>Outstanding Invoices Report</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Unpaid & Pending Invoices</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-sm btn-warning" data-toggle="collapse" data-target="#filterCollapse">
                    <i class="fas fa-filter"></i> Filters
                </button>
            </div>
        </div>
        <div class="card-body">
            <!-- Filters -->
            <div class="collapse mb-3" id="filterCollapse">
                <form method="GET" action="{{ route('admin.reports.outstanding') }}">
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
                                    <option value="unpaid" {{ request('status') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                                    <option value="partially_paid" {{ request('status') == 'partially_paid' ? 'selected' : '' }}>Partially Paid</option>
                                    <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-warning btn-block">Apply Filters</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Summary -->
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="info-box bg-gradient-warning">
                        <span class="info-box-icon"><i class="fas fa-exclamation-triangle"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Outstanding Balance</span>
                            <span class="info-box-number">{{ \App\Models\Setting::getCurrencySymbol() }} {{ number_format($totalOutstanding, 0) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Invoices Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Invoice #</th>
                            <th>Date</th>
                            <th>Client</th>
                            <th>Total</th>
                            <th>Paid</th>
                            <th>Balance</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoices as $invoice)
                            <tr>
                                <td>{{ $invoice->invoice_number }}</td>
                                <td>{{ $invoice->issue_date->format('d M Y') }}</td>
                                <td>{{ $invoice->shipment->client->name ?? 'N/A' }}</td>
                                 <td>{{ \App\Models\Setting::getCurrencySymbol($invoice->shipment->currency ?? null) }} {{ number_format($invoice->total, 0) }}</td>
                                 <td>{{ \App\Models\Setting::getCurrencySymbol($invoice->shipment->currency ?? null) }} {{ number_format($invoice->amount_paid, 0) }}</td>
                                 <td>
                                     <strong class="text-danger">{{ \App\Models\Setting::getCurrencySymbol($invoice->shipment->currency ?? null) }} {{ number_format($invoice->balance, 0) }}</strong>
                                 </td>
                                <td>
                                    @if($invoice->status == 'unpaid')
                                        <span class="badge badge-danger">Unpaid</span>
                                    @elseif($invoice->status == 'partially_paid')
                                        <span class="badge badge-warning">Partially Paid</span>
                                    @elseif($invoice->status == 'overdue')
                                        <span class="badge badge-dark">Overdue</span>
                                    @else
                                        <span class="badge badge-secondary">{{ ucfirst($invoice->status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.invoices.show', $invoice->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No outstanding invoices found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $invoices->links() }}
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
