@extends('adminlte::page')

@section('title', 'Edit Air Cargo Shipment')

@section('content_header')
    <h1><i class="fas fa-plane"></i> Edit Air Cargo Shipment</h1>
@stop

@section('content')
    <form action="{{ route('admin.air-cargo.update', $shipment) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Shipment Information</h3>
            </div>
            <div class="card-body">
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
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="current_status">Status</label>
                            <select name="current_status" id="current_status" class="form-control">
                                <option value="Pending" {{ old('current_status', $shipment->current_status) == 'Pending' ? 'selected' : '' }}>Pending</option>
                                <option value="Picked Up" {{ old('current_status', $shipment->current_status) == 'Picked Up' ? 'selected' : '' }}>Picked Up</option>
                                <option value="In Transit" {{ old('current_status', $shipment->current_status) == 'In Transit' ? 'selected' : '' }}>In Transit</option>
                                <option value="At Warehouse in China" {{ old('current_status', $shipment->current_status) == 'At Warehouse in China' ? 'selected' : '' }}>At Warehouse in China</option>
                                <option value="Delivered" {{ old('current_status', $shipment->current_status) == 'Delivered' ? 'selected' : '' }}>Delivered</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="china_warehouse_date">Date Received at China Warehouse</label>
                            <input type="date" name="china_warehouse_date" id="china_warehouse_date" class="form-control" value="{{ old('china_warehouse_date', $shipment->china_warehouse_date) }}">
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
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Delivery Time (Days) <span class="text-danger">*</span></label>
                            <div class="row">
                                <div class="col-6">
                                    <input type="number" name="delivery_time_min" class="form-control @error('delivery_time_min') is-invalid @enderror" placeholder="Min" value="{{ old('delivery_time_min', $shipment->delivery_time_min) }}" min="1" required>
                                    @error('delivery_time_min')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-6">
                                    <input type="number" name="delivery_time_max" class="form-control @error('delivery_time_max') is-invalid @enderror" placeholder="Max" value="{{ old('delivery_time_max', $shipment->delivery_time_max) }}" min="1" required>
                                    @error('delivery_time_max')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="weight">Weight (kg)</label>
                            <input type="number" step="0.01" name="weight" id="weight" class="form-control" value="{{ old('weight', $shipment->weight) }}">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" rows="3" class="form-control">{{ old('description', $shipment->description) }}</textarea>
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

                <hr>
                <h5>Sender & Receiver</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card card-light mb-3">
                            <div class="card-header"><h3 class="card-title">Sender Information</h3></div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="sender_name">Sender Name</label>
                                    <input type="text" name="sender_name" id="sender_name" class="form-control" value="{{ old('sender_name', $shipment->sender_name) }}">
                                </div>
                                <div class="form-group">
                                    <label for="sender_phone">Sender Phone</label>
                                    <input type="tel" name="sender_phone" id="sender_phone" class="form-control" value="{{ old('sender_phone', $shipment->sender_phone) }}">
                                </div>
                                <div class="form-group">
                                    <label for="sender_address">Sender Address</label>
                                    <textarea name="sender_address" id="sender_address" rows="2" class="form-control">{{ old('sender_address', $shipment->sender_address) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card card-light mb-3">
                            <div class="card-header"><h3 class="card-title">Receiver Information</h3></div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="receiver_name">Receiver Name</label>
                                    <input type="text" name="receiver_name" id="receiver_name" class="form-control" value="{{ old('receiver_name', $shipment->receiver_name) }}">
                                </div>
                                <div class="form-group">
                                    <label for="receiver_phone">Receiver Phone</label>
                                    <input type="tel" name="receiver_phone" id="receiver_phone" class="form-control" value="{{ old('receiver_phone', $shipment->receiver_phone) }}">
                                </div>
                                <div class="form-group">
                                    <label for="receiver_address">Receiver Address</label>
                                    <textarea name="receiver_address" id="receiver_address" rows="2" class="form-control">{{ old('receiver_address', $shipment->receiver_address) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>
                <h5>Package Details</h5>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="num_packages">Number of Packages</label>
                            <input type="number" name="num_packages" id="num_packages" class="form-control" value="{{ old('num_packages', $shipment->num_packages) }}" min="1">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="package_type">Package Type</label>
                            <select name="package_type" id="package_type" class="form-control">
                                <option value="">Select Type</option>
                                <option value="box" {{ old('package_type', $shipment->package_type) == 'box' ? 'selected' : '' }}>Box</option>
                                <option value="pallet" {{ old('package_type', $shipment->package_type) == 'pallet' ? 'selected' : '' }}>Pallet</option>
                                <option value="envelope" {{ old('package_type', $shipment->package_type) == 'envelope' ? 'selected' : '' }}>Envelope</option>
                                <option value="custom" {{ old('package_type', $shipment->package_type) == 'custom' ? 'selected' : '' }}>Custom</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <div class="custom-control custom-checkbox mt-4">
                                <input type="checkbox" class="custom-control-input" id="fragile" name="fragile" value="1" {{ old('fragile', $shipment->fragile) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="fragile">Fragile Item</label>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>
                <h5>Pricing</h5>
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="currency">Currency</label>
                            <select name="currency" id="currency" class="form-control">
                                <option value="USD" {{ old('currency', $shipment->currency ?? 'USD') == 'USD' ? 'selected' : '' }}>USD ($)</option>
                                <option value="EUR" {{ old('currency', $shipment->currency ?? 'USD') == 'EUR' ? 'selected' : '' }}>EUR (€)</option>
                                <option value="GBP" {{ old('currency', $shipment->currency ?? 'USD') == 'GBP' ? 'selected' : '' }}>GBP (£)</option>
                                <option value="UGX" {{ old('currency', $shipment->currency ?? 'USD') == 'UGX' ? 'selected' : '' }}>UGX (Shs)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="rate">Rate (per kg)</label>
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text currency-symbol">{{ $shipment->currency ?? 'USD' }}</span></div>
                                @php $computedRate = ($shipment->weight && $shipment->weight > 0) ? ($shipment->shipping_cost / $shipment->weight) : 0; @endphp
                                <input type="number" step="0.01" name="rate" id="rate" class="form-control" value="{{ $computedRate }}">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="shipping_cost">Shipping Cost</label>
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text currency-symbol">{{ $shipment->currency ?? 'USD' }}</span></div>
                                <input type="number" step="0.01" name="shipping_cost" id="shipping_cost" class="form-control font-weight-bold" value="{{ old('shipping_cost', $shipment->shipping_cost) }}" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="tax">Tax</label>
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text currency-symbol">{{ $shipment->currency ?? 'USD' }}</span></div>
                                <input type="number" step="0.01" name="tax" id="tax" class="form-control" value="{{ old('tax', $shipment->tax) }}">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="total_amount">Total Amount</label>
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text currency-symbol font-weight-bold">{{ $shipment->currency ?? 'USD' }}</span></div>
                                <input type="number" step="0.01" name="total_amount" id="total_amount" class="form-control font-weight-bold text-success" value="{{ old('total_amount', $shipment->total_amount) }}" readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Shipment
                </button>
                <a href="{{ route('admin.air-cargo.show', $shipment) }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </div>
    </form>
@stop

@section('js')
<script>
// Currency symbol mapping
const currencyMap = {
    'UGX': 'UGX',
    'USD': '$',
    'EUR': '€',
    'GBP': '£'
};

document.getElementById('currency').addEventListener('change', function() {
    const symbol = currencyMap[this.value] || this.value;
    document.querySelectorAll('.currency-symbol').forEach(el => el.textContent = symbol);
});
// Trigger once
document.getElementById('currency').dispatchEvent(new Event('change'));

function calculateTotal() {
    const weight = parseFloat(document.getElementById('weight').value) || 0;
    const rate = parseFloat(document.getElementById('rate').value) || 0;
    
    // Cost is weight x rate
    const cost = weight * rate;
    document.getElementById('shipping_cost').value = cost.toFixed(2);
    
    // Total is cost + tax
    const tax = parseFloat(document.getElementById('tax').value) || 0;
    const total = cost + tax;
    document.getElementById('total_amount').value = total.toFixed(2);
}

// Listen to weight, rate, and tax for changes
document.getElementById('weight').addEventListener('input', calculateTotal);
document.getElementById('rate').addEventListener('input', calculateTotal);
document.getElementById('tax').addEventListener('input', calculateTotal);

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
</script>
@stop

@section('footer')
    <strong>Copyright &copy; {{ date('Y') }} <a href="#">Eagle Cargo Freights</a>.</strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
        <b>Support Call</b> +256 200 991 118
    </div>
@stop
