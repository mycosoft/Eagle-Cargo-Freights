@extends('adminlte::page')

@section('title', 'Edit Shipment')

@section('content_header')
    <h1>Edit Shipment: {{ $shipment->tracking_number }}</h1>
@stop

@section('content')
    <form action="{{ route('admin.shipments.update', $shipment) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="card card-warning card-outline card-tabs">
            <div class="card-header p-0 pt-1 border-bottom-0">
                <ul class="nav nav-tabs" id="shipment-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="basic-tab" data-toggle="pill" href="#basic" role="tab">Basic Info</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="package-tab" data-toggle="pill" href="#package" role="tab">Package Details</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="sender-tab" data-toggle="pill" href="#sender" role="tab">Sender & Receiver</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="pricing-tab" data-toggle="pill" href="#pricing" role="tab">Pricing & Billing</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="additional-tab" data-toggle="pill" href="#additional" role="tab">Additional Details</a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="shipment-tabContent">
                    
                    <!-- Basic Information Tab -->
                    <div class="tab-pane fade show active" id="basic" role="tabpanel">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="client_id">Client <span class="text-danger">*</span></label>
                                    <select name="client_id" id="client_id" class="form-control @error('client_id') is-invalid @enderror" required>
                                        <option value="">Select Client</option>
                                        @foreach($clients as $client)
                                            <option value="{{ $client->id }}" {{ old('client_id', $shipment->client_id) == $client->id ? 'selected' : '' }}>
                                                {{ $client->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('client_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="shipment_type">Shipment Type <span class="text-danger">*</span></label>
                                    <select name="shipment_type" id="shipment_type" class="form-control @error('shipment_type') is-invalid @enderror" required>
                                        <option value="">Select Type</option>
                                        <option value="air" {{ old('shipment_type', $shipment->shipment_type) == 'air' ? 'selected' : '' }}>Air</option>
                                        <option value="sea" {{ old('shipment_type', $shipment->shipment_type) == 'sea' ? 'selected' : '' }}>Sea</option>
                                        <option value="road" {{ old('shipment_type', $shipment->shipment_type) == 'road' ? 'selected' : '' }}>Road</option>
                                    </select>
                                    @error('shipment_type')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="origin">Origin <span class="text-danger">*</span></label>
                                    <input type="text" name="origin" id="origin" class="form-control @error('origin') is-invalid @enderror" value="{{ old('origin', $shipment->origin) }}" required>
                                    @error('origin')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="destination">Destination <span class="text-danger">*</span></label>
                                    <input type="text" name="destination" id="destination" class="form-control @error('destination') is-invalid @enderror" value="{{ old('destination', $shipment->destination) }}" required>
                                    @error('destination')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="weight">Weight (kg)</label>
                                    <input type="number" step="0.01" name="weight" id="weight" class="form-control @error('weight') is-invalid @enderror" value="{{ old('weight', $shipment->weight) }}">
                                    @error('weight')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="current_status">Status</label>
                                    <select name="current_status" id="current_status" class="form-control @error('current_status') is-invalid @enderror">
                                        <option value="Pending" {{ old('current_status', $shipment->current_status) == 'Pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="Picked Up" {{ old('current_status', $shipment->current_status) == 'Picked Up' ? 'selected' : '' }}>Picked Up</option>
                                        <option value="In Transit" {{ old('current_status', $shipment->current_status) == 'In Transit' ? 'selected' : '' }}>In Transit</option>
                                        <option value="At Warehouse in China" {{ old('current_status', $shipment->current_status) == 'At Warehouse in China' ? 'selected' : '' }}>At Warehouse in China</option>
                                        <option value="Arrived at Facility" {{ old('current_status', $shipment->current_status) == 'Arrived at Facility' ? 'selected' : '' }}>Arrived at Facility</option>
                                        <option value="Out for Delivery" {{ old('current_status', $shipment->current_status) == 'Out for Delivery' ? 'selected' : '' }}>Out for Delivery</option>
                                        <option value="Delivered" {{ old('current_status', $shipment->current_status) == 'Delivered' ? 'selected' : '' }}>Delivered</option>
                                        <option value="On Hold" {{ old('current_status', $shipment->current_status) == 'On Hold' ? 'selected' : '' }}>On Hold</option>
                                        <option value="Cancelled" {{ old('current_status', $shipment->current_status) == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                    @error('current_status')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="china_warehouse_date">Date Received at China Warehouse</label>
                                    <input type="date" name="china_warehouse_date" id="china_warehouse_date" class="form-control" value="{{ old('china_warehouse_date', $shipment->china_warehouse_date) }}">
                                </div>
                            </div>
                                <div class="form-group">
                                    <label for="expected_delivery_date">Expected Delivery</label>
                                    <input type="date" name="expected_delivery_date" id="expected_delivery_date" class="form-control @error('expected_delivery_date') is-invalid @enderror" value="{{ old('expected_delivery_date', $shipment->expected_delivery_date ? $shipment->expected_delivery_date->format('Y-m-d') : '') }}">
                                    @error('expected_delivery_date')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea name="description" id="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description', $shipment->description) }}</textarea>
                            @error('description')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>


                    <!-- Package Details Tab -->
                    <div class="tab-pane fade" id="package" role="tabpanel">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="num_packages">Number of Packages</label>
                                    <input type="number" name="num_packages" id="num_packages" class="form-control @error('num_packages') is-invalid @enderror" value="{{ old('num_packages', $shipment->num_packages) }}" min="1">
                                    @error('num_packages')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="package_type">Package Type</label>
                                    <select name="package_type" id="package_type" class="form-control @error('package_type') is-invalid @enderror">
                                        <option value="">Select Type</option>
                                        <option value="box" {{ old('package_type', $shipment->package_type) == 'box' ? 'selected' : '' }}>Box</option>
                                        <option value="pallet" {{ old('package_type', $shipment->package_type) == 'pallet' ? 'selected' : '' }}>Pallet</option>
                                        <option value="envelope" {{ old('package_type', $shipment->package_type) == 'envelope' ? 'selected' : '' }}>Envelope</option>
                                        <option value="custom" {{ old('package_type', $shipment->package_type) == 'custom' ? 'selected' : '' }}>Custom</option>
                                    </select>
                                    @error('package_type')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Fragile Item?</label>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="fragile" name="fragile" value="1" {{ old('fragile', $shipment->fragile) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="fragile">Mark as fragile (requires special handling)</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="width">Width (cm)</label>
                                    <input type="number" step="0.01" name="width" id="width" class="form-control @error('width') is-invalid @enderror" value="{{ old('width', $shipment->width) }}">
                                    @error('width')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="height">Height (cm)</label>
                                    <input type="number" step="0.01" name="height" id="height" class="form-control @error('height') is-invalid @enderror" value="{{ old('height', $shipment->height) }}">
                                    @error('height')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="special_instructions">Special Handling Instructions</label>
                            <textarea name="special_instructions" id="special_instructions" rows="3" class="form-control @error('special_instructions') is-invalid @enderror" placeholder="E.g., Keep upright, Temperature sensitive, etc.">{{ old('special_instructions', $shipment->special_instructions) }}</textarea>
                            @error('special_instructions')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <hr>
                        <h5 class="mb-3">Package Items</h5>
                        <p class="text-muted">Add individual packages with descriptions (optional)</p>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered" id="packagesTable">
                                <thead class="thead-light">
                                    <tr>
                                        <th width="25%">Description</th>
                                        <th width="10%">Qty</th>
                                        <th width="12%">Length (cm)</th>
                                        <th width="12%">Width (cm)</th>
                                        <th width="12%">Height (cm)</th>
                                        <th width="12%">Weight (kg)</th>
                                        <th width="5%"></th>
                                    </tr>
                                </thead>
                                <tbody id="packagesBody">
                                    @forelse($shipment->packages as $index => $package)
                                    <tr class="package-row">
                                        <td>
                                            <input type="text" name="packages[{{ $index }}][description]" class="form-control form-control-sm" value="{{ $package->description }}" placeholder="Package description">
                                            <input type="hidden" name="packages[{{ $index }}][id]" value="{{ $package->id }}">
                                        </td>
                                        <td>
                                            <input type="number" name="packages[{{ $index }}][quantity]" class="form-control form-control-sm" value="{{ $package->quantity }}" min="1">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" name="packages[{{ $index }}][length]" class="form-control form-control-sm" value="{{ $package->length }}" placeholder="0">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" name="packages[{{ $index }}][width]" class="form-control form-control-sm" value="{{ $package->width }}" placeholder="0">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" name="packages[{{ $index }}][height]" class="form-control form-control-sm" value="{{ $package->height }}" placeholder="0">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" name="packages[{{ $index }}][weight]" class="form-control form-control-sm" value="{{ $package->weight }}" placeholder="0">
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-danger remove-package"><i class="fas fa-trash"></i></button>
                                        </td>
                                    </tr>
                                    @empty
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <button type="button" class="btn btn-sm btn-success mb-3" id="addPackage">
                            <i class="fas fa-plus"></i> Add Package
                        </button>
                    </div>

                    <!-- Sender & Receiver Tab -->
                    <div class="tab-pane fade" id="sender" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="mb-3">Sender Information</h5>
                                <div class="form-group">
                                    <label for="sender_name">Sender Name</label>
                                    <input type="text" name="sender_name" id="sender_name" class="form-control @error('sender_name') is-invalid @enderror" value="{{ old('sender_name', $shipment->sender_name) }}">
                                    @error('sender_name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="sender_phone">Sender Phone</label>
                                    <input type="tel" name="sender_phone" id="sender_phone" class="form-control @error('sender_phone') is-invalid @enderror" value="{{ old('sender_phone', $shipment->sender_phone) }}">
                                    @error('sender_phone')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="sender_address">Sender Address</label>
                                    <textarea name="sender_address" id="sender_address" rows="3" class="form-control @error('sender_address') is-invalid @enderror">{{ old('sender_address', $shipment->sender_address) }}</textarea>
                                    @error('sender_address')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h5 class="mb-3">Receiver Information</h5>
                                
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="receiver_is_client" {{ old('receiver_id', $shipment->receiver_id) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="receiver_is_client">Receiver is an existing Client</label>
                                    </div>
                                </div>

                                <div id="receiver_client_select" style="display: {{ old('receiver_id', $shipment->receiver_id) ? 'block' : 'none' }};">
                                    <div class="form-group">
                                        <label for="receiver_id">Select Client</label>
                                        <select name="receiver_id" id="receiver_id" class="form-control @error('receiver_id') is-invalid @enderror">
                                            <option value="">Select Client</option>
                                            @foreach($clients as $client)
                                                <option value="{{ $client->id }}" {{ old('receiver_id', $shipment->receiver_id) == $client->id ? 'selected' : '' }} 
                                                    data-name="{{ $client->name }}"
                                                    data-phone="{{ $client->phone }}"
                                                    data-address="{{ $client->address }}">
                                                    {{ $client->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('receiver_id')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div id="receiver_manual_input">
                                    <div class="form-group">
                                        <label for="receiver_name">Receiver Name</label>
                                        <input type="text" name="receiver_name" id="receiver_name" class="form-control @error('receiver_name') is-invalid @enderror" value="{{ old('receiver_name', $shipment->receiver_name) }}">
                                        @error('receiver_name')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="receiver_phone">Receiver Phone</label>
                                        <input type="tel" name="receiver_phone" id="receiver_phone" class="form-control @error('receiver_phone') is-invalid @enderror" value="{{ old('receiver_phone', $shipment->receiver_phone) }}">
                                        @error('receiver_phone')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="receiver_address">Receiver Address</label>
                                        <textarea name="receiver_address" id="receiver_address" rows="3" class="form-control @error('receiver_address') is-invalid @enderror">{{ old('receiver_address', $shipment->receiver_address) }}</textarea>
                                        @error('receiver_address')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pricing & Billing Tab -->
                    <div class="tab-pane fade" id="pricing" role="tabpanel">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="currency">Currency</label>
                                    <select name="currency" id="currency" class="form-control @error('currency') is-invalid @enderror">
                                        <option value="USD" {{ old('currency', $shipment->currency) == 'USD' ? 'selected' : '' }}>USD ($)</option>
                                        <option value="EUR" {{ old('currency', $shipment->currency) == 'EUR' ? 'selected' : '' }}>EUR (€)</option>
                                        <option value="GBP" {{ old('currency', $shipment->currency) == 'GBP' ? 'selected' : '' }}>GBP (£)</option>
                                        <option value="UGX" {{ old('currency', $shipment->currency) == 'UGX' ? 'selected' : '' }}>UGX (Shs)</option>
                                    </select>
                                    @error('currency')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="shipping_cost">Shipping Cost</label>
                                    <input type="number" step="0.01" name="shipping_cost" id="shipping_cost" class="form-control billing-calc @error('shipping_cost') is-invalid @enderror" value="{{ old('shipping_cost', $shipment->shipping_cost) }}">
                                    @error('shipping_cost')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="insurance_value">Insurance</label>
                                    <input type="number" step="0.01" name="insurance_value" id="insurance_value" class="form-control billing-calc @error('insurance_value') is-invalid @enderror" value="{{ old('insurance_value', $shipment->insurance_value) }}">
                                    @error('insurance_value')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="tax">Tax</label>
                                    <input type="number" step="0.01" name="tax" id="tax" class="form-control billing-calc @error('tax') is-invalid @enderror" value="{{ old('tax', $shipment->tax) }}">
                                    @error('tax')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="discount">Discount</label>
                                    <input type="number" step="0.01" name="discount" id="discount" class="form-control billing-calc @error('discount') is-invalid @enderror" value="{{ old('discount', $shipment->discount) }}">
                                    @error('discount')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="total_amount">Total Amount</label>
                                    <input type="number" step="0.01" name="total_amount" id="total_amount" class="form-control font-weight-bold @error('total_amount') is-invalid @enderror" value="{{ old('total_amount', $shipment->total_amount) }}" readonly>
                                    @error('total_amount')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="payment_method">Payment Method</label>
                                    <select name="payment_method" id="payment_method" class="form-control @error('payment_method') is-invalid @enderror">
                                        <option value="">Select Method</option>
                                        <option value="cash" {{ old('payment_method', $shipment->payment_method) == 'cash' ? 'selected' : '' }}>Cash</option>
                                        <option value="card" {{ old('payment_method', $shipment->payment_method) == 'card' ? 'selected' : '' }}>Card</option>
                                        <option value="bank_transfer" {{ old('payment_method', $shipment->payment_method) == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                        <option value="cod" {{ old('payment_method', $shipment->payment_method) == 'cod' ? 'selected' : '' }}>Cash on Delivery</option>
                                    </select>
                                    @error('payment_method')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="payment_status">Payment Status</label>
                                    <select name="payment_status" id="payment_status" class="form-control @error('payment_status') is-invalid @enderror">
                                        <option value="pending" {{ old('payment_status', $shipment->payment_status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="paid" {{ old('payment_status', $shipment->payment_status) == 'paid' ? 'selected' : '' }}>Paid</option>
                                        <option value="refunded" {{ old('payment_status', $shipment->payment_status) == 'refunded' ? 'selected' : '' }}>Refunded</option>
                                    </select>
                                    @error('payment_status')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        @if($shipment->invoices->isNotEmpty() && $shipment->invoices->first()->items->isNotEmpty())
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
                                        @foreach($shipment->invoices->first()->items as $item)
                                        <tr>
                                            <td>{{ $item->description }}</td>
                                            <td class="text-right">{{ $item->quantity }}</td>
                                            <td class="text-right">{{ $shipment->currency ?? 'USD' }} {{ number_format($item->rate, 2) }}</td>
                                            <td class="text-right">{{ $shipment->currency ?? 'USD' }} {{ number_format($item->amount, 2) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="font-weight-bold">
                                            <td colspan="3" class="text-right">Subtotal:</td>
                                            <td class="text-right">{{ $shipment->currency ?? 'USD' }} {{ number_format($shipment->invoices->first()->subtotal, 2) }}</td>
                                        </tr>
                                        @if($shipment->invoices->first()->tax > 0)
                                        <tr>
                                            <td colspan="3" class="text-right">Tax:</td>
                                            <td class="text-right">{{ $shipment->currency ?? 'USD' }} {{ number_format($shipment->invoices->first()->tax, 2) }}</td>
                                        </tr>
                                        @endif
                                        @if($shipment->invoices->first()->discount > 0)
                                        <tr>
                                            <td colspan="3" class="text-right">Discount:</td>
                                            <td class="text-right">-{{ $shipment->currency ?? 'USD' }} {{ number_format($shipment->invoices->first()->discount, 2) }}</td>
                                        </tr>
                                        @endif
                                        <tr class="font-weight-bold bg-light">
                                            <td colspan="3" class="text-right">Total:</td>
                                            <td class="text-right">{{ $shipment->currency ?? 'USD' }} {{ number_format($shipment->invoices->first()->total, 2) }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        @endif
                    </div>

                    <!-- Additional Details Tab -->
                    <div class="tab-pane fade" id="additional" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="service_type">Service Type</label>
                                    <select name="service_type" id="service_type" class="form-control @error('service_type') is-invalid @enderror">
                                        <option value="">Select Service</option>
                                        <option value="express" {{ old('service_type', $shipment->service_type) == 'express' ? 'selected' : '' }}>Express</option>
                                        <option value="standard" {{ old('service_type', $shipment->service_type) == 'standard' ? 'selected' : '' }}>Standard</option>
                                        <option value="economy" {{ old('service_type', $shipment->service_type) == 'economy' ? 'selected' : '' }}>Economy</option>
                                    </select>
                                    @error('service_type')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="reference_number">Reference Number</label>
                                    <input type="text" name="reference_number" id="reference_number" class="form-control @error('reference_number') is-invalid @enderror" value="{{ old('reference_number', $shipment->reference_number) }}" placeholder="Optional customer reference">
                                    @error('reference_number')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="delivery_instructions">Delivery Instructions</label>
                            <textarea name="delivery_instructions" id="delivery_instructions" rows="3" class="form-control @error('delivery_instructions') is-invalid @enderror" placeholder="E.g., Call before delivery, Leave at reception, etc.">{{ old('delivery_instructions', $shipment->delivery_instructions) }}</textarea>
                            @error('delivery_instructions')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="special_notes">Special Notes</label>
                            <textarea name="special_notes" id="special_notes" rows="3" class="form-control @error('special_notes') is-invalid @enderror">{{ old('special_notes', $shipment->special_notes) }}</textarea>
                            @error('special_notes')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <hr>
                        <h5 class="mb-3">Customs Information (International Shipments)</h5>
                        
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="is_international" name="is_international" value="1" {{ old('is_international', $shipment->is_international) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_international">This is an international shipment</label>
                            </div>
                        </div>

                        <div id="customs-fields" style="display: {{ old('is_international', $shipment->is_international) ? 'block' : 'none' }};">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="customs_value">Customs Value</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">$</span>
                                            </div>
                                            <input type="number" step="0.01" name="customs_value" id="customs_value" class="form-control @error('customs_value') is-invalid @enderror" value="{{ old('customs_value', $shipment->customs_value) }}">
                                            @error('customs_value')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="customs_description">Customs Description</label>
                                <textarea name="customs_description" id="customs_description" rows="3" class="form-control @error('customs_description') is-invalid @enderror" placeholder="Detailed description of goods for customs">{{ old('customs_description', $shipment->customs_description) }}</textarea>
                                @error('customs_description')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-warning">
                    <i class="fas fa-save"></i> Update Shipment
                </button>
                <a href="{{ route('admin.shipments.index') }}" class="btn btn-default">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </div>
    </form>
@stop

@section('js')
<script>
// Show/hide customs fields based on international checkbox
document.getElementById('is_international').addEventListener('change', function() {
    document.getElementById('customs-fields').style.display = this.checked ? 'block' : 'none';
});

// Receiver Selection Logic
const receiverIsClient = document.getElementById('receiver_is_client');
const receiverClientSelect = document.getElementById('receiver_client_select');
const receiverManualInput = document.getElementById('receiver_manual_input');
const receiverIdSelect = document.getElementById('receiver_id');

function toggleReceiverFields() {
    if (receiverIsClient.checked) {
        receiverClientSelect.style.display = 'block';
        receiverManualInput.style.display = 'none';
    } else {
        receiverClientSelect.style.display = 'none';
        receiverManualInput.style.display = 'block';
        receiverIdSelect.value = ''; // Reset selection
    }
}

receiverIsClient.addEventListener('change', toggleReceiverFields);
toggleReceiverFields(); // Initial check

receiverIdSelect.addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    if (selectedOption.value) {
        // Optional: Auto-fill manual fields if we want to store them even when client is selected
        // Or just leave them blank as the backend will handle the relationship
        document.getElementById('receiver_name').value = selectedOption.dataset.name;
        document.getElementById('receiver_phone').value = selectedOption.dataset.phone;
        document.getElementById('receiver_address').value = selectedOption.dataset.address;
    }
});

// Billing Calculation
const billingInputs = document.querySelectorAll('.billing-calc');
const totalAmountInput = document.getElementById('total_amount');

function calculateTotal() {
    let shippingCost = parseFloat(document.getElementById('shipping_cost').value) || 0;
    let insurance = parseFloat(document.getElementById('insurance_value').value) || 0;
    let tax = parseFloat(document.getElementById('tax').value) || 0;
    let discount = parseFloat(document.getElementById('discount').value) || 0;

    let total = shippingCost + insurance + tax - discount;
    totalAmountInput.value = total.toFixed(2);
}

billingInputs.forEach(input => {
    input.addEventListener('input', calculateTotal);
});
</script>
// ========== PACKAGES MANAGEMENT ==========
let packageIndex = {{ $shipment->packages->count() }};

document.getElementById('addPackage').addEventListener('click', function() {
    const tbody = document.getElementById('packagesBody');
    const newRow = `
        <tr class="package-row">
            <td>
                <input type="text" name="packages[${packageIndex}][description]" class="form-control form-control-sm" placeholder="Package description">
            </td>
            <td>
                <input type="number" name="packages[${packageIndex}][quantity]" class="form-control form-control-sm" value="1" min="1">
            </td>
            <td>
                <input type="number" step="0.01" name="packages[${packageIndex}][length]" class="form-control form-control-sm" placeholder="0">
            </td>
            <td>
                <input type="number" step="0.01" name="packages[${packageIndex}][width]" class="form-control form-control-sm" placeholder="0">
            </td>
            <td>
                <input type="number" step="0.01" name="packages[${packageIndex}][height]" class="form-control form-control-sm" placeholder="0">
            </td>
            <td>
                <input type="number" step="0.01" name="packages[${packageIndex}][weight]" class="form-control form-control-sm" placeholder="0">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-danger remove-package"><i class="fas fa-trash"></i></button>
            </td>
        </tr>
    `;
    tbody.insertAdjacentHTML('beforeend', newRow);
    packageIndex++;
});

// Remove package
document.addEventListener('click', function(e) {
    if (e.target.closest('.remove-package')) {
        const row = e.target.closest('tr');
        row.remove();
    }
});
@stop



@section('footer')
    <strong>Copyright &copy; {{ date('Y') }} <a href="#">Eagle Cargo Freights</a>.</strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
        <b>Support Call</b> +256 200 991 118
    </div>
@stop

