@extends('adminlte::page')

@section('title', 'Expense ' . $expense->expense_number)

@section('content_header')
    <h1>Expense Details</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ $expense->expense_number }}</h3>
                    <div class="card-tools">
                        @if($expense->status == 'pending')
                            <span class="badge badge-warning">Pending Approval</span>
                        @elseif($expense->status == 'approved')
                            <span class="badge badge-primary">Approved</span>
                        @elseif($expense->status == 'rejected')
                            <span class="badge badge-danger">Rejected</span>
                        @elseif($expense->status == 'paid')
                            <span class="badge badge-success">Paid</span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Expense Information</h5>
                            <table class="table table-sm">
                                <tr>
                                    <th width="40%">Category:</th>
                                    <td>
                                        <span class="badge" style="background-color: {{ $expense->category->color }}">
                                            {{ $expense->category->name }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Amount:</th>
                                    <td>
                                        @if(($expense->currency ?? 'UGX') === 'USD')
                                            <strong class="text-success">${{ number_format($expense->amount, 0) }}</strong>
                                        @else
                                            <strong class="text-success">UGX {{ number_format($expense->amount, 0) }}</strong>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Expense Date:</th>
                                    <td>{{ $expense->expense_date->format('d M Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Payment Method:</th>
                                    <td>{{ ucfirst(str_replace('_', ' ', $expense->payment_method)) }}</td>
                                </tr>
                                <tr>
                                    <th>Reference Number:</th>
                                    <td>{{ $expense->reference_number ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Tracking Information</h5>
                            <table class="table table-sm">
                                <tr>
                                    <th width="40%">Recorded By:</th>
                                    <td>{{ $expense->recorder->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Created At:</th>
                                    <td>{{ $expense->created_at->format('d M Y H:i') }}</td>
                                </tr>
                                @if($expense->approved_by)
                                    <tr>
                                        <th>Approved By:</th>
                                        <td>{{ $expense->approver->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Approved At:</th>
                                        <td>{{ $expense->approved_at->format('d M Y H:i') }}</td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    @if($expense->description)
                        <hr>
                        <h5>Description</h5>
                        <p>{{ $expense->description }}</p>
                    @endif

                    @if($expense->notes)
                        <hr>
                        <h5>Notes</h5>
                        <p>{{ $expense->notes }}</p>
                    @endif

                    @if($expense->rejection_reason)
                        <hr>
                        <div class="alert alert-danger">
                            <h5><i class="fas fa-exclamation-triangle"></i> Rejection Reason</h5>
                            <p>{{ $expense->rejection_reason }}</p>
                        </div>
                    @endif

                    @if($expense->receipt_image)
                        <hr>
                        <h5>Receipt Image</h5>
                        <a href="{{ asset('storage/' . $expense->receipt_image) }}" target="_blank">
                            <img src="{{ asset('storage/' . $expense->receipt_image) }}" alt="Receipt" class="img-fluid" style="max-height: 300px;">
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Actions</h3>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @can('approve expenses')
                        @if($expense->canBeApproved())
                            <form method="POST" action="{{ route('admin.expenses.approve', $expense) }}">
                                @csrf
                                <button type="submit" class="btn btn-success btn-block" onclick="return confirm('Approve this expense?')">
                                    <i class="fas fa-check"></i> Approve
                                </button>
                            </form>
                        @endif

                        @if($expense->canBeApproved() || $expense->isRejected())
                            <button type="button" class="btn btn-danger btn-block" data-toggle="modal" data-target="#rejectModal">
                                <i class="fas fa-times"></i> Reject
                            </button>
                        @endif

                        @if($expense->isApproved())
                            <form method="POST" action="{{ route('admin.expenses.mark-paid', $expense) }}">
                                @csrf
                                <button type="submit" class="btn btn-info btn-block" onclick="return confirm('Mark this expense as paid?')">
                                    <i class="fas fa-money-bill"></i> Mark as Paid
                                </button>
                            </form>
                        @endif
                        @endcan

                        @can('edit expenses')
                        @if($expense->canBeEdited())
                            <a href="{{ route('admin.expenses.edit', $expense) }}" class="btn btn-warning btn-block">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        @endif
                        @endcan

                        <a href="{{ route('admin.expenses.receipt', $expense) }}" class="btn btn-secondary btn-block">
                            <i class="fas fa-file-pdf"></i> Download PDF
                        </a>

                        @can('delete expenses')
                        @if($expense->canBeDeleted())
                            <form method="POST" action="{{ route('admin.expenses.destroy', $expense) }}" class="delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-block">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        @endif
                        @endcan

                        <a href="{{ route('admin.expenses.index') }}" class="btn btn-default btn-block">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    @if($expense->canBeApproved() || $expense->isRejected())
        <div class="modal fade" id="rejectModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST" action="{{ route('admin.expenses.reject', $expense) }}">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Reject Expense</h5>
                            <button type="button" class="close" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="rejection_reason">Rejection Reason <span class="text-danger">*</span></label>
                                <textarea id="rejection_reason" name="rejection_reason" class="form-control" rows="4" required placeholder="Please explain why this expense is being rejected..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">Reject Expense</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@stop

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.delete-form').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
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
