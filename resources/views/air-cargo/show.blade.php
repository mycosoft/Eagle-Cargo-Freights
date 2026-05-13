@extends('adminlte::page')

@section('title', 'Air Cargo Details')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1><i class="fas fa-plane"></i> Air Cargo: {{ $shipment->tracking_number }}</h1>
        </div>
        <div class="col-sm-6">
            @can('edit air cargo')
            <a href="{{ route('admin.air-cargo.edit', $shipment) }}" class="btn btn-warning float-right ml-2">
                <i class="fas fa-edit"></i> Edit
            </a>
            @endcan
            <a href="{{ route('admin.air-cargo.index') }}" class="btn btn-secondary float-right">
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
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Shipment Details</h3>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Tracking Number:</dt>
                        <dd class="col-sm-8"><strong>{{ $shipment->tracking_number }}</strong></dd>

                        <dt class="col-sm-4">Client:</dt>
                        <dd class="col-sm-8">{{ $shipment->client->name }}</dd>

                        <dt class="col-sm-4">Origin:</dt>
                        <dd class="col-sm-8">{{ $shipment->origin }}</dd>

                        <dt class="col-sm-4">Destination:</dt>
                        <dd class="col-sm-8">{{ $shipment->destination }}</dd>

                        <dt class="col-sm-4">Delivery Time:</dt>
                        <dd class="col-sm-8"><span class="badge badge-info">{{ $shipment->delivery_range }}</span></dd>

                        <dt class="col-sm-4">Weight:</dt>
                        <dd class="col-sm-8">{{ $shipment->weight ? $shipment->weight . ' kg' : 'N/A' }}</dd>

                        <dt class="col-sm-4">Status:</dt>
                        <dd class="col-sm-8"><span class="badge badge-secondary">{{ $shipment->current_status }}</span></dd>

                        @if($shipment->batch)
                            <dt class="col-sm-4">Batch:</dt>
                            <dd class="col-sm-8">
                                <a href="{{ route('admin.batches.show', $shipment->batch) }}">
                                    <span class="badge badge-primary">{{ $shipment->batch->batch_number }}</span>
                                </a>
                            </dd>
                        @endif

                        @if($shipment->description)
                            <dt class="col-sm-4">Description:</dt>
                            <dd class="col-sm-8">{{ $shipment->description }}</dd>
                        @endif
                    </dl>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">Sender & Receiver</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6 border-right">
                            <h5>Sender</h5>
                            <p class="mb-1"><strong>Name:</strong> {{ $shipment->sender_name ?? 'N/A' }}</p>
                            <p class="mb-1"><strong>Phone:</strong> {{ $shipment->sender_phone ?? 'N/A' }}</p>
                            <p class="mb-0"><strong>Address:</strong><br>{{ $shipment->sender_address ?? 'N/A' }}</p>
                        </div>
                        <div class="col-sm-6 pl-4">
                            <h5>Receiver</h5>
                            <p class="mb-1"><strong>Name:</strong> {{ $shipment->receiver_name ?? 'N/A' }}</p>
                            <p class="mb-1"><strong>Phone:</strong> {{ $shipment->receiver_phone ?? 'N/A' }}</p>
                            <p class="mb-0"><strong>Address:</strong><br>{{ $shipment->receiver_address ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Timeline -->
            <div class="card card-info mt-3">
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

        <div class="col-md-4">
            <!-- Add Status Update Card -->
            @can('manage status updates')
            <div class="card card-success mb-3">
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
                        
                        <button type="submit" class="btn btn-success btn-block">
                            <i class="fas fa-plus"></i> Add Update
                        </button>
                    </form>
                </div>
            </div>
            @endcan

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Package Info</h3>
                </div>
                <div class="card-body">
                    <p><strong>Packages:</strong> {{ $shipment->num_packages ?? 'N/A' }}</p>
                    <p><strong>Type:</strong> {{ $shipment->package_type ? ucfirst($shipment->package_type) : 'N/A' }}</p>
                    <p><strong>Fragile:</strong> {{ $shipment->fragile ? 'Yes' : 'No' }}</p>
                </div>
            </div>

            @if($shipment->shipping_cost)
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Pricing</h3>
                </div>
                <div class="card-body">
                    @php $sym = $shipment->currency == 'USD' ? '$' : ($shipment->currency == 'EUR' ? '€' : ($shipment->currency == 'GBP' ? '£' : 'UGX')); @endphp
                    <p><strong>Shipping:</strong> {{ $sym }} {{ number_format($shipment->shipping_cost, 2) }}</p>
                    <p><strong>Tax:</strong> {{ $sym }} {{ number_format($shipment->tax ?? 0, 2) }}</p>
                    <hr>
                    <p><strong>Total:</strong> <strong>{{ $sym }} {{ number_format($shipment->total_amount ?? 0, 2) }}</strong></p>
                </div>
            </div>
            @endif
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
    let currentStatus = "{{ $currentStatus }}";
    
    console.log('Invoice Balance:', invoiceBalance);
    console.log('Current Status:', currentStatus);

    // Disable status dropdown if already Picked Up
    if (currentStatus === 'picked up') {
        $('select[name="status"]').prop('disabled', true);
        $('#confirmPickupBtn').prop('disabled', true).closest('.modal-footer').append('<span class="text-muted d-block mt-2">Status is locked after pickup</span>');
    }

    $('#status-update-form').on('submit', function(e) {
            let status = $(this).find('select[name="status"]').val();
            let storageFeeStr = $('#form_storage_fee').val();
            console.log('Status:', status, 'Balance:', invoiceBalance);
            
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
