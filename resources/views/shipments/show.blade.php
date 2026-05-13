@extends('adminlte::page')

@section('title', 'Shipment Details')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>Shipment Details: {{ $shipment->tracking_number }}</h1>
        </div>
        <div class="col-sm-6">
            <div class="float-right">
                @can('edit shipments')
                <a href="{{ route('admin.shipments.edit', $shipment) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Edit
                </a>
                @endcan
                <a href="{{ route('admin.shipments.index') }}" class="btn btn-default">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
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

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <ul class="mb-0">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <!-- Main Details Column -->
        <div class="col-md-8">
            <!-- Shipment Information Card -->
            <div class="card card-primary card-outline card-tabs">
                <div class="card-header p-0 pt-1 border-bottom-0">
                    <ul class="nav nav-tabs" id="shipment-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="details-tab" data-toggle="pill" href="#details" role="tab">Shipment Info</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="package-tab" data-toggle="pill" href="#package" role="tab">Package</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="parties-tab" data-toggle="pill" href="#parties" role="tab">Sender & Receiver</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="billing-tab" data-toggle="pill" href="#billing" role="tab">Billing</a>
                        </li>
                        @if($shipment->is_international)
                        <li class="nav-item">
                            <a class="nav-link" id="customs-tab" data-toggle="pill" href="#customs" role="tab">Customs</a>
                        </li>
                        @endif
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="shipment-tabContent">
                        
                        <!-- Basic Details Tab -->
                        <div class="tab-pane fade show active" id="details" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Client:</strong>
                                    <p><a href="{{ route('admin.clients.show', $shipment->client) }}">{{ $shipment->client->name }}</a></p>
                                    
                                    <strong>Origin:</strong>
                                    <p>{{ $shipment->origin }}</p>
                                    
                                    <strong>Destination:</strong>
                                    <p>{{ $shipment->destination }}</p>
                                    
                                    <strong>Service Type:</strong>
                                    <p>{{ ucfirst($shipment->service_type ?? 'Standard') }}</p>
                                </div>
                                <div class="col-md-6">
                                    <strong>Current Status:</strong>
                                    <p>
                                        @php
                                            $statusColors = [
                                                'Pending' => 'warning',
                                                'Picked Up' => 'info',
                                                'In Transit' => 'primary',
                                                'Arrived at Facility' => 'secondary',
                                                'Out for Delivery' => 'info',
                                                'Delivered' => 'success',
                                                'On Hold' => 'dark',
                                                'Cancelled' => 'danger'
                                            ];
                                            $badgeClass = $statusColors[$shipment->current_status] ?? 'secondary';
                                        @endphp
                                        <span class="badge badge-{{ $badgeClass }}">{{ $shipment->current_status }}</span>
                                    </p>
                                    
                                    <strong>Shipment Type:</strong>
                                    <p>{{ ucfirst($shipment->shipment_type) }}</p>
                                    
                                    <strong>Expected Delivery:</strong>
                                    <p>{{ $shipment->expected_delivery_date ? $shipment->expected_delivery_date->format('M d, Y') : 'N/A' }}</p>
                                    
                                    <strong>Reference Number:</strong>
                                    <p>{{ $shipment->reference_number ?? 'N/A' }}</p>
                                </div>
                            </div>
                            
                            @if($shipment->description)
                                <hr>
                                <strong>Description:</strong>
                                <p>{{ $shipment->description }}</p>
                            @endif
                            
                            @if($shipment->delivery_instructions)
                                <hr>
                                <strong>Delivery Instructions:</strong>
                                <p class="text-info"><i class="fas fa-info-circle"></i> {{ $shipment->delivery_instructions }}</p>
                            @endif
                        </div>

                        <!-- Package Details Tab -->
                        <div class="tab-pane fade" id="package" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Number of Packages:</strong>
                                    <p>{{ $shipment->num_packages ?? 1 }}</p>
                                    
                                    <strong>Package Type:</strong>
                                    <p>{{ ucfirst($shipment->package_type ?? 'Box') }}</p>
                                    
                                    <strong>Weight:</strong>
                                    <p>{{ $shipment->weight }} kg</p>
                                </div>
                                <div class="col-md-6">
                                    <strong>Dimensions (W x H):</strong>
                                    <p>
                                        {{ $shipment->width ?? '-' }} x 
                                        {{ $shipment->height ?? '-' }} cm
                                    </p>
                                    
                                    <strong>Fragile:</strong>
                                    <p>
                                        @if($shipment->fragile)
                                            <span class="badge badge-danger"><i class="fas fa-wine-glass-alt"></i> Yes - Handle with Care</span>
                                        @else
                                            <span class="badge badge-success">No</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            
                            @if($shipment->special_instructions)
                                <hr>
                                <strong>Special Handling Instructions:</strong>
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i> {{ $shipment->special_instructions }}
                                </div>
                            @endif
                        </div>

                        <!-- Sender & Receiver Tab -->
                        <div class="tab-pane fade" id="parties" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card card-light">
                                        <div class="card-header">
                                            <h3 class="card-title">Sender</h3>
                                        </div>
                                        <div class="card-body">
                                            <strong>Name:</strong> {{ $shipment->sender_name ?? 'N/A' }}<br>
                                            <strong>Phone:</strong> {{ $shipment->sender_phone ?? 'N/A' }}<br>
                                            <strong>Address:</strong><br>
                                            {{ $shipment->sender_address ?? 'N/A' }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card card-light">
                                        <div class="card-header">
                                            <h3 class="card-title">Receiver</h3>
                                        </div>
                                        <div class="card-body">
                                            <strong>Name:</strong> {{ $shipment->receiver_name ?? 'N/A' }}<br>
                                            <strong>Phone:</strong> {{ $shipment->receiver_phone ?? 'N/A' }}<br>
                                            <strong>Address:</strong><br>
                                            {{ $shipment->receiver_address ?? 'N/A' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Billing Tab -->
                        <div class="tab-pane fade" id="billing" role="tabpanel">
                            @if($shipment->invoices->isNotEmpty())
                                @php $invoice = $shipment->invoices->first(); @endphp
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Invoice Number:</strong>
                                        <p><a href="{{ route('admin.invoices.show', $invoice) }}"><strong>{{ $invoice->invoice_number }}</strong></a></p>
                                        
                                        <strong>Total Amount:</strong>
                                        <p class="lead font-weight-bold">{{ $shipment->currency ?? 'USD' }} {{ number_format($invoice->total, 2) }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Amount Paid:</strong>
                                        <p class="text-success">{{ $shipment->currency ?? 'USD' }} {{ number_format($invoice->amount_paid, 2) }}</p>
                                        
                                        <strong>Balance Due:</strong>
                                        <p class="text-danger font-weight-bold">{{ $shipment->currency ?? 'USD' }} {{ number_format($invoice->balance, 2) }}</p>
                                        
                                        <strong>Invoice Status:</strong>
                                        <p>
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
                                            <span class="badge badge-{{ $badgeClass }} px-2 py-1">{{ ucfirst($invoice->status) }}</span>
                                        </p>
                                    </div>
                                </div>

                                @if($invoice->items->isNotEmpty())
                                    <hr>
                                    <h5 class="mb-3">Invoice Line Items</h5>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead class="bg-dark">
                                                <tr>
                                                    <th>Description</th>
                                                    <th class="text-right">Quantity</th>
                                                    <th class="text-right">Rate</th>
                                                    <th class="text-right">Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($invoice->items as $item)
                                                <tr class="{{ $item->description == 'Storage Fee' ? 'table-warning font-weight-bold' : '' }}">
                                                    <td>
                                                        {{ $item->description }}
                                                        @if($item->description == 'Storage Fee')
                                                            <span class="badge badge-warning ml-1">Added</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-right">{{ $item->quantity }}</td>
                                                    <td class="text-right">{{ $shipment->currency ?? 'USD' }} {{ number_format($item->rate, 2) }}</td>
                                                    <td class="text-right">{{ $shipment->currency ?? 'USD' }} {{ number_format($item->amount, 2) }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr class="font-weight-bold">
                                                    <td colspan="3" class="text-right">Subtotal:</td>
                                                    <td class="text-right">{{ $shipment->currency ?? 'USD' }} {{ number_format($invoice->subtotal, 2) }}</td>
                                                </tr>
                                                @if($invoice->tax > 0)
                                                <tr>
                                                    <td colspan="3" class="text-right">Tax:</td>
                                                    <td class="text-right">{{ $shipment->currency ?? 'USD' }} {{ number_format($invoice->tax, 2) }}</td>
                                                </tr>
                                                @endif
                                                @if($invoice->discount > 0)
                                                <tr>
                                                    <td colspan="3" class="text-right">Discount:</td>
                                                    <td class="text-right">-{{ $shipment->currency ?? 'USD' }} {{ number_format($invoice->discount, 2) }}</td>
                                                </tr>
                                                @endif
                                                <tr class="font-weight-bold bg-light">
                                                    <td colspan="3" class="text-right">Total:</td>
                                                    <td class="text-right">{{ $shipment->currency ?? 'USD' }} {{ number_format($invoice->total, 2) }}</td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                @endif
                                
                                <div class="mt-3">
                                    <a href="{{ route('admin.invoices.show', $invoice) }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-file-invoice"></i> Manage Invoice
                                    </a>
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    No invoice generated for this shipment.
                                </div>
                            @endif
                        </div>

                        <!-- Customs Tab -->
                        @if($shipment->is_international)
                        <div class="tab-pane fade" id="customs" role="tabpanel">
                            <div class="alert alert-info">
                                <i class="fas fa-globe"></i> This is an international shipment subject to customs regulations.
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Customs Value:</strong>
                                    <p>${{ number_format($shipment->customs_value, 2) }}</p>
                                </div>
                            </div>
                            
                            <strong>Customs Description:</strong>
                            <p>{{ $shipment->customs_description ?? 'N/A' }}</p>
                        </div>
                        @endif

                    </div>
                </div>
            </div>

            <!-- Status Timeline -->
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">Status Timeline</h3>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @foreach($shipment->statusUpdates as $update)
                            <div>
                                <i class="fas fa-truck bg-blue"></i>
                                <div class="timeline-item">
                                    <span class="time"><i class="fas fa-clock"></i> {{ $update->created_at->format('M d, H:i') }}</span>
                                    <h3 class="timeline-header"><strong>{{ $update->status }}</strong></h3>
                                    <div class="timeline-body">
                                        {{ $update->remarks }}
                                        @if($update->location)
                                            <br>
                                            <small class="text-muted"><i class="fas fa-map-marker-alt"></i> {{ $update->location }}</small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        <div>
                            <i class="fas fa-clock bg-gray"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Column -->
        <div class="col-md-4">
            <!-- Add Status Update Card -->
            @can('manage status updates')
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">Add Status Update</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.shipment-status-updates.store') }}" method="POST" id="status-update-form">
                        @csrf
                        <input type="hidden" name="shipment_id" value="{{ $shipment->id }}">
                        <input type="hidden" name="storage_fee" id="form_storage_fee">
                        
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control" required>
                                <option value="Pending" {{ $shipment->current_status == 'Pending' ? 'selected' : '' }}>Pending</option>
                                <option value="Picked Up" {{ $shipment->current_status == 'Picked Up' ? 'selected' : '' }}>Picked Up</option>
                                <option value="In Transit" {{ $shipment->current_status == 'In Transit' ? 'selected' : '' }}>In Transit</option>
                                <option value="Arrived at Facility" {{ $shipment->current_status == 'Arrived at Facility' ? 'selected' : '' }}>Arrived at Facility</option>
                                <option value="Out for Delivery" {{ $shipment->current_status == 'Out for Delivery' ? 'selected' : '' }}>Out for Delivery</option>
                                <option value="Delivered" {{ $shipment->current_status == 'Delivered' ? 'selected' : '' }}>Delivered</option>
                                <option value="On Hold" {{ $shipment->current_status == 'On Hold' ? 'selected' : '' }}>On Hold</option>
                                <option value="Cancelled" {{ $shipment->current_status == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Location</label>
                            <input type="text" name="location" class="form-control" placeholder="Current Location">
                        </div>
                        
                        <div class="form-group">
                            <label>Remarks</label>
                            <textarea name="remarks" class="form-control" rows="3" placeholder="Optional remarks"></textarea>
                        </div>
                        
                        <button type="submit" id="statusSubmitBtn" class="btn btn-success btn-block">
                            <i class="fas fa-plus"></i> Add Update
                        </button>
                    </form>
                </div>
            </div>
            @endcan
            
            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Quick Actions</h3>
                </div>
                <div class="card-body">
                    <a href="{{ route('admin.shipments.label', $shipment) }}" target="_blank" class="btn btn-app">
                        <i class="fas fa-print"></i> Print Label
                    </a>
                    <a href="{{ route('admin.shipments.invoice', $shipment) }}" target="_blank" class="btn btn-app">
                        <i class="fas fa-file-invoice"></i> Invoice
                    </a>
                    <a href="mailto:{{ $shipment->client->email }}?subject=Shipment Update: {{ $shipment->tracking_number }}" class="btn btn-app">
                        <i class="fas fa-envelope"></i> Email Client
                    </a>
                </div>
            </div>

        </div>
    </div>

    <!-- Storage Fee Modal -->
    <div class="modal fade" id="storageFeeModal" tabindex="-1" role="dialog" aria-labelledby="storageFeeModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h5 class="modal-title" id="storageFeeModalLabel">Package Pickup & Storage Fee</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> You are updating the status to <strong>Picked Up</strong>.
                    </div>
                    <p>Please enter the Storage Fee collected from the client. If no fee applies, simply enter <strong>0</strong>.</p>
                    <div class="form-group">
                        <label>Storage Fee Amount ({{ $shipment->currency ?? 'USD' }}) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" class="form-control" id="modal_storage_fee" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="confirmPickupBtn">Confirm Pickup & Add Fee</button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(function() {
        @php
        $totalBalance = 0;
        foreach ($shipment->invoices as $inv) {
            $totalBalance += $inv->balance;
        }
        $currentStatus = strtolower($shipment->current_status ?? '');
        @endphp
        let invoiceBalance = {{ $totalBalance }};
        let currency = "{{ $shipment->currency ?? 'USD' }}";
        let invoiceUrl = "{{ $shipment->invoices->isNotEmpty() ? route('admin.invoices.show', $shipment->invoices->first()) : '#' }}";
        let currentStatus = "{{ strtolower($shipment->current_status ?? '') }}";
        
        console.log('Invoice Balance:', invoiceBalance);
        console.log('Currency:', currency);
        console.log('Invoice URL:', invoiceUrl);
        console.log('Current Status:', currentStatus);

        // Disable status dropdown if already Picked Up
        if (currentStatus === 'picked up') {
            $('select[name="status"]').prop('disabled', true);
            $('#statusSubmitBtn').prop('disabled', true).closest('.form-group').append('<small class="text-muted d-block mt-1">Status is locked after pickup</small>');
        }

        $('#status-update-form').on('submit', function(e) {
            let status = $(this).find('select[name="status"]').val();
            let storageFeeStr = $('#form_storage_fee').val();
            console.log('Status:', status, 'Balance:', invoiceBalance, 'Storage Fee:', storageFeeStr);
            
            if (status === 'Picked Up') {
                if (invoiceBalance > 0) {
                    e.preventDefault();
                    console.log('Blocking - has balance');
                    Swal.fire({
                        title: 'Cannot Pickup',
                        text: 'There is an outstanding invoice balance of ' + currency + ' ' + invoiceBalance.toFixed(2) + '. Please go to the invoice and make a payment first.',
                        icon: 'error',
                        confirmButtonText: 'Go to Invoice'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = invoiceUrl;
                        }
                    });
                    return;
                }

                if (storageFeeStr === '') {
                    e.preventDefault();
                    $('#storageFeeModal').modal('show');
                }
            }
        });

        $('#confirmPickupBtn').on('click', function() {
            let fee = $('#modal_storage_fee').val();
            if(fee === '') {
                alert('Please enter a storage fee amount. Enter 0 if none.');
                $('#modal_storage_fee').focus();
                return;
            }
            $('#form_storage_fee').val(fee);
            $('#storageFeeModal').modal('hide');
            $('#status-update-form').submit();
        });
    });
</script>
@stop



@section('footer')
    <strong>Copyright &copy; {{ date('Y') }} <a href="#">Eagle Cargo Freights</a>.</strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
        <b>Support Call</b> +256 200 991 118
    </div>
@stop

