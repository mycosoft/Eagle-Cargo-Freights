@extends('adminlte::page')

@section('title', 'Reports')

@section('content_header')
    <h1>Reports & Analytics</h1>
@stop

@section('content')
    <div class="row">
        <!-- Revenue Report -->
        <div class="col-lg-4 col-md-6">
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    <div class="text-center">
                        <i class="fas fa-chart-line fa-4x text-primary mb-3"></i>
                    </div>
                    <h3 class="profile-username text-center">Revenue Report</h3>
                    <p class="text-muted text-center">View detailed revenue and payment history</p>
                    <a href="{{ route('admin.reports.revenue') }}" class="btn btn-primary btn-block">
                        <i class="fas fa-eye"></i> View Report
                    </a>
                </div>
            </div>
        </div>

        <!-- Outstanding Invoices -->
        <div class="col-lg-4 col-md-6">
            <div class="card card-warning card-outline">
                <div class="card-body box-profile">
                    <div class="text-center">
                        <i class="fas fa-exclamation-circle fa-4x text-warning mb-3"></i>
                    </div>
                    <h3 class="profile-username text-center">Outstanding Invoices</h3>
                    <p class="text-muted text-center">Track unpaid and pending invoices</p>
                    <a href="{{ route('admin.reports.outstanding') }}" class="btn btn-warning btn-block">
                        <i class="fas fa-eye"></i> View Report
                    </a>
                </div>
            </div>
        </div>

        <!-- Payment History -->
        <div class="col-lg-4 col-md-6">
            <div class="card card-success card-outline">
                <div class="card-body box-profile">
                    <div class="text-center">
                        <i class="fas fa-money-bill-wave fa-4x text-success mb-3"></i>
                    </div>
                    <h3 class="profile-username text-center">Payment History</h3>
                    <p class="text-muted text-center">Complete payment transaction history</p>
                    <a href="{{ route('admin.reports.payments') }}" class="btn btn-success btn-block">
                        <i class="fas fa-eye"></i> View Report
                    </a>
                </div>
            </div>
        </div>

        <!-- Shipment Report -->
        <div class="col-lg-4 col-md-6">
            <div class="card card-info card-outline">
                <div class="card-body box-profile">
                    <div class="text-center">
                        <i class="fas fa-shipping-fast fa-4x text-info mb-3"></i>
                    </div>
                    <h3 class="profile-username text-center">Shipment Report</h3>
                    <p class="text-muted text-center">View all shipments with filters and analytics</p>
                    <a href="{{ route('admin.reports.shipments') }}" class="btn btn-info btn-block">
                        <i class="fas fa-eye"></i> View Report
                    </a>
                </div>
            </div>
        </div>

        <!-- Batch Revenue Report -->
        <div class="col-lg-4 col-md-6">
            <div class="card card-secondary card-outline">
                <div class="card-body box-profile">
                    <div class="text-center">
                        <i class="fas fa-boxes fa-4x text-secondary mb-3"></i>
                    </div>
                    <h3 class="profile-username text-center">Batch Revenue</h3>
                    <p class="text-muted text-center">Revenue collected per shipment batch</p>
                    <a href="{{ route('admin.reports.batch-revenue') }}" class="btn btn-secondary btn-block">
                        <i class="fas fa-eye"></i> View Report
                    </a>
                </div>
            </div>
        </div>

        <!-- Expenses Report -->
        <div class="col-lg-4 col-md-6">
            <div class="card card-danger card-outline">
                <div class="card-body box-profile">
                    <div class="text-center">
                        <i class="fas fa-receipt fa-4x text-danger mb-3"></i>
                    </div>
                    <h3 class="profile-username text-center">Expenses</h3>
                    <p class="text-muted text-center">Track business expenses by category</p>
                    <a href="{{ route('admin.reports.expenses') }}" class="btn btn-danger btn-block">
                        <i class="fas fa-eye"></i> View Report
                    </a>
                </div>
            </div>
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
