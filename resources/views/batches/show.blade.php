@extends('adminlte::page')

@section('title', 'Batch Details')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>Batch: {{ $batch->batch_number }}</h1>
        </div>
        <div class="col-sm-6">
            @can('view batches')
            <a href="{{ route('admin.batches.packing-list', $batch) }}" class="btn btn-success float-right ml-2">
                <i class="fas fa-file-pdf"></i> Generate Packing List
            </a>
            @endcan
            @can('edit batches')
            <a href="{{ route('admin.batches.edit', $batch) }}" class="btn btn-warning float-right ml-2">
                <i class="fas fa-edit"></i> Edit
            </a>
            @endcan
            <a href="{{ route('admin.batches.index') }}" class="btn btn-secondary float-right">
                <i class="fas fa-arrow-left"></i> Back
            </a>
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

    <div class="row">
        <!-- Batch Information -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Batch Information</h3>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-5">Batch Number:</dt>
                        <dd class="col-sm-7"><strong>{{ $batch->batch_number }}</strong></dd>

                        <dt class="col-sm-5">Name:</dt>
                        <dd class="col-sm-7">{{ $batch->name }}</dd>

                        <dt class="col-sm-5">Status:</dt>
                        <dd class="col-sm-7">
                            @php
                                $statusColors = [
                                    'Pending'             => 'warning',
                                    'Picked Up'           => 'info',
                                    'In Transit'          => 'primary',
                                    'Arrived at Facility' => 'secondary',
                                    'Out for Delivery'    => 'info',
                                    'Delivered'           => 'success',
                                    'On Hold'             => 'dark',
                                    'Cancelled'           => 'danger',
                                    // lowercase fallbacks
                                    'pending'             => 'warning',
                                    'processing'          => 'info',
                                    'in_transit'          => 'primary',
                                    'delivered'           => 'success',
                                    'cancelled'           => 'danger',
                                ];
                                $badgeClass = $statusColors[$batch->current_status] ?? 'secondary';
                            @endphp
                            <span class="badge badge-{{ $badgeClass }}">{{ ucfirst(str_replace('_', ' ', $batch->current_status)) }}</span>
                        </dd>

                        <dt class="col-sm-5">Shipments:</dt>
                        <dd class="col-sm-7"><span class="badge badge-info">{{ $batch->shipments->count() }}</span></dd>

                        <dt class="col-sm-5">Created By:</dt>
                        <dd class="col-sm-7">{{ $batch->creator->name ?? 'N/A' }}</dd>

                        <dt class="col-sm-5">Created:</dt>
                        <dd class="col-sm-7">{{ $batch->created_at->format('M d, Y H:i') }}</dd>

                        <dt class="col-sm-5">Revenue:</dt>
                        <dd class="col-sm-7"><strong class="text-success">{{ \App\Models\Setting::getCurrencySymbol(null) }} {{ number_format($batch->total_revenue, 0) }}</strong></dd>

                        <dt class="col-sm-5">Total Costs:</dt>
                        <dd class="col-sm-7">
                            <strong class="text-danger">{{ \App\Models\Setting::getCurrencySymbol(null) }} {{ number_format($batch->total_costs, 0) }}</strong>
                            @if($batch->total_cost_usd > 0)
                                <br><small class="text-muted">${{ number_format($batch->total_cost_usd, 0) }}</small>
                            @endif
                        </dd>

                        <dt class="col-sm-5">Profit:</dt>
                        <dd class="col-sm-7">
                            @php $currencySymbol = \App\Models\Setting::getCurrencySymbol(null); @endphp
                            @if($batch->profit >= 0)
                                <strong class="text-success">{{ $currencySymbol }} {{ number_format($batch->profit, 0) }}</strong>
                                <span class="text-success">({{ $batch->profit_margin }}%)</span>
                            @else
                                <strong class="text-danger">-{{ $currencySymbol }} {{ number_format(abs($batch->profit), 0) }}</strong>
                                <span class="text-danger">({{ $batch->profit_margin }}%)</span>
                            @endif
                        </dd>
                    </dl>

                    @if($batch->description)
                        <hr>
                        <p><strong>Description:</strong></p>
                        <p>{{ $batch->description }}</p>
                    @endif
                </div>
            </div>

            <!-- Update Batch Status -->
            @can('edit batches')
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">Update Batch Status</h3>
                </div>
                <form action="{{ route('admin.batches.update-status', $batch) }}" method="POST" id="batch-status-form">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="current_status">New Status</label>
                            <select name="current_status" id="current_status" class="form-control" required>
                                <option value="Pending" {{ $batch->current_status == 'Pending' ? 'selected' : '' }}>Pending</option>
                                <option value="Picked Up" {{ $batch->current_status == 'Picked Up' ? 'selected' : '' }}>Picked Up</option>
                                <option value="In Transit" {{ $batch->current_status == 'In Transit' ? 'selected' : '' }}>In Transit</option>
                                <option value="At Warehouse in China" {{ $batch->current_status == 'At Warehouse in China' ? 'selected' : '' }}>At Warehouse in China</option>
                                <option value="Arrived at Facility" {{ $batch->current_status == 'Arrived at Facility' ? 'selected' : '' }}>Arrived at Facility</option>
                                <option value="Out for Delivery" {{ $batch->current_status == 'Out for Delivery' ? 'selected' : '' }}>Out for Delivery</option>
                                <option value="Delivered" {{ $batch->current_status == 'Delivered' ? 'selected' : '' }}>Delivered</option>
                                <option value="On Hold" {{ $batch->current_status == 'On Hold' ? 'selected' : '' }}>On Hold</option>
                                <option value="Cancelled" {{ $batch->current_status == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="location">Location <span class="text-danger">*</span></label>
                            <input type="text" name="location" id="location" class="form-control" placeholder="Current location (e.g., Warehouse, In Transit, etc.)" required>
                            <small class="form-text text-muted">Specify the current location for this status update</small>
                        </div>
                        <div class="form-group">
                            <label for="notes">Notes (Optional)</label>
                            <textarea name="notes" id="notes" rows="3" class="form-control" placeholder="Add notes about this status update..."></textarea>
                        </div>
                        <div class="form-group" id="storage-fee-group" style="display: none;">
                            <label for="storage_fee">Storage Fee (per shipment)</label>
                            <input type="number" name="storage_fee" id="storage_fee" class="form-control" placeholder="Enter storage fee amount" min="0" step="0.01">
                            <small class="form-text text-muted">Enter 0 if no storage fee</small>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-warning btn-block">
                            <i class="fas fa-sync"></i> Update Status for All Shipments
                        </button>
                    </div>
                </form>
            </div>
            @endcan
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Shipments in This Batch</h3>
                    <div class="card-tools">
                        @can('edit batches')
                        <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#addShipmentModal">
                            <i class="fas fa-plus"></i> Add Shipment
                        </button>
                        @endcan
                        @can('edit batches')
                        <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#addExpenseModal">
                            <i class="fas fa-plus"></i> Add Expense
                        </button>
                        @endcan
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Tracking #</th>
                                    <th>Client</th>
                                    <th>Route</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($batch->shipments as $shipment)
                                    <tr>
                                        <td><strong>{{ $shipment->tracking_number }}</strong></td>
                                        <td>{{ $shipment->client->name }}</td>
                                        <td>{{ $shipment->origin }} → {{ $shipment->destination }}</td>
                                        <td>
                                            @php
                                                $statusColors = [
                                                    'Pending' => 'warning',
                                                    'Picked Up' => 'info',
                                                    'In Transit' => 'primary',
                                                    'Arrived at Facility' => 'secondary',
                                                    'Out for Delivery' => 'info',
                                                    'Delivered' => 'success',
                                                    'On Hold' => 'dark',
                                                    'Cancelled' => 'danger',
                                                ];
                                                $badgeClass = $statusColors[$shipment->current_status] ?? 'secondary';
                                            @endphp
                                            <span class="badge badge-{{ $badgeClass }}">{{ $shipment->current_status }}</span>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.shipments.show', $shipment) }}" class="btn btn-sm btn-info" title="View Shipment">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @can('edit batches')
                                            <form action="{{ route('admin.batches.remove-shipment', [$batch, $shipment]) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Remove this shipment from the batch?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Remove from Batch">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No shipments in this batch yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Invoices & Payments -->
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-file-invoice-dollar"></i> Invoices & Payments</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Invoice #</th>
                                    <th>Client</th>
                                    <th>Total</th>
                                    <th>Paid</th>
                                    <th>Balance</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalInvoiced = 0;
                                    $totalPaid = 0;
                                    $totalOutstanding = 0;
                                @endphp
                                @forelse($batch->shipments as $shipment)
                                    @if($shipment->invoice)
                                        @php
                                            $totalInvoiced += $shipment->invoice->total;
                                            $totalPaid += $shipment->invoice->amount_paid;
                                            $totalOutstanding += $shipment->invoice->balance;
                                            $invCurrency = \App\Models\Setting::getCurrencySymbol($shipment->currency ?? null);
                                        @endphp
                                        <tr>
                                            <td><strong>{{ $shipment->invoice->invoice_number }}</strong></td>
                                            <td>{{ $shipment->client->name }}</td>
                                            <td>{{ $invCurrency }} {{ number_format($shipment->invoice->total, 0) }}</td>
                                            <td class="text-success">{{ $invCurrency }} {{ number_format($shipment->invoice->amount_paid, 0) }}</td>
                                            <td class="text-danger"><strong>{{ $invCurrency }} {{ number_format($shipment->invoice->balance, 0) }}</strong></td>
                                            <td>
                                                @if($shipment->invoice->status == 'paid')
                                                    <span class="badge badge-success">Paid</span>
                                                @elseif($shipment->invoice->status == 'partial')
                                                    <span class="badge badge-warning">Partially Paid</span>
                                                @elseif($shipment->invoice->status == 'overdue')
                                                    <span class="badge badge-danger">Overdue</span>
                                                @elseif($shipment->invoice->status == 'sent')
                                                    <span class="badge badge-info">{{ ucfirst($shipment->invoice->status) }}</span>
                                                @else
                                                    <span class="badge badge-secondary">{{ ucfirst($shipment->invoice->status) }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.invoices.show', $shipment->invoice->id) }}" class="btn btn-sm btn-info" title="View Invoice">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @can('create payments')
                                                @if($shipment->invoice->status != 'paid')
                                                    <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#paymentModal{{ $shipment->invoice->id }}" title="Record Payment">
                                                        <i class="fas fa-money-bill"></i>
                                                    </button>
                                                @endif
                                                @endcan
                                                <a href="{{ route('admin.invoices.pdf', $shipment->invoice->id) }}" class="btn btn-sm btn-danger" title="Download PDF" target="_blank">
                                                    <i class="fas fa-file-pdf"></i>
                                                </a>
                                            </td>
                                        </tr>

                                        <!-- Payment Modal for this invoice -->
                                        <div class="modal fade" id="paymentModal{{ $shipment->invoice->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-success">
                                                        <h5 class="modal-title">Record Payment - {{ $shipment->invoice->invoice_number }}</h5>
                                                        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                                                    </div>
                                                    <form action="{{ route('admin.invoices.payments.store', $shipment->invoice->id) }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="invoice_id" value="{{ $shipment->invoice->id }}">
                                                        <div class="modal-body">
                                                            <div class="alert alert-info">
                                                                <strong>Balance Due:</strong> {{ $invCurrency }} {{ number_format($shipment->invoice->balance, 0) }}
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Amount <span class="text-danger">*</span></label>
                                                                <input type="number" step="0.01" name="amount" class="form-control" value="{{ $shipment->invoice->balance > 0 ? $shipment->invoice->balance : '' }}" max="{{ $shipment->invoice->balance }}" required>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Payment Date <span class="text-danger">*</span></label>
                                                                <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Payment Method <span class="text-danger">*</span></label>
                                                                <select name="payment_method" class="form-control" required>
                                                                    <option value="cash">Cash</option>
                                                                    <option value="card">Card</option>
                                                                    <option value="bank_transfer">Bank Transfer</option>
                                                                    <option value="mobile_money">Mobile Money</option>
                                                                </select>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Reference Number</label>
                                                                <input type="text" name="reference_number" class="form-control">
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Notes</label>
                                                                <textarea name="notes" class="form-control" rows="2"></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-success">
                                                                <i class="fas fa-check"></i> Record Payment
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No invoices found for this batch</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if($totalInvoiced > 0)
                                <tfoot class="bg-light">
                                    <tr>
                                        <th colspan="2" class="text-right">TOTALS:</th>
                                        @php $totalCurrencySymbol = \App\Models\Setting::getCurrencySymbol(); @endphp
                                        <th>{{ $totalCurrencySymbol }} {{ number_format($totalInvoiced, 0) }}</th>
                                        <th class="text-success">{{ $totalCurrencySymbol }} {{ number_format($totalPaid, 0) }}</th>
                                        <th class="text-danger">{{ $totalCurrencySymbol }} {{ number_format($totalOutstanding, 0) }}</th>
                                        <th colspan="2"></th>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Shipment Modal -->
    <div class="modal fade" id="addShipmentModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Shipment to Batch</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.batches.add-shipment', $batch) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="shipment_id">Select Shipment</label>
                            <select name="shipment_id" id="shipment_id" class="form-control" required>
                                <option value="">-- Select a shipment --</option>
                                @foreach($availableShipments as $shipment)
                                    <option value="{{ $shipment->id }}">
                                        {{ $shipment->tracking_number }} - {{ $shipment->client->name }} ({{ $shipment->origin }} → {{ $shipment->destination }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @if($availableShipments->isEmpty())
                            <div class="alert alert-info">
                                No available shipments. All shipments are already in batches.
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" {{ $availableShipments->isEmpty() ? 'disabled' : '' }}>
                            <i class="fas fa-plus"></i> Add to Batch
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Batch Financial Summary -->
    @php
        $expenseTypes = [
            'shipping_china_mombasa' => 'Shipping from China – Mombasa',
            'transport_mombasa_kla' => 'Transport from Mombasa to KLA',
            'verification_fees' => 'Verification Fees',
            'demurrage' => 'Demurrage Charges',
            'other' => 'Other Expense',
        ];
    @endphp

    <!-- Container Expenses -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-receipt"></i> Container Expenses</h3>
            <div class="card-tools">
                @can('edit batches')
                <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#addExpenseModal">
                    <i class="fas fa-plus"></i> Add Expense
                </button>
                @endcan
            </div>
        </div>
        <div class="card-body p-0">
            <table class="table table-sm table-striped">
                <thead>
                    <tr>
                        <th>Expense Type</th>
                        <th>Amount</th>
                        <th>Description</th>
                        <th>Entered By</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($batch->expenses as $expense)
                        <tr>
                            <td><strong>{{ $expenseTypes[$expense->type] ?? ucfirst(str_replace('_', ' ', $expense->type)) }}</strong></td>
                            <td>@if($expense->currency === 'USD')${{ number_format($expense->amount, 0) }}@else UGX {{ number_format($expense->amount, 0) }}@endif</td>
                            <td><small>{{ $expense->description ?? '' }}</small></td>
                            <td><small>{{ $expense->creator->name ?? 'N/A' }}</small></td>
                            <td>
                                @can('delete batches')
                                <form action="{{ route('admin.batches.expenses.destroy', [$batch, $expense]) }}" method="POST" style="display:inline;" class="delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Remove Expense"><i class="fas fa-trash"></i></button>
                                </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-3">No container expenses recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Expense Modal -->
    <div class="modal fade" id="addExpenseModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.batches.expenses.store', $batch) }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Add Container Expense</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Expense Type <span class="text-danger">*</span></label>
                            <select name="type" class="form-control" required>
                                <option value="">Select Type</option>
                                @foreach($expenseTypes as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Amount <span class="text-danger">*</span></label>
                                    <input type="number" name="amount" class="form-control" step="0.01" min="0" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Currency <span class="text-danger">*</span></label>
                                    <select name="currency" class="form-control" required>
                                        <option value="UGX">UGX</option>
                                        <option value="USD">USD</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Description (Optional)</label>
                            <textarea name="description" class="form-control" rows="2" placeholder="Additional details..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Expense</button>
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

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(function() {
        // Prepare shipment data for validation
        @php
        $shipmentData = [];
        $firstInvoiceUrl = '';
        $totalBalance = 0;
        foreach ($batch->shipments as $shipment) {
            $invoice = $shipment->invoices->first() ?? $shipment->invoice;
            $hasStorageFee = false;
            if ($invoice && $invoice->items) {
                foreach ($invoice->items as $item) {
                    if (strtolower(trim($item->description)) === 'storage fee') {
                        $hasStorageFee = true;
                        break;
                    }
                }
            }
            $shipmentData[] = [
                'id' => $shipment->id,
                'tracking' => $shipment->tracking_number,
                'balance' => $invoice ? floatval($invoice->balance) : 0,
                'hasStorageFee' => $hasStorageFee,
            ];
            $totalBalance += $invoice ? floatval($invoice->balance) : 0;
            if (!$firstInvoiceUrl && $invoice) {
                $firstInvoiceUrl = route('admin.invoices.show', $invoice);
            }
        }
        @endphp
        
        var shipmentData = @json($shipmentData);
        var totalBalance = {{ $totalBalance }};
        var currency = "{{ \App\Models\Setting::getCurrencySymbol() }}";
        var invoiceUrl = "{{ $firstInvoiceUrl }}";
        var batchShipmentCount = {{ $batch->shipments->count() }};

        console.log('Total Balance:', totalBalance);
        console.log('Shipment Data:', shipmentData);

        // Show/hide storage fee field based on status selection
        $('#current_status').on('change', function() {
            if ($(this).val() === 'Picked Up') {
                $('#storage-fee-group').slideDown();
            } else {
                $('#storage-fee-group').slideUp();
                $('#storage_fee').val('');
            }
        });

        // Form submission with validation
        $('#batch-status-form').on('submit', function(e) {
            var status = $('#current_status').val();
            var storageFee = $('#storage_fee').val();
            
            if (status === 'Picked Up') {
                e.preventDefault();
                
                // Check if any shipment has balance
                var hasBalance = false;
                var balanceShipments = [];
                var noFeeShipments = [];
                
                for (var i = 0; i < shipmentData.length; i++) {
                    if (shipmentData[i].balance > 0) {
                        hasBalance = true;
                        balanceShipments.push(shipmentData[i].tracking + ' (' + currency + ' ' + shipmentData[i].balance + ')');
                    }
                    
                    if (!shipmentData[i].hasStorageFee) {
                        noFeeShipments.push(shipmentData[i].tracking);
                    }
                }
                
                // If has balance, redirect to invoice
                if (hasBalance) {
                    Swal.fire({
                        title: 'Outstanding Balance',
                        html: 'The following shipments have outstanding balances:<br><strong>' + balanceShipments.join(', ') + '</strong><br><br>Please go to the invoice and make a payment first.',
                        icon: 'error',
                        confirmButtonText: 'Go to Invoice'
                    }).then((result) => {
                        if (result.isConfirmed && invoiceUrl) {
                            window.location.href = invoiceUrl;
                        }
                    });
                    return;
                }
                
                // If no storage fee entered and some shipments don't have it
                if (storageFee === '' && noFeeShipments.length > 0) {
                    Swal.fire({
                        title: 'Storage Fee Required',
                        text: 'The following shipments need a storage fee: ' + noFeeShipments.join(', ') + '. Please enter a storage fee amount (enter 0 if no fee).',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    });
                    return;
                }
                
                // All checks passed - confirm and submit
                Swal.fire({
                    title: 'Confirm Status Update',
                    text: 'This will update the status of ALL ' + batchShipmentCount + ' shipments in this batch to "Picked Up". Continue?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Update',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#batch-status-form')[0].submit();
                    }
                });
            }
        });
    });
</script>
@stop
