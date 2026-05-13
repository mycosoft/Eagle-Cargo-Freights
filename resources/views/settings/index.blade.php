@extends('adminlte::page')

@section('title', 'System Settings')

@section('content_header')
    <h1>System Settings</h1>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ url('admin/settings') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- General Settings -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">General Settings</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="site_email">Company Email</label>
                            <input type="email" class="form-control @error('site_email') is-invalid @enderror" 
                                   id="site_email" name="site_email" 
                                   value="{{ old('site_email', $settings['site_email'] ?? '') }}"
                                   placeholder="info@bryanzlogistics.com">
                            @error('site_email')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="site_phone">Company Phone</label>
                            <input type="text" class="form-control @error('site_phone') is-invalid @enderror" 
                                   id="site_phone" name="site_phone" 
                                   value="{{ old('site_phone', $settings['site_phone'] ?? '') }}"
                                   placeholder="+1234567890">
                            @error('site_phone')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="site_address">Company Address</label>
                            <input type="text" class="form-control @error('site_address') is-invalid @enderror" 
                                   id="site_address" name="site_address" 
                                   value="{{ old('site_address', $settings['site_address'] ?? '') }}"
                                   placeholder="123 Main Street, City, Country">
                            @error('site_address')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="system_currency">System Defaults Currency</label>
                            <select class="form-control @error('system_currency') is-invalid @enderror" 
                                    id="system_currency" name="system_currency">
                                @php $curr = old('system_currency', $settings['system_currency'] ?? 'UGX'); @endphp
                                <option value="UGX" {{ $curr == 'UGX' ? 'selected' : '' }}>Ugandan Shilling (UGX)</option>
                                <option value="USD" {{ $curr == 'USD' ? 'selected' : '' }}>US Dollar (USD)</option>
                                <option value="EUR" {{ $curr == 'EUR' ? 'selected' : '' }}>Euro (EUR)</option>
                                <option value="GBP" {{ $curr == 'GBP' ? 'selected' : '' }}>British Pound (GBP)</option>
                                <option value="KES" {{ $curr == 'KES' ? 'selected' : '' }}>Kenyan Shilling (KES)</option>
                                <option value="RWF" {{ $curr == 'RWF' ? 'selected' : '' }}>Rwandan Franc (RWF)</option>
                                <option value="TZS" {{ $curr == 'TZS' ? 'selected' : '' }}>Tanzanian Shilling (TZS)</option>
                            </select>
                            <small class="form-text text-muted">This currency will be used across the system for dashboards and default totals.</small>
                            @error('system_currency')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="site_logo">Company Logo</label>
                    @if(isset($settings['site_logo']))
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $settings['site_logo']) }}" alt="Logo" style="max-height: 100px;">
                        </div>
                    @endif
                    <input type="file" class="form-control-file @error('site_logo') is-invalid @enderror" 
                           id="site_logo" name="site_logo" accept="image/*">
                    <small class="form-text text-muted">Upload a new logo (max 2MB)</small>
                    @error('site_logo')
                        <span class="invalid-feedback d-block">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Email Settings -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Email Configuration (SMTP)</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="smtp_host">SMTP Host</label>
                            <input type="text" class="form-control" id="smtp_host" name="smtp_host" 
                                   value="{{ old('smtp_host', $settings['smtp_host'] ?? '') }}"
                                   placeholder="smtp.gmail.com">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="smtp_port">SMTP Port</label>
                            <input type="number" class="form-control" id="smtp_port" name="smtp_port" 
                                   value="{{ old('smtp_port', $settings['smtp_port'] ?? '587') }}"
                                   placeholder="587">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="smtp_encryption">Encryption</label>
                            <select class="form-control" id="smtp_encryption" name="smtp_encryption">
                                <option value="tls" {{ ($settings['smtp_encryption'] ?? 'tls') == 'tls' ? 'selected' : '' }}>TLS</option>
                                <option value="ssl" {{ ($settings['smtp_encryption'] ?? '') == 'ssl' ? 'selected' : '' }}>SSL</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="smtp_username">SMTP Username</label>
                            <input type="text" class="form-control" id="smtp_username" name="smtp_username" 
                                   value="{{ old('smtp_username', $settings['smtp_username'] ?? '') }}"
                                   placeholder="your-email@gmail.com">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="smtp_password">SMTP Password</label>
                            <input type="password" class="form-control" id="smtp_password" name="smtp_password" 
                                   value="{{ old('smtp_password', $settings['smtp_password'] ?? '') }}"
                                   placeholder="••••••••">
                            <small class="form-text text-muted">Leave blank to keep current password</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notification Preferences -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-bell"></i> Notification Preferences</h3>
            </div>
            <div class="card-body">
                <p class="text-muted">Choose which channels to use for sending shipment status update notifications to clients.</p>
                
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="notify_status_change_email" 
                               name="notify_status_change_email" value="1"
                               {{ ($settings['notify_status_change_email'] ?? 1) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="notify_status_change_email">
                            <i class="fas fa-envelope text-primary"></i> Email Notifications
                        </label>
                        <small class="form-text text-muted">Send email notifications when shipment status changes</small>
                    </div>
                </div>

                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="notify_status_change_whatsapp" 
                               name="notify_status_change_whatsapp" value="1"
                               {{ ($settings['notify_status_change_whatsapp'] ?? 1) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="notify_status_change_whatsapp">
                            <i class="fab fa-whatsapp text-success"></i> WhatsApp Notifications
                        </label>
                        <small class="form-text text-muted">Send WhatsApp messages when shipment status changes</small>
                    </div>
                </div>

                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="notify_status_change_sms" 
                               name="notify_status_change_sms" value="1"
                               {{ ($settings['notify_status_change_sms'] ?? 0) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="notify_status_change_sms">
                            <i class="fas fa-sms text-info"></i> SMS Notifications
                        </label>
                        <small class="form-text text-muted">Send SMS when shipment status changes (Coming Soon)</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Settings
                </button>
            </div>
        </div>
    </form>
@stop

@section('footer')
    <strong>Copyright &copy; {{ date('Y') }} <a href="#">Eagle Cargo Freights</a>.</strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
        <b>Support Call</b> +256 200 991 118
    </div>
@stop
