@extends('adminlte::page')

@section('title', 'Sea Cargo Shipments')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1><i class="fas fa-ship"></i> Sea Cargo Shipments</h1>
        </div>
        <div class="col-sm-6">
            @can('create sea cargo')
            <a href="{{ route('admin.sea-cargo.create') }}" class="btn btn-primary float-right">
                <i class="fas fa-plus"></i> Create Sea Shipment
            </a>
            @endcan
        </div>
    </div>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Sea Shipments List</h3>
        </div>
        <div class="card-body">
            <!-- Filters -->
            <form action="{{ route('admin.sea-cargo.index') }}" method="GET" class="mb-3">
                <div class="row">
                    <div class="col-md-5">
                        <input type="text" name="search" class="form-control" placeholder="Search by tracking number or client" value="{{ request('search') }}">
                    </div>
                    <div class="col-md-4">
                        <select name="status" class="form-control">
                            <option value="">All Statuses</option>
                            <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="Picked Up" {{ request('status') == 'Picked Up' ? 'selected' : '' }}>Picked Up</option>
                            <option value="In Transit" {{ request('status') == 'In Transit' ? 'selected' : '' }}>In Transit</option>
                            <option value="Delivered" {{ request('status') == 'Delivered' ? 'selected' : '' }}>Delivered</option>
                        </select>
                    </div>
                    <div class="col-md-3">
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
                            <th>Tracking #</th>
                            <th>Client</th>
                            <th>Route</th>
                            <th>Delivery Time</th>
                            <th>Status</th>
                            <th>Batch</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($shipments as $shipment)
                            <tr>
                                <td><strong>{{ $shipment->tracking_number }}</strong></td>
                                <td>{{ $shipment->client->name }}</td>
                                <td>{{ $shipment->origin }} → {{ $shipment->destination }}</td>
                                <td>
                                    @php
                                        $deliveryRange = $shipment->delivery_range;
                                        if (!$deliveryRange && $shipment->delivery_time_min && $shipment->delivery_time_max) {
                                            $deliveryRange = $shipment->delivery_time_min . '-' . $shipment->delivery_time_max . ' ' . ($shipment->delivery_time_unit ?? 'days');
                                        }
                                    @endphp
                                    <span class="badge badge-info">{{ $deliveryRange ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    @php
                                        $statusBadge = match($shipment->current_status) {
                                            'Pending'              => 'badge-secondary',
                                            'Picked Up'            => 'badge-info',
                                            'In Transit'           => 'badge-primary',
                                            'Arrived at Facility'  => 'badge-indigo',
                                            'Out for Delivery'     => 'badge-cyan',
                                            'Delivered'            => 'badge-success',
                                            'On Hold'              => 'badge-warning',
                                            'Cancelled'            => 'badge-danger',
                                            default                => 'badge-secondary',
                                        };
                                    @endphp
                                    <span class="badge {{ $statusBadge }}">{{ $shipment->current_status }}</span>
                                </td>
                                <td>
                                    @if($shipment->batch)
                                        <span class="badge badge-primary">{{ $shipment->batch->batch_number }}</span>
                                    @else
                                        <span class="text-muted">No batch</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.sea-cargo.show', $shipment) }}" class="btn btn-sm btn-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @can('edit sea cargo')
                                    <a href="{{ route('admin.sea-cargo.edit', $shipment) }}" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No sea cargo shipments found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer clearfix">
            {{ $shipments->appends(request()->query())->links('pagination::bootstrap-4') }}
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
