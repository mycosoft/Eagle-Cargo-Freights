<?php $__env->startSection('title', 'Expenses'); ?>

<?php $__env->startSection('content_header'); ?>
    <h1>Expenses Management</h1>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">All Expenses</h3>
            <div class="card-tools">
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create expenses')): ?>
                <a href="<?php echo e(route('admin.expenses.create')); ?>" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus"></i> New Expense
                </a>
                <?php endif; ?>
                <button type="button" class="btn btn-sm btn-info" data-toggle="collapse" data-target="#filterCollapse">
                    <i class="fas fa-filter"></i> Filters
                </button>
            </div>
        </div>
        <div class="card-body">
            <!-- Filters -->
            <div class="collapse mb-3" id="filterCollapse">
                <form method="GET" action="<?php echo e(route('admin.expenses.index')); ?>">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="">All Statuses</option>
                                    <option value="pending" <?php echo e(request('status') == 'pending' ? 'selected' : ''); ?>>Pending</option>
                                    <option value="approved" <?php echo e(request('status') == 'approved' ? 'selected' : ''); ?>>Approved</option>
                                    <option value="rejected" <?php echo e(request('status') == 'rejected' ? 'selected' : ''); ?>>Rejected</option>
                                    <option value="paid" <?php echo e(request('status') == 'paid' ? 'selected' : ''); ?>>Paid</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Currency</label>
                                <select name="currency" class="form-control">
                                    <option value="">All Currencies</option>
                                    <option value="UGX" <?php echo e(request('currency') == 'UGX' ? 'selected' : ''); ?>>UGX</option>
                                    <option value="USD" <?php echo e(request('currency') == 'USD' ? 'selected' : ''); ?>>USD</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Category</label>
                                <select name="category_id" class="form-control">
                                    <option value="">All Categories</option>
                                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($category->id); ?>" <?php echo e(request('category_id') == $category->id ? 'selected' : ''); ?>>
                                            <?php echo e($category->name); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>From Date</label>
                                <input type="date" name="date_from" class="form-control" value="<?php echo e(request('date_from')); ?>">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>To Date</label>
                                <input type="date" name="date_to" class="form-control" value="<?php echo e(request('date_to')); ?>">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Search</label>
                                <input type="text" name="search" class="form-control" placeholder="Number, Ref, Desc..." value="<?php echo e(request('search')); ?>">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn btn-primary">Apply</button>
                                    <a href="<?php echo e(route('admin.expenses.index')); ?>" class="btn btn-secondary">Clear</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Summary Cards -->
            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="info-box bg-info">
                        <span class="info-box-icon"><i class="fas fa-money-bill-wave"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">UGX Total</span>
                            <span class="info-box-number">UGX <?php echo e(number_format($totalUGX, 0)); ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-box bg-success">
                        <span class="info-box-icon"><i class="fas fa-dollar-sign"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">USD Total</span>
                            <span class="info-box-number">$<?php echo e(number_format($totalUSD, 0)); ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-box bg-warning">
                        <span class="info-box-icon"><i class="fas fa-clock"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Pending</span>
                            <span class="info-box-number"><?php echo e($pendingCount); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Expenses Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Expense #</th>
                            <th>Date</th>
                            <th>Category</th>
                            <th>Description</th>
                            <th>Amount</th>
                            <th>Currency</th>
                            <th>Payment Method</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $expenses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $expense): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td>
                                    <a href="<?php echo e(route('admin.expenses.show', $expense)); ?>">
                                        <?php echo e($expense->expense_number); ?>

                                    </a>
                                </td>
                                <td><?php echo e($expense->expense_date->format('d M Y')); ?></td>
                                <td>
                                    <span class="badge" style="background-color: <?php echo e($expense->category->color); ?>">
                                        <?php echo e($expense->category->name); ?>

                                    </span>
                                </td>
                                <td><?php echo e(Str::limit($expense->description ?? 'N/A', 40)); ?></td>
                                <td>
                                    <?php if($expense->currency === 'USD'): ?>
                                        <strong>$<?php echo e(number_format($expense->amount, 0)); ?></strong>
                                    <?php else: ?>
                                        <strong>UGX <?php echo e(number_format($expense->amount, 0)); ?></strong>
                                    <?php endif; ?>
                                </td>
                                <td><span class="badge badge-secondary"><?php echo e($expense->currency ?? 'UGX'); ?></span></td>
                                <td>
                                    <span class="badge badge-secondary">
                                        <?php echo e(ucfirst(str_replace('_', ' ', $expense->payment_method))); ?>

                                    </span>
                                </td>
                                <td>
                                    <?php if($expense->status == 'pending'): ?>
                                        <span class="badge badge-warning">Pending</span>
                                    <?php elseif($expense->status == 'approved'): ?>
                                        <span class="badge badge-primary">Approved</span>
                                    <?php elseif($expense->status == 'rejected'): ?>
                                        <span class="badge badge-danger">Rejected</span>
                                    <?php elseif($expense->status == 'paid'): ?>
                                        <span class="badge badge-success">Paid</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="<?php echo e(route('admin.expenses.show', $expense)); ?>" class="btn btn-sm btn-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if($expense->canBeEdited()): ?>
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit expenses')): ?>
                                            <a href="<?php echo e(route('admin.expenses.edit', $expense)); ?>" class="btn btn-sm btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        <?php if($expense->canBeApproved()): ?>
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('approve expenses')): ?>
                                            <form action="<?php echo e(route('admin.expenses.approve', $expense)); ?>" method="POST" style="display: inline;">
                                                <?php echo csrf_field(); ?>
                                                <button type="submit" class="btn btn-sm btn-success" title="Approve"
                                                        onclick="event.preventDefault(); if(confirm('Approve this expense?')) this.form.submit();">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="8" class="text-center">No expenses found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                <?php echo e($expenses->links()); ?>

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

<?php echo $__env->make('adminlte::page', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\eagle2\resources\views/expenses/index.blade.php ENDPATH**/ ?>