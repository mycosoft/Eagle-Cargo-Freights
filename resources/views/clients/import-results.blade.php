@extends('adminlte::page')

@section('title', 'Import Results')

@section('content_header')
    <h1>Client Import Results</h1>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {{ session('success') }}
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Import Summary</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="info-box bg-success">
                                <span class="info-box-icon"><i class="fas fa-check"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Successfully Imported</span>
                                    <span class="info-box-number">{{ $imported }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box bg-warning">
                                <span class="info-box-icon"><i class="fas fa-exclamation-triangle"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Skipped/Failed</span>
                                    <span class="info-box-number">{{ $skipped }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box bg-info">
                                <span class="info-box-icon"><i class="fas fa-file-csv"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Processed</span>
                                    <span class="info-box-number">{{ $imported + $skipped }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($skipped > 0 && count($errors) > 0)
                        <div class="alert alert-warning">
                            <h5><i class="icon fas fa-exclamation-triangle"></i> Errors and Skipped Rows:</h5>
                            <ul class="mb-0">
                                @foreach($errors as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if(count($skippedRows) > 0)
                        <h5 class="mt-4">Detailed Error Information</h5>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead>
                                    <tr>
                                        <th>Row</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Errors</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($skippedRows as $row)
                                        <tr>
                                            <td>{{ $row['row'] }}</td>
                                            <td>{{ $row['data']['name'] ?? 'N/A' }}</td>
                                            <td>{{ $row['data']['email'] ?? 'N/A' }}</td>
                                            <td>{{ $row['data']['phone'] ?? 'N/A' }}</td>
                                            <td>
                                                <ul class="mb-0 pl-3">
                                                    @foreach($row['errors'] as $error)
                                                        <li class="text-danger">{{ $error }}</li>
                                                    @endforeach
                                                </ul>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                <div class="card-footer">
                    <a href="{{ route('admin.clients.index') }}" class="btn btn-primary">
                        <i class="fas fa-users"></i> View All Clients
                    </a>
                    <a href="{{ route('admin.clients.import') }}" class="btn btn-secondary">
                        <i class="fas fa-upload"></i> Import More Clients
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
