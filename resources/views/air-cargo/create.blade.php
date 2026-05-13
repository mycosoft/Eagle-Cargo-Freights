@extends('adminlte::page')

@section('title', 'Create Air Cargo Shipment')

@section('content_header')
    <h1><i class="fas fa-plane"></i> Create Air Cargo Shipment</h1>
@stop

@section('content')
    <form action="{{ route('admin.air-cargo.store') }}" method="POST">
        @csrf
        
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Shipment Information</h3>
            </div>
            <div class="card-body">

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group" style="position: relative;">
                            <label for="client_search">Client <span class="text-danger">*</span></label>
                            <input type="text" id="client_search" class="form-control" placeholder="Search client by name, email, phone..." autocomplete="off">
                            <input type="hidden" name="client_id" id="client_id" value="{{ old('client_id') }}" required>
                            <div id="client_results" class="list-group shadow" style="position: absolute; top: 100%; left: 0; right: 0; z-index: 9999; max-height: 250px; overflow-y: auto; display: none;"></div>
                            @error('client_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="current_status">Status</label>
                            <select name="current_status" id="current_status" class="form-control">
                                <option value="Pending" selected>Pending</option>
                                <option value="Picked Up">Picked Up</option>
                                <option value="In Transit">In Transit</option>
                                <option value="At Warehouse in China">At Warehouse in China</option>
                                <option value="Delivered">Delivered</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="china_warehouse_date">Date Received at China Warehouse</label>
                            <input type="date" name="china_warehouse_date" id="china_warehouse_date" class="form-control" value="{{ old('china_warehouse_date') }}">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="origin">Origin <span class="text-danger">*</span></label>
                            <input type="text" name="origin" id="origin" class="form-control @error('origin') is-invalid @enderror" value="{{ old('origin') }}" required>
                            @error('origin')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="destination">Destination <span class="text-danger">*</span></label>
                            <input type="text" name="destination" id="destination" class="form-control @error('destination') is-invalid @enderror" value="{{ old('destination') }}" required>
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
                                    <input type="number" name="delivery_time_min" class="form-control @error('delivery_time_min') is-invalid @enderror" placeholder="Min (e.g., 5)" value="{{ old('delivery_time_min') }}" min="1" required>
                                    @error('delivery_time_min')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-6">
                                    <input type="number" name="delivery_time_max" class="form-control @error('delivery_time_max') is-invalid @enderror" placeholder="Max (e.g., 7)" value="{{ old('delivery_time_max') }}" min="1" required>
                                    @error('delivery_time_max')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <small class="form-text text-muted">Example: 5-7 days, 10-14 days</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="weight">Weight (kg)</label>
                            <input type="number" step="0.01" name="weight" id="weight" class="form-control" value="{{ old('weight') }}">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" rows="3" class="form-control">{{ old('description') }}</textarea>
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
                                    <input type="text" name="sender_name" id="sender_name" class="form-control" value="{{ old('sender_name') }}">
                                </div>
                                <div class="form-group">
                                    <label for="sender_phone">Sender Phone</label>
                                    <input type="tel" name="sender_phone" id="sender_phone" class="form-control" value="{{ old('sender_phone') }}">
                                </div>
                                <div class="form-group">
                                    <label for="sender_address">Sender Address</label>
                                    <textarea name="sender_address" id="sender_address" rows="2" class="form-control">{{ old('sender_address') }}</textarea>
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
                                    <input type="text" name="receiver_name" id="receiver_name" class="form-control" value="{{ old('receiver_name') }}">
                                </div>
                                <div class="form-group">
                                    <label for="receiver_phone">Receiver Phone</label>
                                    <input type="tel" name="receiver_phone" id="receiver_phone" class="form-control" value="{{ old('receiver_phone') }}">
                                </div>
                                <div class="form-group">
                                    <label for="receiver_address">Receiver Address</label>
                                    <textarea name="receiver_address" id="receiver_address" rows="2" class="form-control">{{ old('receiver_address') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>
                <h5>Package Details (Optional)</h5>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="num_packages">Number of Packages</label>
                            <input type="number" name="num_packages" id="num_packages" class="form-control" value="{{ old('num_packages') }}" min="1">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="package_type">Package Type</label>
                            <select name="package_type" id="package_type" class="form-control">
                                <option value="">Select Type</option>
                                <option value="box">Box</option>
                                <option value="pallet">Pallet</option>
                                <option value="envelope">Envelope</option>
                                <option value="custom">Custom</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <div class="custom-control custom-checkbox mt-4">
                                <input type="checkbox" class="custom-control-input" id="fragile" name="fragile" value="1">
                                <label class="custom-control-label" for="fragile">Fragile Item</label>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>
                <h5>Pricing & Billing</h5>
                
                <div class="row mb-3">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="currency">Currency</label>
                            <select name="currency" id="currency" class="form-control">
                                <option value="USD" {{ old('currency', 'USD') == 'USD' ? 'selected' : '' }}>USD ($)</option>
                                <option value="EUR" {{ old('currency', 'USD') == 'EUR' ? 'selected' : '' }}>EUR (€)</option>
                                <option value="GBP" {{ old('currency', 'USD') == 'GBP' ? 'selected' : '' }}>GBP (£)</option>
                                <option value="UGX" {{ old('currency', 'USD') == 'UGX' ? 'selected' : '' }}>UGX (Shs)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <h6 class="mb-3">Invoice Line Items</h6>
                <div class="table-responsive">
                    <table class="table table-bordered" id="lineItemsTable">
                        <thead class="thead-light">
                            <tr>
                                <th width="40%">Description</th>
                                <th width="15%">Weight (kg)</th>
                                <th width="20%">Rate (<span class="currency-label">USD</span>)</th>
                                <th width="20%">Amount (<span class="currency-label">USD</span>)</th>
                                <th width="5%"></th>
                            </tr>
                        </thead>
                        <tbody id="lineItemsBody">
                        </tbody>
                    </table>
                </div>

                <button type="button" class="btn btn-sm btn-success mb-3" id="addLineItem">
                    <i class="fas fa-plus"></i> Add Line Item
                </button>

                <hr>

                <div class="row">
                    <div class="col-md-6 offset-md-6">
                        <table class="table table-sm">
                            <tr>
                                <td class="text-right"><strong>Subtotal:</strong></td>
                                <td width="200">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend"><span class="input-group-text currency-symbol">USD</span></div>
                                        <input type="number" step="0.01" id="subtotal_display" class="form-control text-right font-weight-bold" value="0.00" readonly>
                                    </div>
                                    <input type="hidden" name="shipping_cost" id="shipping_cost" value="0">
                                </td>
                            </tr>
                            <tr>
                                <td class="text-right"><strong>Tax:</strong></td>
                                <td>
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend"><span class="input-group-text currency-symbol">USD</span></div>
                                        <input type="number" step="0.01" name="tax" id="tax" class="form-control text-right" value="{{ old('tax', 0) }}">
                                    </div>
                                </td>
                            </tr>
                            <tr class="table-active">
                                <td class="text-right"><h5><strong>Total Amount:</strong></h5></td>
                                <td>
                                    <div class="input-group">
                                        <div class="input-group-prepend"><span class="input-group-text currency-symbol font-weight-bold">USD</span></div>
                                        <input type="number" step="0.01" name="total_amount" id="total_amount" class="form-control font-weight-bold text-right" value="{{ old('total_amount', 0) }}" readonly>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="payment_method">Payment Method</label>
                            <select name="payment_method" id="payment_method" class="form-control">
                                <option value="">Select Method</option>
                                <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                                <option value="card" {{ old('payment_method') == 'card' ? 'selected' : '' }}>Card</option>
                                <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                <option value="mobile_money" {{ old('payment_method') == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="payment_status">Payment Status</label>
                            <select name="payment_status" id="payment_status" class="form-control">
                                <option value="pending" {{ old('payment_status', 'pending') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="paid" {{ old('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Create Air Shipment
                </button>
                <a href="{{ route('admin.air-cargo.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </div>
    </form>
@stop

@section('js')
<script>
const clientSearchInput = document.getElementById('client_search');
const clientResults = document.getElementById('client_results');
const clientIdInput = document.getElementById('client_id');
let searchTimeout;

if (clientSearchInput) {
    clientSearchInput.addEventListener('input', function() {
        const query = this.value;
        clearTimeout(searchTimeout);
        
        if (query.length < 1) {
            clientResults.style.display = 'none';
            return;
        }
        
        searchTimeout = setTimeout(() => {
            fetch('{{ route("admin.clients.search") }}?q=' + encodeURIComponent(query))
                .then(response => response.json())
                .then(clients => {
                    if (clients.length > 0) {
                        clientResults.innerHTML = clients.map(client => 
                            `<a href="#" class="list-group-item list-group-item-action" data-id="${client.id}" data-name="${client.name}">
                                <strong>${client.name}</strong>
                                <small class="text-muted">${client.email || ''} ${client.company ? '- ' + client.company : ''}</small>
                            </a>`
                        ).join('');
                        clientResults.style.display = 'block';
                    } else {
                        clientResults.innerHTML = '<div class="list-group-item">No clients found</div>';
                        clientResults.style.display = 'block';
                    }
                });
        }, 100);
    });

    clientResults.addEventListener('click', function(e) {
        const item = e.target.closest('a');
        if (item) {
            e.preventDefault();
            clientIdInput.value = item.dataset.id;
            clientSearchInput.value = item.dataset.name;
            clientResults.style.display = 'none';
        }
    });

    document.addEventListener('click', function(e) {
        if (!clientSearchInput.contains(e.target) && !clientResults.contains(e.target)) {
            clientResults.style.display = 'none';
        }
    });
}

// Line Items Management
let itemIndex = 0;

// Add new line item
document.getElementById('addLineItem').addEventListener('click', function() {
    const tbody = document.getElementById('lineItemsBody');
    const newRow = `
        <tr class="line-item-row">
            <td>
                <select name="items[${itemIndex}][description]" class="form-control" required>
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
            <td>
                <input type="number" name="items[${itemIndex}][quantity]" class="form-control item-quantity" value="1" min="1" required>
            </td>
            <td>
                <input type="number" step="0.01" name="items[${itemIndex}][rate]" class="form-control item-rate" placeholder="0.00" required>
            </td>
            <td>
                <input type="number" step="0.01" name="items[${itemIndex}][amount]" class="form-control item-amount" placeholder="0.00" readonly>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-danger remove-item">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    `;
    tbody.insertAdjacentHTML('beforeend', newRow);
    itemIndex++;
    updateRemoveButtons();
    attachLineItemListeners();
});

// Remove line item
document.addEventListener('click', function(e) {
    if (e.target.closest('.remove-item')) {
        const row = e.target.closest('tr');
        row.remove();
        updateRemoveButtons();
        calculateLineItemsTotal();
    }
});

// Update remove button states
function updateRemoveButtons() {
    const rows = document.querySelectorAll('.line-item-row');
    const removeButtons = document.querySelectorAll('.remove-item');
    removeButtons.forEach((btn, index) => {
        btn.disabled = false;
    });
}

// Calculate line item amount (quantity * rate)
function calculateLineItemAmount(row) {
    const quantity = parseFloat(row.querySelector('.item-quantity').value) || 0;
    const rate = parseFloat(row.querySelector('.item-rate').value) || 0;
    const amount = quantity * rate;
    row.querySelector('.item-amount').value = amount.toFixed(2);
    calculateLineItemsTotal();
}

// Attach listeners to line items
function attachLineItemListeners() {
    document.querySelectorAll('.line-item-row').forEach(row => {
        const quantityInput = row.querySelector('.item-quantity');
        const rateInput = row.querySelector('.item-rate');
        
        quantityInput.removeEventListener('input', () => calculateLineItemAmount(row));
        rateInput.removeEventListener('input', () => calculateLineItemAmount(row));
        
        quantityInput.addEventListener('input', () => calculateLineItemAmount(row));
        rateInput.addEventListener('input', () => calculateLineItemAmount(row));
    });
}

// Calculate subtotal from all line items
function calculateLineItemsTotal() {
    let subtotal = 0;
    document.querySelectorAll('.item-amount').forEach(input => {
        subtotal += parseFloat(input.value) || 0;
    });
    
    document.getElementById('subtotal_display').value = subtotal.toFixed(2);
    document.getElementById('shipping_cost').value = subtotal.toFixed(2);
    
    calculateFinalTotal();
}

// Currency symbol mapping
const currencyMap = {
    'UGX': 'UGX',
    'USD': '$',
    'EUR': '€',
    'GBP': '£'
};

// Update currency symbols dynamically
document.getElementById('currency').addEventListener('change', function() {
    const symbol = currencyMap[this.value] || this.value;
    document.querySelectorAll('.currency-symbol').forEach(el => el.textContent = symbol);
    document.querySelectorAll('.currency-label').forEach(el => el.textContent = symbol);
});
// Trigger initial symbol setup
document.getElementById('currency').dispatchEvent(new Event('change'));

// Auto-sync shipment weight with item weight/quantity
document.getElementById('weight').addEventListener('input', function() {
    const w = this.value || 1;
    document.querySelectorAll('.item-quantity').forEach(input => {
        input.value = w;
        input.dispatchEvent(new Event('input'));
    });
});

// Calculate final total (subtotal + tax)
function calculateFinalTotal() {
    const subtotal = parseFloat(document.getElementById('shipping_cost').value) || 0;
    const tax = parseFloat(document.getElementById('tax').value) || 0;
    
    // Removed discount for air-cargo
    const total = subtotal + tax;
    document.getElementById('total_amount').value = total.toFixed(2);
}

// Attach listeners to tax
document.getElementById('tax').addEventListener('input', calculateFinalTotal);

// Initial setup
attachLineItemListeners();
updateRemoveButtons();

// Set initial weight if present
const initWeight = document.getElementById('weight').value;
if(initWeight) {
    document.getElementById('weight').dispatchEvent(new Event('input'));
}

// ========== PACKAGES MANAGEMENT ==========
let packageIndex = 0;

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
