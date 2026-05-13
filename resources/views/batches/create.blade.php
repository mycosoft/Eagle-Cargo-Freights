@extends('adminlte::page')

@section('title', 'Create Batch')

@section('content_header')
    <h1>Create New Batch with Multiple Shipments</h1>
@stop

@section('content')
    <form action="{{ route('admin.batches.store') }}" method="POST" id="batchForm">
        @csrf
        
        <!-- Batch Information Card -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Batch Information</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="cargo_type">Cargo Type <span class="text-danger">*</span></label>
                            <select name="cargo_type" id="cargo_type" class="form-control @error('cargo_type') is-invalid @enderror" required>
                                <option value="air" {{ old('cargo_type') == 'air' ? 'selected' : '' }}>Air Cargo</option>
                                <option value="sea" {{ old('cargo_type') == 'sea' ? 'selected' : '' }}>Sea Cargo</option>
                            </select>
                            @error('cargo_type')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Batch Name (Optional)</label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Leave empty to auto-generate">
                            <small class="form-text text-muted">If left empty, will be auto-generated based on cargo type and date</small>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="current_status">Initial Status <span class="text-danger">*</span></label>
                            <select name="current_status" id="current_status" class="form-control @error('current_status') is-invalid @enderror" required>
                                <option value="Pending" {{ old('current_status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                                <option value="Picked Up" {{ old('current_status') == 'Picked Up' ? 'selected' : '' }}>Picked Up</option>
                                <option value="In Transit" {{ old('current_status') == 'In Transit' ? 'selected' : '' }}>In Transit</option>
                                <option value="At Warehouse in China" {{ old('current_status') == 'At Warehouse in China' ? 'selected' : '' }}>At Warehouse in China</option>
                                <option value="Arrived at Facility" {{ old('current_status') == 'Arrived at Facility' ? 'selected' : '' }}>Arrived at Facility</option>
                                <option value="Out for Delivery" {{ old('current_status') == 'Out for Delivery' ? 'selected' : '' }}>Out for Delivery</option>
                                <option value="Delivered" {{ old('current_status') == 'Delivered' ? 'selected' : '' }}>Delivered</option>
                                <option value="On Hold" {{ old('current_status') == 'On Hold' ? 'selected' : '' }}>On Hold</option>
                                <option value="Cancelled" {{ old('current_status') == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                            @error('current_status')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea name="description" id="description" rows="1" class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                            @error('description')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Shipments in Batch -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Shipments in this Batch</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-success" id="addShipmentBtn">
                        <i class="fas fa-plus"></i> Add Client Shipment
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div id="shipmentsContainer">
                    <!-- Shipments will be added here dynamically -->
                </div>
                
                <div id="emptyMessage" class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Click "Add Client Shipment" to start adding shipments to this batch.
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save"></i> Create Batch with Shipments
                </button>
                <a href="{{ route('admin.batches.index') }}" class="btn btn-secondary btn-lg">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </div>
    </form>
@stop

@section('js')
<script>
let shipmentIndex = 0;
const clients = @json($clients);

// Client search functionality
let searchTimeouts = {};

document.addEventListener('input', function(e) {
    if (e.target.classList.contains('client-search-input')) {
        const input = e.target;
        const index = input.dataset.index;
        const results = document.querySelector(`.client-results[data-index="${index}"]`);
        const clientIdInput = document.querySelector(`input.client-id-input[data-index="${index}"]`);
        
        if (!results || !clientIdInput) return;
        
        const query = input.value;
        clearTimeout(searchTimeouts[index]);
        
        if (query.length < 1) {
            results.style.display = 'none';
            return;
        }
        
        searchTimeouts[index] = setTimeout(() => {
            fetch('{{ route("admin.clients.search") }}?q=' + encodeURIComponent(query))
                .then(response => response.json())
                .then(clients => {
                    if (clients.length > 0) {
                        results.innerHTML = clients.map(client => 
                            `<a href="#" class="list-group-item list-group-item-action" data-id="${client.id}" data-name="${client.name}">
                                <strong>${client.name}</strong>
                                <small class="text-muted">${client.email || ''} ${client.company ? '- ' + client.company : ''}</small>
                            </a>`
                        ).join('');
                        results.style.display = 'block';
                    } else {
                        results.innerHTML = '<div class="list-group-item">No clients found</div>';
                        results.style.display = 'block';
                    }
                });
        }, 100);
    }
});

document.addEventListener('click', function(e) {
    const item = e.target.closest('a.list-group-item-action');
    if (item) {
        e.preventDefault();
        const results = item.closest('.client-results');
        const input = document.querySelector(`.client-search-input[data-index="${results.dataset.index}"]`);
        const clientIdInput = document.querySelector(`.client-id-input[data-index="${results.dataset.index}"]`);
        
        clientIdInput.value = item.dataset.id;
        input.value = item.dataset.name;
        results.style.display = 'none';
    }
    
    // Close dropdowns when clicking outside
    if (!e.target.classList.contains('client-search-input') && !e.target.closest('.client-results')) {
        document.querySelectorAll('.client-results').forEach(r => r.style.display = 'none');
    }
});

// Shipment template
function getShipmentTemplate(index) {
    const cargoType = document.getElementById('cargo_type').value;
    
    return `
        <div class="card card-outline card-primary shipment-card" data-index="${index}">
            <div class="card-header">
                <h3 class="card-title">Shipment #${index + 1}</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool btn-sm text-danger remove-shipment" data-index="${index}">
                        <i class="fas fa-times"></i> Remove
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Client Selection -->
                    <div class="col-md-4">
                        <div class="form-group" style="position: relative;">
                            <label>Client <span class="text-danger">*</span></label>
                            <input type="text" class="form-control client-search-input" data-index="${index}" placeholder="Search client..." autocomplete="off">
                            <input type="hidden" name="shipments[${index}][client_id]" class="client-id-input" data-index="${index}" required>
                            <div class="client-results list-group shadow" data-index="${index}" style="position: absolute; top: 100%; left: 0; right: 0; z-index: 9999; max-height: 250px; overflow-y: auto; display: none;"></div>
                        </div>
                    </div>
                    
                    <!-- Shipment Type -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Shipment Type <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" value="${cargoType.charAt(0).toUpperCase() + cargoType.slice(1)}" readonly>
                            <input type="hidden" name="shipments[${index}][shipment_type]" value="${cargoType}">
                        </div>
                    </div>
                    
                    <!-- Weight -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Weight (kg)</label>
                            <input type="number" step="0.01" name="shipments[${index}][weight]" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Origin -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Origin <span class="text-danger">*</span></label>
                            <input type="text" name="shipments[${index}][origin]" class="form-control" required>
                        </div>
                    </div>
                    
                    <!-- Destination -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Destination <span class="text-danger">*</span></label>
                            <input type="text" name="shipments[${index}][destination]" class="form-control" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Delivery Time Range -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Delivery Time (${cargoType === 'sea' ? 'Months' : 'Days'}) <span class="text-danger">*</span></label>
                            <div class="row">
                                <div class="col-6">
                                    <input type="number" name="shipments[${index}][delivery_time_min]" class="form-control" placeholder="Min" min="1" required>
                                </div>
                                <div class="col-6">
                                    <input type="number" name="shipments[${index}][delivery_time_max]" class="form-control" placeholder="Max" min="1" required>
                                </div>
                            </div>
                            <small class="form-text text-muted">${cargoType === 'sea' ? 'Example: 1-2 months' : 'Example: 5-7 days'}</small>
                        </div>
                    </div>
                </div>

                ${cargoType === 'sea' ? `
                <h6 class="text-primary mt-3"><i class="fas fa-box"></i> Package Items</h6>
                <p class="text-muted small">Add individual packages with dimensions (for sea cargo CBM calculation)</p>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm shipment-packages-table" id="shipment-packages-${index}">
                        <thead class="thead-light">
                            <tr>
                                <th width="25%">Description</th>
                                <th width="10%">Qty</th>
                                <th width="10%">Length (cm)</th>
                                <th width="10%">Width (cm)</th>
                                <th width="10%">Height (cm)</th>
                                <th width="10%">CBM</th>
                                <th width="10%">Weight (kg)</th>
                                <th width="5%"></th>
                            </tr>
                        </thead>
                        <tbody class="shipment-packages-body-${index}">
                        </tbody>
                        <tfoot>
                            <tr class="table-secondary">
                                <td colspan="5" class="text-right"><strong>Total CBM:</strong></td>
                                <td><strong class="shipment-total-cbm-${index}">0.0000</strong></td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <button type="button" class="btn btn-sm btn-success mb-3 add-batch-package" data-shipment-index="${index}">
                    <i class="fas fa-plus"></i> Add Package
                </button>
                ` : ''}

                <!-- Collapsible Additional Details -->
                <div class="mt-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-toggle="collapse" data-target="#details-${index}">
                        <i class="fas fa-chevron-down"></i> More Details (Package, Pricing, etc.)
                    </button>
                </div>

                <div id="details-${index}" class="collapse mt-3">
                    <!-- Package Details -->
                    <h6 class="text-primary"><i class="fas fa-box"></i> Package Details</h6>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Number of Packages</label>
                                <input type="number" name="shipments[${index}][num_packages]" class="form-control" min="1">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Package Type</label>
                                <select name="shipments[${index}][package_type]" class="form-control">
                                    <option value="">Select</option>
                                    <option value="box">Box</option>
                                    <option value="pallet">Pallet</option>
                                    <option value="envelope">Envelope</option>
                                    <option value="custom">Custom</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <div class="custom-control custom-checkbox mt-4">
                                    <input type="checkbox" class="custom-control-input" id="fragile-${index}" name="shipments[${index}][fragile]" value="1">
                                    <label class="custom-control-label" for="fragile-${index}">Fragile</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pricing & Billing -->
                    <h6 class="text-primary mt-3"><i class="fas fa-dollar-sign"></i> Pricing & Billing</h6>
                    
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Currency</label>
                                <select name="shipments[${index}][currency]" class="form-control shipment-currency">
                                    <option value="USD" selected>USD ($)</option>
                                    <option value="EUR">EUR (€)</option>
                                    <option value="GBP">GBP (£)</option>
                                    <option value="UGX">UGX (Shs)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <h6 class="mb-2">Invoice Line Items</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm shipment-items-table" data-shipment-index="${index}">
                            <thead class="thead-light">
                                <tr>
                                    <th width="40%">Description</th>
                                    <th width="15%">Quantity</th>
                                    <th width="20%">Rate</th>
                                    <th width="20%">Amount</th>
                                    <th width="5%"></th>
                                </tr>
                            </thead>
                            <tbody class="shipment-items-body">
                            </tbody>
                        </table>
                    </div>

                    <button type="button" class="btn btn-sm btn-success mb-3 add-batch-line-item" data-shipment-index="${index}">
                        <i class="fas fa-plus"></i> Add Line Item
                    </button>

                    <div class="row">
                        <div class="col-md-6 offset-md-6">
                            <table class="table table-sm">
                                <tr>
                                    <td class="text-right"><strong>Subtotal:</strong></td>
                                    <td width="150">
                                        <input type="number" step="0.01" class="form-control-plaintext form-control-sm text-right font-weight-bold shipment-subtotal-display" value="0.00" readonly>
                                        <input type="hidden" name="shipments[${index}][shipping_cost]" class="shipment-shipping-cost" value="0">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-right"><strong>Tax:</strong></td>
                                    <td><input type="number" step="0.01" name="shipments[${index}][tax]" class="form-control form-control-sm shipment-tax" value="0"></td>
                                </tr>
                                <tr class="table-active">
                                    <td class="text-right"><strong>Total:</strong></td>
                                    <td><input type="number" step="0.01" name="shipments[${index}][total_amount]" class="form-control form-control-sm font-weight-bold shipment-total" value="0" readonly></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Payment Method</label>
                                <select name="shipments[${index}][payment_method]" class="form-control form-control-sm">
                                    <option value="">Select Method</option>
                                    <option value="cash">Cash</option>
                                    <option value="card">Card</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                    <option value="mobile_money">Mobile Money</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Payment Status</label>
                                <select name="shipments[${index}][payment_status]" class="form-control form-control-sm">
                                    <option value="pending">Pending</option>
                                    <option value="paid">Paid</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="form-group">
                        <label>Description / Special Instructions</label>
                        <textarea name="shipments[${index}][description]" rows="2" class="form-control"></textarea>
                    </div>
                </div>
            </div>
        </div>
    `;
}

// Add shipment
document.getElementById('addShipmentBtn').addEventListener('click', function() {
    const container = document.getElementById('shipmentsContainer');
    container.insertAdjacentHTML('beforeend', getShipmentTemplate(shipmentIndex));
    shipmentIndex++;
    
    // Hide empty message
    document.getElementById('emptyMessage').style.display = 'none';
    
    updateShipmentNumbers();
});

// Remove shipment
document.addEventListener('click', function(e) {
    if (e.target.closest('.remove-shipment')) {
        const card = e.target.closest('.shipment-card');
        card.remove();
        updateShipmentNumbers();
        
        // Show empty message if no shipments
        if (document.querySelectorAll('.shipment-card').length === 0) {
            document.getElementById('emptyMessage').style.display = 'block';
        }
    }
});

// Update shipment numbers after removal
function updateShipmentNumbers() {
    document.querySelectorAll('.shipment-card').forEach((card, index) => {
        card.querySelector('.card-title').textContent = `Shipment #${index + 1}`;
    });
}

// Form validation
document.getElementById('batchForm').addEventListener('submit', function(e) {
    const shipmentCount = document.querySelectorAll('.shipment-card').length;
    
    if (shipmentCount === 0) {
        e.preventDefault();
        alert('Please add at least one shipment to the batch.');
        return false;
    }
});

// Add first shipment automatically
document.getElementById('addShipmentBtn').click();

// Update all existing shipment types when batch cargo type changes
document.getElementById('cargo_type').addEventListener('change', function() {
    const container = document.getElementById('shipmentsContainer');
    const existingShipments = document.querySelectorAll('.shipment-card');
    
    // If there are existing shipments, regenerate them with the new cargo type
    if (existingShipments.length > 0) {
        // Store the current shipment count
        const count = existingShipments.length;
        
        // Clear all shipments
        container.innerHTML = '';
        
        // Reset the index
        shipmentIndex = 0;
        
        // Recreate the same number of shipments with the new cargo type
        for (let i = 0; i < count; i++) {
            container.insertAdjacentHTML('beforeend', getShipmentTemplate(shipmentIndex));
            shipmentIndex++;
        }
        
        // Hide empty message since we have shipments
        document.getElementById('emptyMessage').style.display = 'none';
        
        updateShipmentNumbers();
    }
});

// ========== BATCH LINE ITEMS MANAGEMENT ==========
let batchItemIndexes = {};

// Add line item to batch shipment
document.addEventListener('click', function(e) {
    if (e.target.closest('.add-batch-line-item')) {
        const btn = e.target.closest('.add-batch-line-item');
        const shipmentIndex = btn.dataset.shipmentIndex;
        const table = document.querySelector(`.shipment-items-table[data-shipment-index="${shipmentIndex}"]`);
        const tbody = table.querySelector('.shipment-items-body');
        
        if (!batchItemIndexes[shipmentIndex]) {
            batchItemIndexes[shipmentIndex] = 0;
        } else {
            batchItemIndexes[shipmentIndex]++;
        }
        
        const itemIndex = batchItemIndexes[shipmentIndex];
        const totalCbmElement = document.querySelector(`.shipment-total-cbm-${shipmentIndex}`);
        const totalCbm = totalCbmElement ? parseFloat(totalCbmElement.textContent) || 0 : 0;
        const newRow = `
            <tr class="line-item-row">
                <td>
                    <select name="shipments[${shipmentIndex}][items][${itemIndex}][description]" class="form-control form-control-sm" required>
                        <option value="">Select Item</option>
                        <option value="Freight Charges">Freight Charges</option>
                        <option value="House Bill">House Bill</option>
                        <option value="COC Charges (PIVOC)">COC Charges (PIVOC)</option>
                        <option value="Freight MBS to KLA">Freight MBS to KLA</option>
                        <option value="Storage Bill">Storage Bill</option>
                        <option value="Handling Fee">Handling Fee</option>
                        <option value="Customs Fee">Customs Fee</option>
                        <option value="Other">Other</option>
                    </select>
                </td>
                <td><input type="number" step="any" name="shipments[${shipmentIndex}][items][${itemIndex}][quantity]" class="form-control form-control-sm item-quantity" value="${totalCbm > 0 ? totalCbm.toFixed(4) : 1}" min="0" required></td>
                <td><input type="number" step="0.01" name="shipments[${shipmentIndex}][items][${itemIndex}][rate]" class="form-control form-control-sm item-rate" placeholder="0.00" required></td>
                <td><input type="number" step="0.01" name="shipments[${shipmentIndex}][items][${itemIndex}][amount]" class="form-control form-control-sm item-amount" placeholder="0.00" readonly></td>
                <td class="text-center"><button type="button" class="btn btn-sm btn-danger remove-batch-item"><i class="fas fa-trash"></i></button></td>
            </tr>
        `;
        tbody.insertAdjacentHTML('beforeend', newRow);
        updateBatchRemoveButtons(table);
        attachBatchItemListeners(table);
    }
});

// Remove batch line item
document.addEventListener('click', function(e) {
    if (e.target.closest('.remove-batch-item')) {
        const row = e.target.closest('tr');
        const table = row.closest('.shipment-items-table');
        row.remove();
        updateBatchRemoveButtons(table);
        calculateBatchShipmentTotal(table);
    }
});

// Update remove button states for batch items
function updateBatchRemoveButtons(table) {
    const rows = table.querySelectorAll('.line-item-row');
    const removeButtons = table.querySelectorAll('.remove-batch-item');
    removeButtons.forEach((btn, index) => {
        btn.disabled = false;
    });
}

// Calculate batch line item amount
function calculateBatchItemAmount(row) {
    const qty = parseFloat(row.querySelector('.item-quantity').value) || 0;
    const rate = parseFloat(row.querySelector('.item-rate').value) || 0;
    row.querySelector('.item-amount').value = (qty * rate).toFixed(2);
    const table = row.closest('.shipment-items-table');
    calculateBatchShipmentTotal(table);
}

// Attach listeners to batch line items
function attachBatchItemListeners(table) {
    table.querySelectorAll('.line-item-row').forEach(row => {
        ['item-quantity', 'item-rate'].forEach(cls => {
            const input = row.querySelector('.' + cls);
            input.removeEventListener('input', () => calculateBatchItemAmount(row));
            input.addEventListener('input', () => calculateBatchItemAmount(row));
        });
    });
}

// Calculate batch shipment total
function calculateBatchShipmentTotal(table) {
    const card = table.closest('.shipment-card');
    let subtotal = 0;
    table.querySelectorAll('.item-amount').forEach(input => subtotal += parseFloat(input.value) || 0);
    
    card.querySelector('.shipment-subtotal-display').value = subtotal.toFixed(2);
    card.querySelector('.shipment-shipping-cost').value = subtotal.toFixed(2);
    
    calculateBatchFinalTotal(card);
}

// Calculate batch final total
function calculateBatchFinalTotal(card) {
    const subtotal = parseFloat(card.querySelector('.shipment-shipping-cost').value) || 0;
    const tax = parseFloat(card.querySelector('.shipment-tax').value) || 0;
    card.querySelector('.shipment-total').value = (subtotal + tax).toFixed(2);
}

// Attach tax/discount listeners when shipment is added
document.addEventListener('DOMContentLoaded', function() {
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            mutation.addedNodes.forEach(function(node) {
                if (node.classList && node.classList.contains('shipment-card')) {
                    const table = node.querySelector('.shipment-items-table');
                    attachBatchItemListeners(table);
                    updateBatchRemoveButtons(table);
                    
                    node.querySelector('.shipment-tax').addEventListener('input', () => calculateBatchFinalTotal(node));
                    
                    // Add Cargo dimensional sync hooks (Air = normal weight, Sea = CBM volumetric checks)
                    const cargoType = document.getElementById('cargo_type').value;
                    const weightInput = node.querySelector('input[name*="[weight]"]');
                    
                    if (cargoType === 'air') {
                        if (weightInput) {
                            weightInput.addEventListener('input', function() {
                                const w = this.value || 1;
                                node.querySelectorAll('.item-quantity').forEach(input => {
                                    input.value = w;
                                    input.dispatchEvent(new Event('input'));
                                });
                            });
                        }
                    }
                }
            });
        });
    });
    
    observer.observe(document.getElementById('shipmentsContainer'), { childList: true });
});

// ========== BATCH PACKAGES MANAGEMENT (For Sea Cargo) ==========
let batchPackageIndexes = {};

function calculateBatchPackageCbm(row) {
    const length = parseFloat(row.querySelector('.pkg-length')?.value) || 0;
    const width = parseFloat(row.querySelector('.pkg-width')?.value) || 0;
    const height = parseFloat(row.querySelector('.pkg-height')?.value) || 0;
    const quantity = parseFloat(row.querySelector('.pkg-qty')?.value) || 1;
    
    const cbm = (length * width * height / 1000000) * quantity;
    const cbmInput = row.querySelector('.pkg-cbm');
    if (cbmInput) {
        cbmInput.value = cbm.toFixed(4);
    }
    return cbm;
}

function updateBatchShipmentTotalCbm(shipmentIndex) {
    let totalCbm = 0;
    const tbody = document.querySelector(`.shipment-packages-body-${shipmentIndex}`);
    if (tbody) {
        tbody.querySelectorAll('.package-row').forEach(row => {
            const cbmInput = row.querySelector('.pkg-cbm');
            const cbm = parseFloat(cbmInput?.value) || 0;
            totalCbm += cbm;
        });
        document.querySelector(`.shipment-total-cbm-${shipmentIndex}`).textContent = totalCbm.toFixed(4);
    }
}

// Add package to batch shipment
document.addEventListener('click', function(e) {
    if (e.target.closest('.add-batch-package')) {
        const btn = e.target.closest('.add-batch-package');
        const shipmentIndex = btn.dataset.shipmentIndex;
        const tbody = document.querySelector(`.shipment-packages-body-${shipmentIndex}`);
        
        if (!batchPackageIndexes[shipmentIndex]) {
            batchPackageIndexes[shipmentIndex] = 0;
        } else {
            batchPackageIndexes[shipmentIndex]++;
        }
        
        const pkgIndex = batchPackageIndexes[shipmentIndex];
        const newRow = `
            <tr class="package-row">
                <td>
                    <input type="text" name="shipments[${shipmentIndex}][packages][${pkgIndex}][description]" class="form-control form-control-sm" placeholder="Package description">
                </td>
                <td>
                    <input type="number" name="shipments[${shipmentIndex}][packages][${pkgIndex}][quantity]" class="form-control form-control-sm pkg-qty" value="1" min="1">
                </td>
                <td>
                    <input type="number" step="0.01" name="shipments[${shipmentIndex}][packages][${pkgIndex}][length]" class="form-control form-control-sm pkg-length" placeholder="0">
                </td>
                <td>
                    <input type="number" step="0.01" name="shipments[${shipmentIndex}][packages][${pkgIndex}][width]" class="form-control form-control-sm pkg-width" placeholder="0">
                </td>
                <td>
                    <input type="number" step="0.01" name="shipments[${shipmentIndex}][packages][${pkgIndex}][height]" class="form-control form-control-sm pkg-height" placeholder="0">
                </td>
                <td>
                    <input type="number" step="0.0001" name="shipments[${shipmentIndex}][packages][${pkgIndex}][cbm]" class="form-control form-control-sm pkg-cbm" placeholder="0">
                </td>
                <td>
                    <input type="number" step="0.01" name="shipments[${shipmentIndex}][packages][${pkgIndex}][weight]" class="form-control form-control-sm" placeholder="0">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-danger remove-batch-package"><i class="fas fa-trash"></i></button>
                </td>
            </tr>
        `;
        tbody.insertAdjacentHTML('beforeend', newRow);
        
        // Add event listeners for manual CBM input
        const row = tbody.lastElementChild;
        row.querySelectorAll('.pkg-cbm').forEach(input => {
            input.addEventListener('input', () => {
                updateBatchShipmentTotalCbm(shipmentIndex);
            });
        });
    }
});

// Remove package from batch shipment
document.addEventListener('click', function(e) {
    if (e.target.closest('.remove-batch-package')) {
        const row = e.target.closest('tr');
        const shipmentIndex = row.closest('tbody').className.match(/shipment-packages-body-(\d+)/)[1];
        row.remove();
        updateBatchShipmentTotalCbm(shipmentIndex);
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
