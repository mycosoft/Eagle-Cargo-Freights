@extends('adminlte::page')

@section('title', 'Edit Expense ' . $expense->expense_number)

@section('content_header')
    <h1>Edit Expense</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Edit: {{ $expense->expense_number }}</h3>
            <div class="card-tools">
                <a href="{{ route('admin.expenses.show', $expense) }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Expense
                </a>
            </div>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.expenses.update', $expense) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="category_id">Category <span class="text-danger">*</span></label>
                            <select id="category_id" name="category_id" class="form-control @error('category_id') is-invalid @enderror" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $expense->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <span class="invalid-feedback" role="alert">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="amount">Amount <span class="text-danger">*</span></label>
                            <input type="number" id="amount" name="amount" class="form-control @error('amount') is-invalid @enderror"
                                   value="{{ old('amount', $expense->amount) }}" step="0.01" min="0.01" required>
                            @error('amount')
                                <span class="invalid-feedback" role="alert">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="currency">Currency <span class="text-danger">*</span></label>
                            <select id="currency" name="currency" class="form-control @error('currency') is-invalid @enderror" required>
                                <option value="UGX" {{ old('currency', $expense->currency ?? 'UGX') == 'UGX' ? 'selected' : '' }}>UGX</option>
                                <option value="USD" {{ old('currency', $expense->currency) == 'USD' ? 'selected' : '' }}>USD</option>
                            </select>
                            @error('currency')
                                <span class="invalid-feedback" role="alert">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="expense_date">Expense Date <span class="text-danger">*</span></label>
                            <input type="date" id="expense_date" name="expense_date" class="form-control @error('expense_date') is-invalid @enderror"
                                   value="{{ old('expense_date', $expense->expense_date->format('Y-m-d')) }}" required>
                            @error('expense_date')
                                <span class="invalid-feedback" role="alert">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="payment_method">Payment Method <span class="text-danger">*</span></label>
                            <select id="payment_method" name="payment_method" class="form-control @error('payment_method') is-invalid @enderror" required>
                                <option value="">Select Method</option>
                                <option value="cash" {{ old('payment_method', $expense->payment_method) == 'cash' ? 'selected' : '' }}>Cash</option>
                                <option value="card" {{ old('payment_method', $expense->payment_method) == 'card' ? 'selected' : '' }}>Card</option>
                                <option value="bank_transfer" {{ old('payment_method', $expense->payment_method) == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                <option value="mobile_money" {{ old('payment_method', $expense->payment_method) == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                                <option value="check" {{ old('payment_method', $expense->payment_method) == 'check' ? 'selected' : '' }}>Check</option>
                            </select>
                            @error('payment_method')
                                <span class="invalid-feedback" role="alert">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="reference_number">Reference Number</label>
                            <input type="text" id="reference_number" name="reference_number" class="form-control @error('reference_number') is-invalid @enderror"
                                   value="{{ old('reference_number', $expense->reference_number) }}" placeholder="Receipt #, Check #, etc.">
                            @error('reference_number')
                                <span class="invalid-feedback" role="alert">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="receipt_image">Receipt Image</label>
                            <input type="file" id="receipt_image" name="receipt_image" class="form-control-file @error('receipt_image') is-invalid @enderror"
                                   accept="image/*">
                            @if($expense->receipt_image)
                                <small class="form-text text-muted">
                                    Current: <a href="{{ asset('storage/' . $expense->receipt_image) }}" target="_blank">View</a>
                                </small>
                            @endif
                            <small class="form-text text-muted">Upload new receipt to replace (max 5MB)</small>
                            @error('receipt_image')
                                <span class="invalid-feedback" role="alert">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror"
                              rows="3">{{ old('description', $expense->description) }}</textarea>
                    @error('description')
                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="notes">Notes</label>
                    <textarea id="notes" name="notes" class="form-control @error('notes') is-invalid @enderror"
                              rows="2" placeholder="Additional notes...">{{ old('notes', $expense->notes) }}</textarea>
                    @error('notes')
                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Expense
                    </button>
                    <a href="{{ route('admin.expenses.show', $expense) }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
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
