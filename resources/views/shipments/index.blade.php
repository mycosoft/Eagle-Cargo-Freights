@extends('adminlte::page')

@section('title', 'Shipments')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>Shipments</h1>
        </div>
        <div class="col-sm-6">
            @can('create shipments')
            <a href="{{ route('admin.shipments.create') }}" class="btn btn-primary float-right">
                <i class="fas fa-plus"></i> Create Shipment
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
            <h3 class="card-title">Shipment List</h3>
        </div>
        <div class="card-body">
            <!-- Filters -->
            <form action="{{ route('admin.shipments.index') }}" method="GET" class="mb-3">
                <div class="row">
                    <div class="col-md-3">
                        <input type="text" name="tracking_number" class="form-control" placeholder="Tracking Number" value="{{ request('tracking_number') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="client_id" class="form-control">
                            <option value="">All Clients</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                                    {{ $client->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-control">
                            <option value="">All Statuses</option>
                            <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="Picked Up" {{ request('status') == 'Picked Up' ? 'selected' : '' }}>Picked Up</option>
                            <option value="In Transit" {{ request('status') == 'In Transit' ? 'selected' : '' }}>In Transit</option>
                            <option value="Arrived at Facility" {{ request('status') == 'Arrived at Facility' ? 'selected' : '' }}>Arrived at Facility</option>
                            <option value="Out for Delivery" {{ request('status') == 'Out for Delivery' ? 'selected' : '' }}>Out for Delivery</option>
                            <option value="Delivered" {{ request('status') == 'Delivered' ? 'selected' : '' }}>Delivered</option>
                            <option value="On Hold" {{ request('status') == 'On Hold' ? 'selected' : '' }}>On Hold</option>
                            <option value="Cancelled" {{ request('status') == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
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
                            <th>Type</th>
                            <th>Status</th>
                            <th>Expected Delivery</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($shipments as $shipment)
                            <tr>
                                <td><strong>{{ $shipment->tracking_number }}</strong></td>
                                <td>{{ $shipment->client->name }}</td>
                                <td>{{ $shipment->origin }} → {{ $shipment->destination }}</td>
                                <td><span class="badge badge-secondary">{{ ucfirst($shipment->shipment_type) }}</span></td>
                                <td>
                                    @php
                                        $statusColors = [
                                            'Pending' => 'warning',
                                            'Picked Up' => 'info',
                                            'In Transit' => 'primary',
                                            'Arrived at Facility' => 'secondary',
                                            'Out for Delivery' => 'info',
                                            'Delivered' => 'success',
                                            'On Hold' => 'dark',
                                            'Cancelled' => 'danger'
                                        ];
                                        $badgeClass = $statusColors[$shipment->current_status] ?? 'secondary';
                                    @endphp
                                    <span class="badge badge-{{ $badgeClass }}">{{ $shipment->current_status }}</span>
                                </td>
                                <td>{{ $shipment->expected_delivery_date ? $shipment->expected_delivery_date->format('M d, Y') : 'N/A' }}</td>
                                <td>
                                    <a href="{{ route('admin.shipments.show', $shipment) }}" class="btn btn-sm btn-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @can('edit shipments')
                                    <a href="{{ route('admin.shipments.edit', $shipment) }}" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endcan
                                    @can('delete shipments')
                                    <form action="{{ route('admin.shipments.destroy', $shipment) }}" method="POST" style="display:inline-block;" class="delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No shipments found.</td>
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

