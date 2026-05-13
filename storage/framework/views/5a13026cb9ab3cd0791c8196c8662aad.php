<?php $__env->startSection('title', 'Sea Cargo Dashboard'); ?>

<?php $__env->startSection('content_header'); ?>
    <div class="row">
        <div class="col-sm-6">
            <h1><i class="fas fa-ship mr-2"></i>Sea Cargo Dashboard</h1>
        </div>
        <div class="col-sm-6">
            <a href="<?php echo e(route('admin.sea-cargo.create')); ?>" class="btn btn-primary float-right">
                <i class="fas fa-plus"></i> New Sea Shipment
            </a>
            <a href="<?php echo e(route('admin.sea-cargo.index')); ?>" class="btn btn-info float-right mr-2">
                <i class="fas fa-list"></i> All Sea Shipments
            </a>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3><?php echo e($totalShipments); ?></h3>
                    <p>Total Sea Shipments</p>
                </div>
                <div class="icon"><i class="fas fa-ship"></i></div>
                <a href="<?php echo e(route('admin.sea-cargo.index')); ?>" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3><?php echo e($inTransit); ?></h3>
                    <p>In Transit</p>
                </div>
                <div class="icon"><i class="fas fa-truck"></i></div>
                <a href="<?php echo e(route('admin.sea-cargo.index', ['status' => 'In Transit'])); ?>" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3><?php echo e($delivered); ?></h3>
                    <p>Delivered</p>
                </div>
                <div class="icon"><i class="fas fa-check-circle"></i></div>
                <a href="<?php echo e(route('admin.sea-cargo.index', ['status' => 'Delivered'])); ?>" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3><?php echo e($thisMonth); ?></h3>
                    <p>This Month</p>
                </div>
                <div class="icon"><i class="fas fa-calendar-alt"></i></div>
                <a href="<?php echo e(route('admin.sea-cargo.index')); ?>" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3><?php echo e(\App\Models\Setting::getCurrencySymbol(null)); ?> <?php echo e(number_format($revenueUgx, 0)); ?></h3>
                    <p>Revenue (UGX)</p>
                </div>
                <div class="icon"><i class="fas fa-money-bill-wave"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>$<?php echo e(number_format($revenueUsd, 0)); ?></h3>
                    <p>Revenue (USD)</p>
                </div>
                <div class="icon"><i class="fas fa-dollar-sign"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3><?php echo e(\App\Models\Setting::getCurrencySymbol(null)); ?> <?php echo e(number_format($totalInvoiced, 0)); ?></h3>
                    <p>Total Invoiced</p>
                </div>
                <div class="icon"><i class="fas fa-file-invoice"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3><?php echo e(\App\Models\Setting::getCurrencySymbol(null)); ?> <?php echo e(number_format($outstanding, 0)); ?></h3>
                    <p>Outstanding</p>
                </div>
                <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-chart-pie"></i> Shipments by Status</h3>
                </div>
                <div class="card-body">
                    <canvas id="seaPieChart" style="height: 250px;"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-chart-line"></i> Sea Shipments Trend</h3>
                </div>
                <div class="card-body">
                    <canvas id="seaTrendChart" style="height: 250px;"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-clock"></i> Recent Sea Shipments</h3>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <?php $__empty_1 = true; $__currentLoopData = $recentShipments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <li class="list-group-item">
                                <a href="<?php echo e(route('admin.sea-cargo.show', $s)); ?>">
                                    <strong><?php echo e($s->tracking_number); ?></strong>
                                </a>
                                <br><small class="text-muted"><?php echo e($s->client->name ?? 'N/A'); ?> - <?php echo e($s->current_status); ?></small>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <li class="list-group-item text-muted text-center">No shipments yet</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-credit-card"></i> Recent Payments (Sea Cargo)</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Receipt #</th>
                                <th>Invoice</th>
                                <th>Client</th>
                                <th>Amount</th>
                                <th>Date</th>
                                <th>Method</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $recentPayments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><strong><?php echo e($p->receipt_number); ?></strong></td>
                                    <td><?php echo e($p->invoice->invoice_number ?? 'N/A'); ?></td>
                                    <td><?php echo e($p->invoice->shipment->client->name ?? 'N/A'); ?></td>
                                    <td><?php echo e(\App\Models\Setting::getCurrencySymbol($p->invoice->shipment->currency ?? null)); ?> <?php echo e(number_format($p->amount, 0)); ?></td>
                                    <td><?php echo e($p->payment_date->format('d M Y')); ?></td>
                                    <td><span class="badge badge-secondary"><?php echo e(ucfirst(str_replace('_', ' ', $p->payment_method))); ?></span></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr><td colspan="6" class="text-center text-muted">No payments yet</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Quick Actions</h3></div>
                <div class="card-body">
                    <a href="<?php echo e(route('admin.sea-cargo.create')); ?>" class="btn btn-primary"><i class="fas fa-plus"></i> New Sea Shipment</a>
                    <a href="<?php echo e(route('admin.sea-cargo.index')); ?>" class="btn btn-info"><i class="fas fa-list"></i> All Sea Shipments</a>
                    <a href="<?php echo e(url('admin/invoices')); ?>" class="btn btn-success"><i class="fas fa-file-invoice"></i> Invoices</a>
                    <a href="<?php echo e(url('admin/batches')); ?>" class="btn btn-warning"><i class="fas fa-layer-group"></i> Batches</a>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('seaPieChart'), {
    type: 'pie',
    data: {
        labels: ['Pending', 'In Transit', 'Delivered', 'On Hold'],
        datasets: [{
            data: [<?php echo e($pending); ?>, <?php echo e($inTransit); ?>, <?php echo e($delivered); ?>, <?php echo e($onHold); ?>],
            backgroundColor: ['#ffc107', '#007bff', '#28a745', '#6c757d']
        }]
    },
    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
});

new Chart(document.getElementById('seaTrendChart'), {
    type: 'line',
    data: {
        labels: <?php echo json_encode(array_keys($monthlyData)); ?>,
        datasets: [{
            label: 'Sea Shipments',
            data: <?php echo json_encode(array_values($monthlyData)); ?>,
            backgroundColor: 'rgba(23, 162, 184, 0.2)',
            borderColor: 'rgba(23, 162, 184, 1)',
            borderWidth: 2,
            fill: true
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
    }
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
<?php echo $__env->make('adminlte::page', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\eagle2\resources\views/sea-cargo/dashboard.blade.php ENDPATH**/ ?>