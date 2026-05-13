@extends('adminlte::page')

@section('title', 'Invoice Details')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>Invoice {{ $invoice->invoice_number }}</h1>
        </div>
        <div class="col-sm-6">
            <div class="float-right">
                @can('create payments')
                @if($invoice->balance > 0)
                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#recordPaymentModal">
                    <i class="fas fa-money-bill-wave"></i> Record Payment
                </button>
                @endif
                @endcan
                @can('edit invoices')
                <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#storageFeeModal" title="Charge storage fee at client pickup">
                    <i class="fas fa-warehouse"></i> Add Storage Fee
                </button>
                <form action="{{ route('admin.invoices.send', $invoice) }}" method="POST" style="display: inline;" onsubmit="return confirm('Send this invoice to the client?');">
                    @csrf
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Send Invoice
                    </button>
                </form>
                @endcan
                <a href="{{ route('admin.invoices.pdf', $invoice) }}" class="btn btn-danger">
                    <i class="fas fa-file-pdf"></i> Download PDF
                </a>
                <a href="{{ route('admin.shipments.invoice', $invoice->shipment) }}" target="_blank" class="btn btn-secondary">
                    <i class="fas fa-print"></i> Print
                </a>
                <a href="{{ route('admin.invoices.index') }}" class="btn btn-default">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <!-- Invoice Details -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Invoice Information</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Invoice Number:</strong> {{ $invoice->invoice_number }}<br>
                            <strong>Issue Date:</strong> {{ $invoice->issue_date->format('M d, Y') }}<br>
                            @if($invoice->due_date)
                            <strong>Due Date:</strong> {{ $invoice->due_date->format('M d, Y') }}<br>
                            @endif
                            <strong>Status:</strong> 
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
                                $currency = \App\Models\Setting::getCurrencySymbol($invoice->shipment->currency ?? null);
                            @endphp
                            <span class="badge badge-{{ $badgeClass }}">{{ ucfirst($invoice->status) }}</span>
                        </div>
                        <div class="col-md-6">
                            <strong>Shipment:</strong> 
                            <a href="{{ route('admin.shipments.show', $invoice->shipment) }}">
                                {{ $invoice->shipment->tracking_number }}
                            </a><br>
                            <strong>Client:</strong> {{ $invoice->shipment->client->name }}<br>
                            <strong>Route:</strong> {{ $invoice->shipment->origin }} → {{ $invoice->shipment->destination }}
                        </div>
                    </div>

                    <hr>

                    <h5>Billing Details</h5>
                    <table class="table table-sm">
                        <tr>
                            <td>Subtotal:</td>
                            <td class="text-right">{{ $currency }} {{ number_format($invoice->subtotal, 2) }}</td>
                        </tr>
                        @if($invoice->tax > 0)
                        <tr>
                            <td>Tax:</td>
                            <td class="text-right">{{ $currency }} {{ number_format($invoice->tax, 2) }}</td>
                        </tr>
                        @endif
                        @if($invoice->discount > 0)
                        <tr>
                            <td>Discount:</td>
                            <td class="text-right">-{{ $currency }} {{ number_format($invoice->discount, 2) }}</td>
                        </tr>
                        @endif
                        <tr class="font-weight-bold">
                            <td><strong>Total:</strong></td>
                            <td class="text-right"><strong>{{ $currency }} {{ number_format($invoice->total, 2) }}</strong></td>
                        </tr>
                        <tr class="text-success">
                            <td>Amount Paid:</td>
                            <td class="text-right">{{ $currency }} {{ number_format($invoice->amount_paid, 2) }}</td>
                        </tr>
                        <tr class="text-danger">
                            <td><strong>Balance Due:</strong></td>
                            <td class="text-right"><strong>{{ $currency }} {{ number_format($invoice->balance, 2) }}</strong></td>
                        </tr>
                    </table>

                    @if($invoice->items->count() > 0)
                    <hr>
                    <h5>Line Items</h5>
                    <table class="table table-sm table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th>Description</th>
                                <th class="text-center">Qty</th>
                                <th class="text-right">Rate</th>
                                <th class="text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoice->items as $item)
                            <tr @if(str_contains(strtolower($item->description), 'storage')) class="table-warning" @endif>
                                <td>
                                    {{ $item->description }}
                                    @if(str_contains(strtolower($item->description), 'storage'))
                                        <span class="badge badge-warning ml-1"><i class="fas fa-warehouse"></i> Storage</span>
                                    @endif
                                </td>
                                <td class="text-center">{{ $item->quantity }}</td>
                                <td class="text-right">{{ $currency }} {{ number_format($item->rate, 2) }}</td>
                                <td class="text-right">{{ $currency }} {{ number_format($item->amount, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @endif

                    @if($invoice->notes)
                    <hr>
                    <h5>Notes</h5>
                    <p>{{ $invoice->notes }}</p>
                    @endif
                </div>
            </div>

            <!-- Payment History -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Payment History</h3>
                </div>
                <div class="card-body p-0">
                    @if($invoice->payments->count() > 0)
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Receipt #</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Reference</th>
                                <th>Recorded By</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoice->payments as $payment)
                            <tr>
                                <td><strong>{{ $payment->receipt_number }}</strong></td>
                                <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                                <td>{{ $currency }} {{ number_format($payment->amount, 2) }}</td>
                                <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                                <td>{{ $payment->reference_number ?? 'N/A' }}</td>
                                <td>{{ $payment->recorder ? $payment->recorder->name : 'System' }}</td>
                                <td>
                                    <a href="{{ route('admin.payments.receipt', $payment) }}" class="btn btn-sm btn-danger" title="Download Receipt">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                    <form action="{{ route('admin.payments.send', $payment) }}" method="POST" style="display: inline;" onsubmit="return confirm('Send receipt to client?');">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-primary" title="Send Receipt">
                                            <i class="fas fa-paper-plane"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <p class="text-center p-3 text-muted">No payments recorded yet.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Quick Actions</h3>
                </div>
                <div class="card-body">
                    <a href="{{ route('admin.invoices.pdf', $invoice) }}" class="btn btn-danger btn-block mb-2">
                        <i class="fas fa-file-pdf"></i> Download PDF
                    </a>
                    <a href="{{ route('admin.shipments.invoice', $invoice->shipment) }}" target="_blank" class="btn btn-secondary btn-block mb-2">
                        <i class="fas fa-print"></i> Print Invoice
                    </a>
                    <a href="{{ route('admin.shipments.show', $invoice->shipment) }}" class="btn btn-info btn-block mb-2">
                        <i class="fas fa-shipping-fast"></i> View Shipment
                    </a>
                    <hr class="my-2">
                    <button type="button" class="btn btn-warning btn-block" data-toggle="modal" data-target="#storageFeeModal">
                        <i class="fas fa-warehouse"></i> Add Storage Fee
                        <br><small class="text-dark opacity-75">Charge at client pickup</small>
                    </button>
                    @if($invoice->balance > 0)
                    <button type="button" class="btn btn-success btn-block mt-2" data-toggle="modal" data-target="#recordPaymentModal">
                        <i class="fas fa-money-bill-wave"></i> Record Payment
                    </button>
                    @endif
                </div>
            </div>

            <!-- Invoice Summary -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Summary</h3>
                </div>
                <div class="card-body">
                    <div class="info-box mb-3 bg-info">
                        <span class="info-box-icon"><i class="fas fa-file-invoice-dollar"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Amount</span>
                            <span class="info-box-number">{{ $currency }} {{ number_format($invoice->total, 2) }}</span>
                        </div>
                    </div>

                    <div class="info-box mb-3 bg-success">
                        <span class="info-box-icon"><i class="fas fa-check-circle"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Amount Paid</span>
                            <span class="info-box-number">{{ $currency }} {{ number_format($invoice->amount_paid, 2) }}</span>
                        </div>
                    </div>

                    <div class="info-box bg-{{ $invoice->balance > 0 ? 'danger' : 'success' }}">
                        <span class="info-box-icon"><i class="fas fa-{{ $invoice->balance > 0 ? 'exclamation-triangle' : 'check' }}"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Balance Due</span>
                            <span class="info-box-number">{{ $currency }} {{ number_format($invoice->balance, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Record Payment Modal -->
    <div class="modal fade" id="recordPaymentModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('admin.invoices.payments.store', $invoice) }}" method="POST">
                    @csrf
                    <div class="modal-header bg-success">
                        <h5 class="modal-title text-white">
                            <i class="fas fa-money-bill-wave"></i> Record Payment
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <strong>Invoice Balance:</strong> {{ $currency }} {{ number_format($invoice->balance, 2) }}
                        </div>

                        <div class="form-group">
                            <label for="amount">Amount <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control @error('amount') is-invalid @enderror" 
                                   id="amount" name="amount" max="{{ $invoice->balance }}" 
                                   value="{{ old('amount', $invoice->balance) }}" required>
                            @error('amount')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="payment_date">Payment Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('payment_date') is-invalid @enderror" 
                                   id="payment_date" name="payment_date" 
                                   value="{{ old('payment_date', date('Y-m-d')) }}" required>
                            @error('payment_date')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="payment_method">Payment Method <span class="text-danger">*</span></label>
                            <select class="form-control @error('payment_method') is-invalid @enderror" 
                                    id="payment_method" name="payment_method" required>
                                <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                                <option value="card" {{ old('payment_method') == 'card' ? 'selected' : '' }}>Card</option>
                                <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                <option value="mobile_money" {{ old('payment_method') == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                            </select>
                            @error('payment_method')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="reference_number">Reference Number</label>
                            <input type="text" class="form-control @error('reference_number') is-invalid @enderror" 
                                   id="reference_number" name="reference_number" 
                                   value="{{ old('reference_number') }}" placeholder="Transaction ID, Check Number, etc.">
                            @error('reference_number')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="notes">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="3" 
                                      placeholder="Additional notes about this payment...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Record Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- Storage Fee Modal --}}
    <div class="modal fade" id="storageFeeModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('admin.invoices.storage-fee', $invoice) }}" method="POST">
                    @csrf
                    <div class="modal-header" style="background:#856404; color:#fff;">
                        <h5 class="modal-title">
                            <i class="fas fa-warehouse"></i> Add Storage Fee &mdash; {{ $invoice->invoice_number }}
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Storage Fee</strong> is charged when the client collects/picks up their package.
                            This adds a new line item to the invoice and the outstanding balance is recalculated.
                        </div>
                        <div class="form-group">
                            <label>Storage Duration <span class="text-muted">(days — optional)</span></label>
                            <input type="number" name="storage_days" id="storage_days" class="form-control" min="1" placeholder="e.g. 7" value="1">
                        </div>
                        <div class="form-group">
                            <label>Total Storage Amount <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">{{ $currency }}</span>
                                </div>
                                <input type="number" step="0.01" name="storage_amount" id="storage_amount"
                                       class="form-control" placeholder="0.00" required min="0.01">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Notes <span class="text-muted">(optional)</span></label>
                            <input type="text" name="storage_notes" class="form-control"
                                   placeholder="e.g. Cold storage, Customs hold, Delayed pickup">
                        </div>
                        <div class="alert alert-warning" id="storageSummary" style="display:none;">
                            <i class="fas fa-calculator"></i> <strong>Summary:</strong> <span id="storageSummaryText"></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-plus"></i> Add Storage Fee to Invoice
                        </button>
                    </div>
                </form>
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
