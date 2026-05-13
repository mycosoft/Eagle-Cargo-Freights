@extends('adminlte::page')

@section('title', 'Revenue Report')

@section('content_header')
    <h1>Revenue Report</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Payment History & Revenue</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-sm btn-primary" data-toggle="collapse" data-target="#filterCollapse">
                    <i class="fas fa-filter"></i> Filters
                </button>
            </div>
        </div>
        <div class="card-body">
            <!-- Filters -->
            <div class="collapse mb-3" id="filterCollapse">
                <form method="GET" action="{{ route('admin.reports.revenue') }}">
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
                                <label>Payment Method</label>
                                <select name="payment_method" class="form-control">
                                    <option value="">All Methods</option>
                                    <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="card" {{ request('payment_method') == 'card' ? 'selected' : '' }}>Card</option>
                                    <option value="bank_transfer" {{ request('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                    <option value="mobile_money" {{ request('payment_method') == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
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

            <!-- Summary -->
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="info-box bg-gradient-success">
                        <span class="info-box-icon"><i class="fas fa-money-bill-wave"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Revenue (Filtered)</span>
                            <span class="info-box-number">{{ \App\Models\Setting::getCurrencySymbol() }} {{ number_format($totalRevenue, 0) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payments Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Receipt #</th>
                            <th>Date</th>
                            <th>Invoice #</th>
                            <th>Client</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Reference</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                            <tr>
                                <td>{{ $payment->receipt_number }}</td>
                                <td>{{ $payment->payment_date->format('d M Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.invoices.show', $payment->invoice_id) }}">
                                        {{ $payment->invoice->invoice_number }}
                                    </a>
                                </td>
                                <td>{{ $payment->invoice->shipment->client->name ?? 'N/A' }}</td>
                                 <td>{{ \App\Models\Setting::getCurrencySymbol($payment->invoice->shipment->currency ?? null) }} {{ number_format($payment->amount, 0) }}</td>
                                <td>
                                    <span class="badge badge-info">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</span>
                                </td>
                                <td>{{ $payment->reference_number ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No payments found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $payments->links() }}
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
