@extends('adminlte::page')

@section('title', 'Record Payment')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1><i class="fas fa-plus-circle"></i> Record New Payment</h1>
        </div>
        <div class="col-sm-6">
            <a href="{{ route('admin.transactions.index') }}" class="btn btn-secondary float-right">
                <i class="fas fa-arrow-left"></i> Back to Transactions
            </a>
        </div>
    </div>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Payment Details</h3>
    </div>
    <form action="{{ route('admin.transactions.store') }}" method="POST">
        @csrf
        <div class="card-body">

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="invoice_id">Invoice <span class="text-danger">*</span></label>
                        <select name="invoice_id" id="invoice_id" class="form-control @error('invoice_id') is-invalid @enderror" required>
                            <option value="">Select Invoice...</option>
                            @foreach($invoices as $invoice)
                                <option value="{{ $invoice->id }}"
                                    data-balance="{{ $invoice->balance }}"
                                    data-client="{{ $invoice->shipment?->client?->name ?? 'N/A' }}"
                                    data-tracking="{{ $invoice->shipment?->tracking_number ?? '' }}"
                                    data-total="{{ $invoice->total }}"
                                    {{ old('invoice_id') == $invoice->id ? 'selected' : '' }}>
                                    {{ $invoice->invoice_number }} — {{ $invoice->shipment?->client?->name ?? 'N/A' }}
                                    (Balance: {{ number_format($invoice->balance, 2) }})
                                </option>
                            @endforeach
                        </select>
                        @error('invoice_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Invoice Summary</label>
                        <div class="card card-body bg-light p-2" id="invoiceSummary">
                            <span class="text-muted">Select an invoice to see details</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="amount">Amount <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text">{{ \App\Models\Setting::getCurrencySymbol() }}</span></div>
                            <input type="number" step="0.01" name="amount" id="amount" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount') }}" placeholder="0.00" required>
                        </div>
                        @error('amount') <span class="text-danger small">{{ $message }}</span> @enderror
                        <small id="maxHint" class="form-text text-muted"></small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="payment_date">Payment Date <span class="text-danger">*</span></label>
                        <input type="date" name="payment_date" id="payment_date" class="form-control @error('payment_date') is-invalid @enderror" value="{{ old('payment_date', date('Y-m-d')) }}" required>
                        @error('payment_date') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="payment_method">Payment Method <span class="text-danger">*</span></label>
                        <select name="payment_method" id="payment_method" class="form-control @error('payment_method') is-invalid @enderror" required>
                            <option value="">Select Method...</option>
                            <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="card" {{ old('payment_method') == 'card' ? 'selected' : '' }}>Card</option>
                            <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                            <option value="mobile_money" {{ old('payment_method') == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                        </select>
                        @error('payment_method') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="reference_number">Reference Number</label>
                        <input type="text" name="reference_number" id="reference_number" class="form-control" value="{{ old('reference_number') }}" placeholder="e.g. Bank Tran. ID, Mobile Money Ref...">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="notes">Notes</label>
                        <textarea name="notes" id="notes" rows="2" class="form-control" placeholder="Additional notes...">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Record Payment</button>
            <a href="{{ route('admin.transactions.index') }}" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
        </div>
    </form>
</div>
@stop

@section('js')
<script>
document.getElementById('invoice_id').addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    const balance = parseFloat(opt.dataset.balance) || 0;
    const total = parseFloat(opt.dataset.total) || 0;
    const client = opt.dataset.client || '';
    const tracking = opt.dataset.tracking || '';

    if (this.value) {
        document.getElementById('invoiceSummary').innerHTML = `
            <strong>Client:</strong> ${client}<br>
            <strong>Tracking #:</strong> ${tracking || 'N/A'}<br>
            <strong>Invoice Total:</strong> <span class="text-primary">${total.toLocaleString()}</span><br>
            <strong>Balance Due:</strong> <span class="text-danger font-weight-bold">${balance.toLocaleString()}</span>
        `;
        document.getElementById('amount').max = balance;
        document.getElementById('amount').value = balance.toFixed(2);
        document.getElementById('maxHint').textContent = `Max payable: ${balance.toLocaleString()}`;
    } else {
        document.getElementById('invoiceSummary').innerHTML = '<span class="text-muted">Select an invoice to see details</span>';
        document.getElementById('amount').removeAttribute('max');
        document.getElementById('maxHint').textContent = '';
    }
});

// Trigger if old value loaded
if (document.getElementById('invoice_id').value) {
    document.getElementById('invoice_id').dispatchEvent(new Event('change'));
}
</script>
@stop

@section('footer')
    <strong>Copyright &copy; {{ date('Y') }} <a href="#">Eagle Cargo Freights</a>.</strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
        <b>Support Call</b> +256 200 991 118
    </div>
@stop
