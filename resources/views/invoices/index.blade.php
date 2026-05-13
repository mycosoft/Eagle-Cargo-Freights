@extends('adminlte::page')

@section('title', 'Invoices')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>Invoices</h1>
        </div>
    </div>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Invoice List</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-sm btn-primary" data-toggle="collapse" data-target="#filterSection">
                    <i class="fas fa-filter"></i> Filters
                </button>
            </div>
        </div>
        
        <!-- Filter Section -->
        <div class="collapse" id="filterSection">
            <div class="card-body border-bottom">
                <form action="{{ route('admin.invoices.index') }}" method="GET">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="">All Statuses</option>
                                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                                    <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Partial</option>
                                    <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>
                        </div>
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
                                <label>Search</label>
                                <input type="text" name="search" class="form-control" placeholder="Invoice # or Tracking #" value="{{ request('search') }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Apply Filters
                            </button>
                            <a href="{{ route('admin.invoices.index') }}" class="btn btn-default">
                                <i class="fas fa-redo"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>Invoice #</th>
                        <th>Tracking #</th>
                        <th>Client</th>
                        <th>Issue Date</th>
                        <th>Due Date</th>
                        <th>Amount</th>
                        <th>Paid</th>
                        <th>Balance</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $invoice)
                        <tr class="{{ $invoice->status === 'overdue' ? 'text-danger font-weight-bold' : '' }}">
                            <td>
                                <a href="{{ route('admin.invoices.show', $invoice) }}" class="{{ $invoice->status === 'overdue' ? 'text-danger' : '' }}">
                                    <strong>{{ $invoice->invoice_number }}</strong>
                                </a>
                            </td>
                            <td>
                                <a href="{{ route('admin.shipments.show', $invoice->shipment) }}" class="{{ $invoice->status === 'overdue' ? 'text-danger' : '' }}">
                                    {{ $invoice->shipment->tracking_number }}
                                </a>
                            </td>
                            <td>{{ $invoice->shipment->client->name }}</td>
                            <td>{{ $invoice->issue_date->format('M d, Y') }}</td>
                            <td>{{ $invoice->due_date ? $invoice->due_date->format('M d, Y') : 'N/A' }}</td>
                            <td>{{ \App\Models\Setting::getCurrencySymbol($invoice->shipment->currency ?? null) }} {{ number_format($invoice->total, 2) }}</td>
                            <td>{{ \App\Models\Setting::getCurrencySymbol($invoice->shipment->currency ?? null) }} {{ number_format($invoice->amount_paid, 2) }}</td>
                            <td>{{ \App\Models\Setting::getCurrencySymbol($invoice->shipment->currency ?? null) }} {{ number_format($invoice->balance, 2) }}</td>
                            <td>
                                @php
                                    $statusColors = [
                                        'draft' => 'secondary',
                                        'sent' => 'info',
                                        'paid' => 'success',
                                        'partial' => 'warning',
                                        'overdue' => 'danger',
                                        'cancelled' => 'dark'
                                    ];
                                    $badgeClass = $statusColors[$invoice->status] ?? 'secondary';
                                @endphp
                                <span class="badge badge-{{ $badgeClass }}">{{ ucfirst($invoice->status) }}</span>
                            </td>
                            <td>
                                <a href="{{ route('admin.invoices.show', $invoice) }}" class="btn btn-sm btn-info" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.invoices.pdf', $invoice) }}" class="btn btn-sm btn-danger" title="Download PDF">
                                    <i class="fas fa-file-pdf"></i>
                                </a>
                                <a href="{{ route('admin.shipments.invoice', $invoice->shipment) }}" target="_blank" class="btn btn-sm btn-secondary" title="Print">
                                    <i class="fas fa-print"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center">No invoices found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer clearfix">
            {{ $invoices->links('pagination::bootstrap-4') }}
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row">
        <div class="col-md-3">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $invoices->total() }}</h3>
                    <p>Total Invoices</p>
                </div>
                <div class="icon">
                    <i class="fas fa-file-invoice"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $invoices->where('status', 'paid')->count() }}</h3>
                    <p>Paid Invoices</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $invoices->where('status', 'sent')->count() }}</h3>
                    <p>Pending Payment</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $invoices->where('status', 'overdue')->count() }}</h3>
                    <p>Overdue</p>
                </div>
                <div class="icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
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
