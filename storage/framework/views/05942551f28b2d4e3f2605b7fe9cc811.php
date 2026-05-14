<?php $__env->startSection('title', 'Batch Details'); ?>

<?php $__env->startSection('content_header'); ?>
    <div class="row">
        <div class="col-sm-6">
            <h1>Batch: <?php echo e($batch->batch_number); ?></h1>
        </div>
        <div class="col-sm-6">
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view batches')): ?>
            <a href="<?php echo e(route('admin.batches.packing-list', $batch)); ?>" class="btn btn-success float-right ml-2">
                <i class="fas fa-file-pdf"></i> Generate Packing List
            </a>
            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit batches')): ?>
            <a href="<?php echo e(route('admin.batches.edit', $batch)); ?>" class="btn btn-warning float-right ml-2">
                <i class="fas fa-edit"></i> Edit
            </a>
            <?php endif; ?>
            <a href="<?php echo e(route('admin.batches.index')); ?>" class="btn btn-secondary float-right">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Batch Information -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Batch Information</h3>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-5">Batch Number:</dt>
                        <dd class="col-sm-7"><strong><?php echo e($batch->batch_number); ?></strong></dd>

                        <dt class="col-sm-5">Name:</dt>
                        <dd class="col-sm-7"><?php echo e($batch->name); ?></dd>

                        <dt class="col-sm-5">Status:</dt>
                        <dd class="col-sm-7">
                            <?php
                                $statusColors = [
                                    'Pending'             => 'warning',
                                    'Picked Up'           => 'info',
                                    'In Transit'          => 'primary',
                                    'Arrived at Facility' => 'secondary',
                                    'Out for Delivery'    => 'info',
                                    'Delivered'           => 'success',
                                    'On Hold'             => 'dark',
                                    'Cancelled'           => 'danger',
                                    // lowercase fallbacks
                                    'pending'             => 'warning',
                                    'processing'          => 'info',
                                    'in_transit'          => 'primary',
                                    'delivered'           => 'success',
                                    'cancelled'           => 'danger',
                                ];
                                $badgeClass = $statusColors[$batch->current_status] ?? 'secondary';
                            ?>
                            <span class="badge badge-<?php echo e($badgeClass); ?>"><?php echo e(ucfirst(str_replace('_', ' ', $batch->current_status))); ?></span>
                        </dd>

                        <dt class="col-sm-5">Shipments:</dt>
                        <dd class="col-sm-7"><span class="badge badge-info"><?php echo e($batch->shipments->count()); ?></span></dd>

                        <dt class="col-sm-5">Created By:</dt>
                        <dd class="col-sm-7"><?php echo e($batch->creator->name ?? 'N/A'); ?></dd>

                        <dt class="col-sm-5">Created:</dt>
                        <dd class="col-sm-7"><?php echo e($batch->created_at->format('M d, Y H:i')); ?></dd>

                        <dt class="col-sm-5">Revenue:</dt>
                        <dd class="col-sm-7"><strong class="text-success"><?php echo e(\App\Models\Setting::getCurrencySymbol(null)); ?> <?php echo e(number_format($batch->total_revenue, 0)); ?></strong></dd>

                        <dt class="col-sm-5">Total Costs:</dt>
                        <dd class="col-sm-7">
                            <strong class="text-danger"><?php echo e(\App\Models\Setting::getCurrencySymbol(null)); ?> <?php echo e(number_format($batch->total_costs, 0)); ?></strong>
                            <?php if($batch->total_cost_usd > 0): ?>
                                <br><small class="text-muted">$<?php echo e(number_format($batch->total_cost_usd, 0)); ?></small>
                            <?php endif; ?>
                        </dd>

                        <dt class="col-sm-5">Profit:</dt>
                        <dd class="col-sm-7">
                            <?php $currencySymbol = \App\Models\Setting::getCurrencySymbol(null); ?>
                            <?php if($batch->profit >= 0): ?>
                                <strong class="text-success"><?php echo e($currencySymbol); ?> <?php echo e(number_format($batch->profit, 0)); ?></strong>
                                <span class="text-success">(<?php echo e($batch->profit_margin); ?>%)</span>
                            <?php else: ?>
                                <strong class="text-danger">-<?php echo e($currencySymbol); ?> <?php echo e(number_format(abs($batch->profit), 0)); ?></strong>
                                <span class="text-danger">(<?php echo e($batch->profit_margin); ?>%)</span>
                            <?php endif; ?>
                        </dd>
                    </dl>

                    <?php if($batch->description): ?>
                        <hr>
                        <p><strong>Description:</strong></p>
                        <p><?php echo e($batch->description); ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Update Batch Status -->
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit batches')): ?>
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">Update Batch Status</h3>
                </div>
                <form action="<?php echo e(route('admin.batches.update-status', $batch)); ?>" method="POST" id="batch-status-form">
                    <?php echo csrf_field(); ?>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="current_status">New Status</label>
                            <select name="current_status" id="current_status" class="form-control" required>
                                <option value="Pending" <?php echo e($batch->current_status == 'Pending' ? 'selected' : ''); ?>>Pending</option>
                                <option value="Picked Up" <?php echo e($batch->current_status == 'Picked Up' ? 'selected' : ''); ?>>Picked Up</option>
                                <option value="In Transit" <?php echo e($batch->current_status == 'In Transit' ? 'selected' : ''); ?>>In Transit</option>
                                <option value="At Warehouse in China" <?php echo e($batch->current_status == 'At Warehouse in China' ? 'selected' : ''); ?>>At Warehouse in China</option>
                                <option value="Arrived at Facility" <?php echo e($batch->current_status == 'Arrived at Facility' ? 'selected' : ''); ?>>Arrived at Facility</option>
                                <option value="Out for Delivery" <?php echo e($batch->current_status == 'Out for Delivery' ? 'selected' : ''); ?>>Out for Delivery</option>
                                <option value="Delivered" <?php echo e($batch->current_status == 'Delivered' ? 'selected' : ''); ?>>Delivered</option>
                                <option value="On Hold" <?php echo e($batch->current_status == 'On Hold' ? 'selected' : ''); ?>>On Hold</option>
                                <option value="Cancelled" <?php echo e($batch->current_status == 'Cancelled' ? 'selected' : ''); ?>>Cancelled</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="location">Location <span class="text-danger">*</span></label>
                            <input type="text" name="location" id="location" class="form-control" placeholder="Current location (e.g., Warehouse, In Transit, etc.)" required>
                            <small class="form-text text-muted">Specify the current location for this status update</small>
                        </div>
                        <div class="form-group">
                            <label for="notes">Notes (Optional)</label>
                            <textarea name="notes" id="notes" rows="3" class="form-control" placeholder="Add notes about this status update..."></textarea>
                        </div>
                        <div class="form-group" id="storage-fee-group" style="display: none;">
                            <label for="storage_fee">Storage Fee (per shipment)</label>
                            <input type="number" name="storage_fee" id="storage_fee" class="form-control" placeholder="Enter storage fee amount" min="0" step="0.01">
                            <small class="form-text text-muted">Enter 0 if no storage fee</small>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-warning btn-block">
                            <i class="fas fa-sync"></i> Update Status for All Shipments
                        </button>
                    </div>
                </form>
            </div>
            <?php endif; ?>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Shipments in This Batch</h3>
                    <div class="card-tools">
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit batches')): ?>
                        <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#addShipmentModal">
                            <i class="fas fa-plus"></i> Add Shipment
                        </button>
                        <?php endif; ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit batches')): ?>
                        <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#addExpenseModal">
                            <i class="fas fa-plus"></i> Add Expense
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Tracking #</th>
                                    <th>Client</th>
                                    <th>Route</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $batch->shipments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $shipment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><strong><?php echo e($shipment->tracking_number); ?></strong></td>
                                        <td><?php echo e($shipment->client->name); ?></td>
                                        <td><?php echo e($shipment->origin); ?> → <?php echo e($shipment->destination); ?></td>
                                        <td>
                                            <?php
                                                $statusColors = [
                                                    'Pending' => 'warning',
                                                    'Picked Up' => 'info',
                                                    'In Transit' => 'primary',
                                                    'Arrived at Facility' => 'secondary',
                                                    'Out for Delivery' => 'info',
                                                    'Delivered' => 'success',
                                                    'On Hold' => 'dark',
                                                    'Cancelled' => 'danger',
                                                ];
                                                $badgeClass = $statusColors[$shipment->current_status] ?? 'secondary';
                                            ?>
                                            <span class="badge badge-<?php echo e($badgeClass); ?>"><?php echo e($shipment->current_status); ?></span>
                                        </td>
                                        <td>
                                            <a href="<?php echo e(route('admin.shipments.show', $shipment)); ?>" class="btn btn-sm btn-info" title="View Shipment">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit batches')): ?>
                                            <form action="<?php echo e(route('admin.batches.remove-shipment', [$batch, $shipment])); ?>" method="POST" style="display:inline-block;" onsubmit="return confirm('Remove this shipment from the batch?');">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="btn btn-sm btn-danger" title="Remove from Batch">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="5" class="text-center">No shipments in this batch yet.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Invoices & Payments -->
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-file-invoice-dollar"></i> Invoices & Payments</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Invoice #</th>
                                    <th>Client</th>
                                    <th>Total</th>
                                    <th>Paid</th>
                                    <th>Balance</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $totalInvoiced = 0;
                                    $totalPaid = 0;
                                    $totalOutstanding = 0;
                                ?>
                                <?php $__empty_1 = true; $__currentLoopData = $batch->shipments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $shipment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <?php if($shipment->invoice): ?>
                                        <?php
                                            $totalInvoiced += $shipment->invoice->total;
                                            $totalPaid += $shipment->invoice->amount_paid;
                                            $totalOutstanding += $shipment->invoice->balance;
                                            $invCurrency = \App\Models\Setting::getCurrencySymbol($shipment->currency ?? null);
                                        ?>
                                        <tr>
                                            <td><strong><?php echo e($shipment->invoice->invoice_number); ?></strong></td>
                                            <td><?php echo e($shipment->client->name); ?></td>
                                            <td><?php echo e($invCurrency); ?> <?php echo e(number_format($shipment->invoice->total, 0)); ?></td>
                                            <td class="text-success"><?php echo e($invCurrency); ?> <?php echo e(number_format($shipment->invoice->amount_paid, 0)); ?></td>
                                            <td class="text-danger"><strong><?php echo e($invCurrency); ?> <?php echo e(number_format($shipment->invoice->balance, 0)); ?></strong></td>
                                            <td>
                                                <?php if($shipment->invoice->status == 'paid'): ?>
                                                    <span class="badge badge-success">Paid</span>
                                                <?php elseif($shipment->invoice->status == 'partial'): ?>
                                                    <span class="badge badge-warning">Partially Paid</span>
                                                <?php elseif($shipment->invoice->status == 'overdue'): ?>
                                                    <span class="badge badge-danger">Overdue</span>
                                                <?php elseif($shipment->invoice->status == 'sent'): ?>
                                                    <span class="badge badge-info"><?php echo e(ucfirst($shipment->invoice->status)); ?></span>
                                                <?php else: ?>
                                                    <span class="badge badge-secondary"><?php echo e(ucfirst($shipment->invoice->status)); ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="<?php echo e(route('admin.invoices.show', $shipment->invoice->id)); ?>" class="btn btn-sm btn-info" title="View Invoice">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create payments')): ?>
                                                <?php if($shipment->invoice->status != 'paid'): ?>
                                                    <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#paymentModal<?php echo e($shipment->invoice->id); ?>" title="Record Payment">
                                                        <i class="fas fa-money-bill"></i>
                                                    </button>
                                                <?php endif; ?>
                                                <?php endif; ?>
                                                <a href="<?php echo e(route('admin.invoices.pdf', $shipment->invoice->id)); ?>" class="btn btn-sm btn-danger" title="Download PDF" target="_blank">
                                                    <i class="fas fa-file-pdf"></i>
                                                </a>
                                            </td>
                                        </tr>

                                        <!-- Payment Modal for this invoice -->
                                        <div class="modal fade" id="paymentModal<?php echo e($shipment->invoice->id); ?>" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-success">
                                                        <h5 class="modal-title">Record Payment - <?php echo e($shipment->invoice->invoice_number); ?></h5>
                                                        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                                                    </div>
                                                    <form action="<?php echo e(route('admin.invoices.payments.store', $shipment->invoice->id)); ?>" method="POST">
                                                        <?php echo csrf_field(); ?>
                                                        <input type="hidden" name="invoice_id" value="<?php echo e($shipment->invoice->id); ?>">
                                                        <div class="modal-body">
                                                            <div class="alert alert-info">
                                                                <strong>Balance Due:</strong> <?php echo e($invCurrency); ?> <?php echo e(number_format($shipment->invoice->balance, 0)); ?>

                                                            </div>
                                                            <div class="form-group">
                                                                <label>Amount <span class="text-danger">*</span></label>
                                                                <input type="number" step="0.01" name="amount" class="form-control" value="<?php echo e($shipment->invoice->balance > 0 ? $shipment->invoice->balance : ''); ?>" max="<?php echo e($shipment->invoice->balance); ?>" required>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Payment Date <span class="text-danger">*</span></label>
                                                                <input type="date" name="payment_date" class="form-control" value="<?php echo e(date('Y-m-d')); ?>" required>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Payment Method <span class="text-danger">*</span></label>
                                                                <select name="payment_method" class="form-control" required>
                                                                    <option value="cash">Cash</option>
                                                                    <option value="card">Card</option>
                                                                    <option value="bank_transfer">Bank Transfer</option>
                                                                    <option value="mobile_money">Mobile Money</option>
                                                                </select>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Reference Number</label>
                                                                <input type="text" name="reference_number" class="form-control">
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Notes</label>
                                                                <textarea name="notes" class="form-control" rows="2"></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-success">
                                                                <i class="fas fa-check"></i> Record Payment
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="7" class="text-center">No invoices found for this batch</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                            <?php if($totalInvoiced > 0): ?>
                                <tfoot class="bg-light">
                                    <tr>
                                        <th colspan="2" class="text-right">TOTALS:</th>
                                        <?php $totalCurrencySymbol = \App\Models\Setting::getCurrencySymbol(); ?>
                                        <th><?php echo e($totalCurrencySymbol); ?> <?php echo e(number_format($totalInvoiced, 0)); ?></th>
                                        <th class="text-success"><?php echo e($totalCurrencySymbol); ?> <?php echo e(number_format($totalPaid, 0)); ?></th>
                                        <th class="text-danger"><?php echo e($totalCurrencySymbol); ?> <?php echo e(number_format($totalOutstanding, 0)); ?></th>
                                        <th colspan="2"></th>
                                    </tr>
                                </tfoot>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Shipment Modal -->
    <div class="modal fade" id="addShipmentModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Shipment to Batch</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form action="<?php echo e(route('admin.batches.add-shipment', $batch)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="shipment_id">Select Shipment</label>
                            <select name="shipment_id" id="shipment_id" class="form-control" required>
                                <option value="">-- Select a shipment --</option>
                                <?php $__currentLoopData = $availableShipments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $shipment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($shipment->id); ?>">
                                        <?php echo e($shipment->tracking_number); ?> - <?php echo e($shipment->client->name); ?> (<?php echo e($shipment->origin); ?> → <?php echo e($shipment->destination); ?>)
                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <?php if($availableShipments->isEmpty()): ?>
                            <div class="alert alert-info">
                                No available shipments. All shipments are already in batches.
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" <?php echo e($availableShipments->isEmpty() ? 'disabled' : ''); ?>>
                            <i class="fas fa-plus"></i> Add to Batch
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Batch Financial Summary -->
    <?php
        $expenseTypes = [
            'shipping_china_mombasa' => 'Shipping from China – Mombasa',
            'transport_mombasa_kla' => 'Transport from Mombasa to KLA',
            'verification_fees' => 'Verification Fees',
            'demurrage' => 'Demurrage Charges',
            'other' => 'Other Expense',
        ];
    ?>

    <!-- Container Expenses -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-receipt"></i> Container Expenses</h3>
            <div class="card-tools">
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit batches')): ?>
                <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#addExpenseModal">
                    <i class="fas fa-plus"></i> Add Expense
                </button>
                <?php endif; ?>
            </div>
        </div>
        <div class="card-body p-0">
            <table class="table table-sm table-striped">
                <thead>
                    <tr>
                        <th>Expense Type</th>
                        <th>Amount</th>
                        <th>Description</th>
                        <th>Entered By</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $batch->expenses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $expense): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><strong><?php echo e($expenseTypes[$expense->type] ?? ucfirst(str_replace('_', ' ', $expense->type))); ?></strong></td>
                            <td><?php if($expense->currency === 'USD'): ?>$<?php echo e(number_format($expense->amount, 0)); ?><?php else: ?> UGX <?php echo e(number_format($expense->amount, 0)); ?><?php endif; ?></td>
                            <td><small><?php echo e($expense->description ?? ''); ?></small></td>
                            <td><small><?php echo e($expense->creator->name ?? 'N/A'); ?></small></td>
                            <td>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete batches')): ?>
                                <form action="<?php echo e(route('admin.batches.expenses.destroy', [$batch, $expense])); ?>" method="POST" style="display:inline;" class="delete-form">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-sm btn-danger" title="Remove Expense"><i class="fas fa-trash"></i></button>
                                </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-3">No container expenses recorded yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Expense Modal -->
    <div class="modal fade" id="addExpenseModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="<?php echo e(route('admin.batches.expenses.store', $batch)); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="modal-header">
                        <h5 class="modal-title">Add Container Expense</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Expense Type <span class="text-danger">*</span></label>
                            <select name="type" class="form-control" required>
                                <option value="">Select Type</option>
                                <?php $__currentLoopData = $expenseTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($key); ?>"><?php echo e($label); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Amount <span class="text-danger">*</span></label>
                                    <input type="number" name="amount" class="form-control" step="0.01" min="0" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Currency <span class="text-danger">*</span></label>
                                    <select name="currency" class="form-control" required>
                                        <option value="UGX">UGX</option>
                                        <option value="USD">USD</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Description (Optional)</label>
                            <textarea name="description" class="form-control" rows="2" placeholder="Additional details..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Expense</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('footer'); ?>
    <strong>Copyright &copy; <?php echo e(date('Y')); ?> <a href="#">Eagle Cargo Freights</a>.</strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
        <b>Support Call</b> +256 200 991 118
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(function() {
        // Prepare shipment data for validation
        <?php
        $shipmentData = [];
        $firstInvoiceUrl = '';
        $totalBalance = 0;
        foreach ($batch->shipments as $shipment) {
            $invoice = $shipment->invoices->first() ?? $shipment->invoice;
            $hasStorageFee = false;
            if ($invoice && $invoice->items) {
                foreach ($invoice->items as $item) {
                    if (strtolower(trim($item->description)) === 'storage fee') {
                        $hasStorageFee = true;
                        break;
                    }
                }
            }
            $shipmentData[] = [
                'id' => $shipment->id,
                'tracking' => $shipment->tracking_number,
                'balance' => $invoice ? floatval($invoice->balance) : 0,
                'hasStorageFee' => $hasStorageFee,
            ];
            $totalBalance += $invoice ? floatval($invoice->balance) : 0;
            if (!$firstInvoiceUrl && $invoice) {
                $firstInvoiceUrl = route('admin.invoices.show', $invoice);
            }
        }
        ?>
        
        var shipmentData = <?php echo json_encode($shipmentData, 15, 512) ?>;
        var totalBalance = <?php echo e($totalBalance); ?>;
        var currency = "<?php echo e(\App\Models\Setting::getCurrencySymbol()); ?>";
        var invoiceUrl = "<?php echo e($firstInvoiceUrl); ?>";
        var batchShipmentCount = <?php echo e($batch->shipments->count()); ?>;

        console.log('Total Balance:', totalBalance);
        console.log('Shipment Data:', shipmentData);

        // Show/hide storage fee field based on status selection
        $('#current_status').on('change', function() {
            if ($(this).val() === 'Picked Up') {
                $('#storage-fee-group').slideDown();
            } else {
                $('#storage-fee-group').slideUp();
                $('#storage_fee').val('');
            }
        });

        // Form submission with validation
        $('#batch-status-form').on('submit', function(e) {
            var status = $('#current_status').val();
            var storageFee = $('#storage_fee').val();
            
            if (status === 'Picked Up') {
                e.preventDefault();
                
                // Check if any shipment has balance
                var hasBalance = false;
                var balanceShipments = [];
                var noFeeShipments = [];
                
                for (var i = 0; i < shipmentData.length; i++) {
                    if (shipmentData[i].balance > 0) {
                        hasBalance = true;
                        balanceShipments.push(shipmentData[i].tracking + ' (' + currency + ' ' + shipmentData[i].balance + ')');
                    }
                    
                    if (!shipmentData[i].hasStorageFee) {
                        noFeeShipments.push(shipmentData[i].tracking);
                    }
                }
                
                // If has balance, redirect to invoice
                if (hasBalance) {
                    Swal.fire({
                        title: 'Outstanding Balance',
                        html: 'The following shipments have outstanding balances:<br><strong>' + balanceShipments.join(', ') + '</strong><br><br>Please go to the invoice and make a payment first.',
                        icon: 'error',
                        confirmButtonText: 'Go to Invoice'
                    }).then((result) => {
                        if (result.isConfirmed && invoiceUrl) {
                            window.location.href = invoiceUrl;
                        }
                    });
                    return;
                }
                
                // If no storage fee entered and some shipments don't have it
                if (storageFee === '' && noFeeShipments.length > 0) {
                    Swal.fire({
                        title: 'Storage Fee Required',
                        text: 'The following shipments need a storage fee: ' + noFeeShipments.join(', ') + '. Please enter a storage fee amount (enter 0 if no fee).',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    });
                    return;
                }
                
                // All checks passed - confirm and submit
                Swal.fire({
                    title: 'Confirm Status Update',
                    text: 'This will update the status of ALL ' + batchShipmentCount + ' shipments in this batch to "Picked Up". Continue?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Update',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#batch-status-form')[0].submit();
                    }
                });
            }
        });
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::page', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\eagle2\resources\views/batches/show.blade.php ENDPATH**/ ?>