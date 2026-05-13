@extends('adminlte::page')

@section('title', 'Activity Logs')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>Activity Logs</h1>
        </div>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">System Activity Log</h3>
            <div class="card-tools">
                <form action="{{ route('admin.activity-logs.index') }}" method="GET">
                    <div class="input-group input-group-sm">
                        <input type="text" name="search" class="form-control" placeholder="Search logs..." value="{{ request('search') }}">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>Date & Time</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Model</th>
                        <th>Description</th>
                        <th>IP Address</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td>{{ $log->created_at->format('M d, Y H:i:s') }}</td>
                            <td>{{ $log->user->name ?? 'System' }}</td>
                            <td>
                                @if($log->action === 'created')
                                    <span class="badge badge-success">{{ ucfirst($log->action) }}</span>
                                @elseif($log->action === 'updated')
                                    <span class="badge badge-warning">{{ ucfirst($log->action) }}</span>
                                @elseif($log->action === 'deleted')
                                    <span class="badge badge-danger">{{ ucfirst($log->action) }}</span>
                                @else
                                    <span class="badge badge-secondary">{{ ucfirst($log->action) }}</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $modelLabel = App\Models\ActivityLog::getModelLabel($log->model_type);
                                @endphp
                                {{ $modelLabel }}
                            </td>
                            <td>{{ $log->description }}</td>
                            <td>{{ $log->ip_address ?? 'N/A' }}</td>
                            <td>
                                <a href="{{ route('admin.activity-logs.show', $log) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No activity logs found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer clearfix">
            {{ $logs->withQueryString()->links('pagination::bootstrap-4') }}
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