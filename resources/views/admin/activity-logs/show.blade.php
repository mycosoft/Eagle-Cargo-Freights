@extends('adminlte::page')

@section('title', 'Activity Log Details')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>Activity Log Details</h1>
        </div>
        <div class="col-sm-6">
            <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-secondary float-right">
                <i class="fas fa-arrow-left"></i> Back to Logs
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Log Information</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 150px;">ID</th>
                            <td>{{ $activityLog->id }}</td>
                        </tr>
                        <tr>
                            <th>Date & Time</th>
                            <td>{{ $activityLog->created_at->format('M d, Y H:i:s') }}</td>
                        </tr>
                        <tr>
                            <th>User</th>
                            <td>{{ $activityLog->user->name ?? 'System' }} ({{ $activityLog->user->email ?? 'N/A' }})</td>
                        </tr>
                        <tr>
                            <th>Action</th>
                            <td>
                                @if($activityLog->action === 'created')
                                    <span class="badge badge-success">{{ ucfirst($activityLog->action) }}</span>
                                @elseif($activityLog->action === 'updated')
                                    <span class="badge badge-warning">{{ ucfirst($activityLog->action) }}</span>
                                @elseif($activityLog->action === 'deleted')
                                    <span class="badge badge-danger">{{ ucfirst($activityLog->action) }}</span>
                                @else
                                    <span class="badge badge-secondary">{{ ucfirst($activityLog->action) }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Model Type</th>
                            <td>
                                @php
                                    $modelLabel = App\Models\ActivityLog::getModelLabel($activityLog->model_type);
                                @endphp
                                {{ $modelLabel }}
                            </td>
                        </tr>
                        <tr>
                            <th>Model ID</th>
                            <td>{{ $activityLog->model_id ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>IP Address</th>
                            <td>{{ $activityLog->ip_address ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>User Agent</th>
                            <td>{{ $activityLog->user_agent ?? 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            @if($activityLog->action === 'updated' && $activityLog->old_values)
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Changes</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Field</th>
                                <th>Old Value</th>
                                <th>New Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($activityLog->new_values as $key => $newValue)
                                @if(isset($activityLog->old_values[$key]) && $activityLog->old_values[$key] !== $newValue)
                                    <tr>
                                        <td><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}</strong></td>
                                        <td>
                                            <span class="text-danger">{{ $activityLog->old_values[$key] ?? 'N/A' }}</span>
                                        </td>
                                        <td>
                                            <span class="text-success">{{ $newValue ?? 'N/A' }}</span>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            @if($activityLog->old_values && $activityLog->action !== 'updated')
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Previous Values</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Field</th>
                                <th>Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($activityLog->old_values as $key => $value)
                                <tr>
                                    <td><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}</strong></td>
                                    <td>{{ is_array($value) ? json_encode($value) : ($value ?? 'N/A') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            @if($activityLog->new_values && $activityLog->action !== 'deleted')
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">New Values</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Field</th>
                                <th>Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($activityLog->new_values as $key => $value)
                                <tr>
                                    <td><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}</strong></td>
                                    <td>{{ is_array($value) ? json_encode($value) : ($value ?? 'N/A') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
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