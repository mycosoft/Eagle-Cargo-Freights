@extends('adminlte::page')

@section('title', 'Import Clients')

@section('content_header')
    <h1>Import Clients from CSV</h1>
@stop

@section('content')
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {{ session('error') }}
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Upload CSV File</h3>
                </div>
                <form action="{{ route('admin.clients.import.process') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="csv_file">Select CSV File <span class="text-danger">*</span></label>
                            <div class="custom-file">
                                <input type="file" name="csv_file" id="csv_file" class="custom-file-input @error('csv_file') is-invalid @enderror" accept=".csv,.txt" required>
                                <label class="custom-file-label" for="csv_file">Choose file...</label>
                            </div>
                            @error('csv_file')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">Maximum file size: 2MB. Accepted formats: CSV, TXT</small>
                        </div>

                        <div class="alert alert-info">
                            <h5><i class="icon fas fa-info-circle"></i> CSV Format Requirements:</h5>
                            <p>Your CSV file must have the following columns in this exact order:</p>
                            <ol class="mb-0">
                                <li><strong>name</strong> - Client's full name (required)</li>
                                <li><strong>email</strong> - Client's email address (required, must be unique)</li>
                                <li><strong>phone</strong> - Client's phone number (required)</li>
                                <li><strong>company</strong> - Company name (optional)</li>
                                <li><strong>address</strong> - Full address (optional)</li>
                            </ol>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload"></i> Import Clients
                        </button>
                        <a href="{{ route('admin.clients.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">Download Template</h3>
                </div>
                <div class="card-body">
                    <p>Download a sample CSV template with example data to help you format your import file correctly.</p>
                    <a href="{{ route('admin.clients.import.template') }}" class="btn btn-success btn-block">
                        <i class="fas fa-download"></i> Download CSV Template
                    </a>
                </div>
            </div>

            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">Important Notes</h3>
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        <li>Duplicate email addresses will be skipped</li>
                        <li>Invalid data will be reported after import</li>
                        <li>Successfully imported clients will be added to the database</li>
                        <li>The import process may take a moment for large files</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
    // Update file input label with selected filename
    document.getElementById('csv_file').addEventListener('change', function(e) {
        const fileName = e.target.files[0]?.name || 'Choose file...';
        const label = e.target.nextElementSibling;
        label.textContent = fileName;
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
