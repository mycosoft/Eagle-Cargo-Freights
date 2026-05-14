<?php $__env->startSection('title', 'Batches'); ?>

<?php $__env->startSection('content_header'); ?>
    <div class="row">
        <div class="col-sm-6">
            <h1>Shipment Batches</h1>
        </div>
        <div class="col-sm-6">
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create batches')): ?>
            <a href="<?php echo e(route('admin.batches.create')); ?>" class="btn btn-primary float-right">
                <i class="fas fa-plus"></i> Create Batch
            </a>
            <?php endif; ?>
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

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Batch List</h3>
        </div>
        <div class="card-body">
            <!-- Filters -->
            <form action="<?php echo e(route('admin.batches.index')); ?>" method="GET" class="mb-3">
                <div class="row">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" placeholder="Search by batch number or name" value="<?php echo e(request('search')); ?>">
                    </div>
                    <div class="col-md-4">
                        <select name="status" class="form-control">
                            <option value="">All Statuses</option>
                            <option value="pending" <?php echo e(request('status') == 'pending' ? 'selected' : ''); ?>>Pending</option>
                            <option value="processing" <?php echo e(request('status') == 'processing' ? 'selected' : ''); ?>>Processing</option>
                            <option value="in_transit" <?php echo e(request('status') == 'in_transit' ? 'selected' : ''); ?>>In Transit</option>
                            <option value="delivered" <?php echo e(request('status') == 'delivered' ? 'selected' : ''); ?>>Delivered</option>
                            <option value="cancelled" <?php echo e(request('status') == 'cancelled' ? 'selected' : ''); ?>>Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Batch Number</th>
                            <th>Name</th>
                            <th>Shipments</th>
                            <th>Status</th>
                            <th>Created By</th>
                            <th>Created Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $batches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $batch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><strong><?php echo e($batch->batch_number); ?></strong></td>
                                <td><?php echo e($batch->name); ?></td>
                                <td><span class="badge badge-info"><?php echo e($batch->shipments->count()); ?></span></td>
                                <td>
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
                                </td>
                                <td><?php echo e($batch->creator->name ?? 'N/A'); ?></td>
                                <td><?php echo e($batch->created_at->format('M d, Y')); ?></td>
                                <td>
                                    <a href="<?php echo e(route('admin.batches.show', $batch)); ?>" class="btn btn-sm btn-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?php echo e(route('admin.batches.packing-list', $batch)); ?>" class="btn btn-sm btn-success" title="Packing List">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit batches')): ?>
                                    <a href="<?php echo e(route('admin.batches.edit', $batch)); ?>" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <?php endif; ?>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete batches')): ?>
                                    <form action="<?php echo e(route('admin.batches.destroy', $batch)); ?>" method="POST" style="display:inline-block;" class="delete-form">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="7" class="text-center">No batches found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer clearfix">
            <?php echo e($batches->appends(request()->query())->links('pagination::bootstrap-4')); ?>

        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.delete-form').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Delete this batch?',
                text: "Shipments will not be deleted, only unlinked from the batch.",
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
<?php $__env->stopSection(); ?>

<?php $__env->startSection('footer'); ?>
    <strong>Copyright &copy; <?php echo e(date('Y')); ?> <a href="#">Eagle Cargo Freights</a>.</strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
        <b>Support Call</b> +256 200 991 118
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::page', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\eagle2\resources\views/batches/index.blade.php ENDPATH**/ ?>