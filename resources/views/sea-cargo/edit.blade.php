@extends('adminlte::page')

@section('title', 'Edit Sea Cargo Shipment')

@section('content_header')
    <h1><i class="fas fa-ship"></i> Edit Sea Cargo Shipment</h1>
@stop

@section('content')
    <form action="{{ route('admin.sea-cargo.update', $shipment) }}" method="POST">
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
                            <label>Delivery Time (Months) <span class="text-danger">*</span></label>
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
                                <th width="22%">Description</th>
                                <th width="8%">Qty</th>
                                <th width="10%">Length (cm)</th>
                                <th width="10%">Width (cm)</th>
                                <th width="10%">Height (cm)</th>
                                <th width="10%">CBM</th>
                                <th width="10%">Weight (kg)</th>
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
                                    <input type="number" name="packages[{{ $index }}][quantity]" class="form-control form-control-sm package-qty" value="{{ $package->quantity }}" min="1">
                                </td>
                                <td>
                                    <input type="number" step="0.01" name="packages[{{ $index }}][length]" class="form-control form-control-sm package-length" value="{{ $package->length }}" placeholder="0">
                                </td>
                                <td>
                                    <input type="number" step="0.01" name="packages[{{ $index }}][width]" class="form-control form-control-sm package-width" value="{{ $package->width }}" placeholder="0">
                                </td>
                                <td>
                                    <input type="number" step="0.01" name="packages[{{ $index }}][height]" class="form-control form-control-sm package-height" value="{{ $package->height }}" placeholder="0">
                                </td>
                                <td>
                                    <input type="number" step="0.0001" class="form-control form-control-sm package-cbm" value="{{ number_format(($package->length * $package->width * $package->height / 1000000) * $package->quantity, 4) }}" placeholder="0" readonly>
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
                        <tfoot>
                            <tr class="table-secondary">
                                <td colspan="5" class="text-right"><strong>Total CBM:</strong></td>
                                <td><strong id="totalCbm">{{ $shipment->packages->sum(function($p) { return ($p->length * $p->width * $p->height / 1000000) * $p->quantity; }) }}</strong></td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
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
                <h5>Pricing</h5>
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="cbm">Total CBM (m³)</label>
                            <input type="number" step="0.001" name="cbm" id="cbm" class="form-control font-weight-bold text-success" value="{{ old('cbm', $shipment->cbm) }}" placeholder="0.000" readonly>
                            <small class="form-text text-muted">Auto-calculated from packages</small>
                        </div>
                    </div>
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
                            <label for="rate">Rate (per m³)</label>
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text currency-symbol">{{ $shipment->currency ?? 'USD' }}</span></div>
                                @php $computedRate = ($shipment->cbm && $shipment->cbm > 0) ? ($shipment->shipping_cost / $shipment->cbm) : 0; @endphp
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
                <a href="{{ route('admin.sea-cargo.show', $shipment) }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </div>
    </form>
@stop

@section('js')
<script>
// Currency symbol mapping
const currencyMap = { 'UGX': 'UGX', 'USD': '$', 'EUR': '€', 'GBP': '£' };

document.getElementById('currency').addEventListener('change', function() {
    const symbol = currencyMap[this.value] || this.value;
    document.querySelectorAll('.currency-symbol').forEach(el => el.textContent = symbol);
});
document.getElementById('currency').dispatchEvent(new Event('change'));

function calculateTotals() {
    const l = parseFloat(document.getElementById('length').value) || 0;
    const w = parseFloat(document.getElementById('width').value) || 0;
    const h = parseFloat(document.getElementById('height').value) || 0;
    const weight = parseFloat(document.getElementById('weight').value) || 0;
    
    // 1. Calculate Actual Volumetric CBM
    const volCbm = (l * w * h) / 1000000;
    if(l || w || h) {
        document.getElementById('cbm').value = (volCbm > 0 ? volCbm.toFixed(3) : '');
    }
    
    // 2. Calculate Chargeable / Billable CBM
    // Manual override check (if cbm box edited by user manually, use it instead)
    let currentCbm = parseFloat(document.getElementById('cbm').value) || 0;
    const weightCbm = weight / 1000;
    
    // Use actual calculated CBM (no minimum of 1.0)
    let billableCbm = Math.max(currentCbm, weightCbm);
    
    if (billableCbm === 0) {
        billableCbm = 0;
    }
    
    // 3. Calculate Cost & Total
    const rate = parseFloat(document.getElementById('rate').value) || 0;
    
    const cost = billableCbm * rate;
    document.getElementById('shipping_cost').value = cost.toFixed(2);
    
    const tax = parseFloat(document.getElementById('tax').value) || 0;
    const total = cost + tax;
    document.getElementById('total_amount').value = total.toFixed(2);
}

// Ensure manual overriding of CBM updates totals if user touches cbm input directly
document.getElementById('cbm').addEventListener('input', calculateTotals);

document.getElementById('length').addEventListener('input', calculateTotals);
document.getElementById('width').addEventListener('input', calculateTotals);
document.getElementById('height').addEventListener('input', calculateTotals);
document.getElementById('weight').addEventListener('input', calculateTotals);
document.getElementById('rate').addEventListener('input', calculateTotals);
document.getElementById('tax').addEventListener('input', calculateTotals);

// ========== PACKAGES MANAGEMENT ==========
let packageIndex = {{ $shipment->packages->count() }};

function calculatePackageCbm(row) {
    const length = parseFloat(row.querySelector('.package-length')?.value) || 0;
    const width = parseFloat(row.querySelector('.package-width')?.value) || 0;
    const height = parseFloat(row.querySelector('.package-height')?.value) || 0;
    const quantity = parseFloat(row.querySelector('.package-qty')?.value) || 1;
    
    const cbm = (length * width * height / 1000000) * quantity;
    const cbmInput = row.querySelector('.package-cbm');
    if (cbmInput) {
        cbmInput.value = cbm.toFixed(4);
    }
    return cbm;
}

function updateTotalCbm() {
    let totalCbm = 0;
    document.querySelectorAll('.package-row').forEach(row => {
        totalCbm += calculatePackageCbm(row);
    });
    document.getElementById('totalCbm').textContent = totalCbm.toFixed(4);
    document.getElementById('cbm').value = totalCbm.toFixed(4);
    calculateTotals();
}

// Add event listeners to existing packages
document.querySelectorAll('.package-row').forEach(row => {
    row.querySelectorAll('.package-length, .package-width, .package-height, .package-qty').forEach(input => {
        input.addEventListener('input', () => {
            calculatePackageCbm(row);
            updateTotalCbm();
        });
    });
});

document.getElementById('addPackage').addEventListener('click', function() {
    const tbody = document.getElementById('packagesBody');
    const newRow = `
        <tr class="package-row">
            <td>
                <input type="text" name="packages[${packageIndex}][description]" class="form-control form-control-sm" placeholder="Package description">
            </td>
            <td>
                <input type="number" name="packages[${packageIndex}][quantity]" class="form-control form-control-sm package-qty" value="1" min="1">
            </td>
            <td>
                <input type="number" step="0.01" name="packages[${packageIndex}][length]" class="form-control form-control-sm package-length" placeholder="0">
            </td>
            <td>
                <input type="number" step="0.01" name="packages[${packageIndex}][width]" class="form-control form-control-sm package-width" placeholder="0">
            </td>
            <td>
                <input type="number" step="0.01" name="packages[${packageIndex}][height]" class="form-control form-control-sm package-height" placeholder="0">
            </td>
            <td>
                <input type="number" step="0.0001" class="form-control form-control-sm package-cbm" placeholder="0" readonly>
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
    
    const row = tbody.lastElementChild;
    row.querySelectorAll('.package-length, .package-width, .package-height, .package-qty').forEach(input => {
        input.addEventListener('input', () => {
            calculatePackageCbm(row);
            updateTotalCbm();
        });
    });
    
    packageIndex++;
});

// Remove package
document.addEventListener('click', function(e) {
    if (e.target.closest('.remove-package')) {
        const row = e.target.closest('tr');
        row.remove();
        updateTotalCbm();
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
