@extends('adminlte::page')

@section('title', 'Bulk Broadcast')

@section('content_header')
    <h1>Send Bulk Broadcast</h1>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {{ session('success') }}
        </div>
    @endif

    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Compose Message</h3>
        </div>
        <form action="{{ url('admin/broadcast/send') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="form-group">
                    <label>Recipients</label>
                    <div class="custom-control custom-radio">
                        <input class="custom-control-input" type="radio" id="recipientsAll" name="recipients" value="all" checked onchange="toggleClientSelect()">
                        <label for="recipientsAll" class="custom-control-label">All Clients ({{ $clients->count() }} total)</label>
                    </div>
                    <div class="custom-control custom-radio">
                        <input class="custom-control-input" type="radio" id="recipientsSelected" name="recipients" value="selected" onchange="toggleClientSelect()">
                        <label for="recipientsSelected" class="custom-control-label">Selected Clients</label>
                    </div>
                </div>

                <div class="form-group" id="clientSelectGroup" style="display: none;">
                    <label for="client_ids">Select Clients</label>
                    <select class="form-control" id="client_ids" name="client_ids[]" multiple size="5" style="width: 100%;">
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}">{{ $client->name }} ({{ $client->email }})</option>
                        @endforeach
                    </select>
                    <small class="form-text text-muted">Hold Ctrl/Cmd to select multiple clients</small>
                </div>

                <div class="form-group">
                    <label>Channel</label>
                    <div class="custom-control custom-radio">
                        <input class="custom-control-input" type="radio" id="channelEmail" name="channel" value="email" checked>
                        <label for="channelEmail" class="custom-control-label">Email</label>
                    </div>
                    <div class="custom-control custom-radio">
                        <input class="custom-control-input" type="radio" id="channelSMS" name="channel" value="sms">
                        <label for="channelSMS" class="custom-control-label">SMS</label>
                    </div>
                    <div class="custom-control custom-radio">
                        <input class="custom-control-input" type="radio" id="channelWhatsApp" name="channel" value="whatsapp">
                        <label for="channelWhatsApp" class="custom-control-label"><i class="fab fa-whatsapp text-success"></i> WhatsApp</label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="subject">Subject</label>
                    <input type="text" class="form-control @error('subject') is-invalid @enderror" id="subject" name="subject" placeholder="Enter subject" value="{{ old('subject') }}" required>
                    @error('subject')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea class="form-control @error('message') is-invalid @enderror" id="message" name="message" rows="5" placeholder="Enter your message here..." required>{{ old('message') }}</textarea>
                    @error('message')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="alert alert-info">
                    <i class="icon fas fa-info"></i> <span id="recipientCount">This message will be sent to all registered clients.</span>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i> Send Broadcast
                </button>
            </div>
        </form>
    </div>
@stop

@section('js')
<script>
function toggleClientSelect() {
    const selected = document.getElementById('recipientsSelected').checked;
    const clientSelectGroup = document.getElementById('clientSelectGroup');
    const clientSelect = document.getElementById('client_ids');
    
    if (selected) {
        clientSelectGroup.style.display = 'block';
        clientSelect.required = true;
    } else {
        clientSelectGroup.style.display = 'none';
        clientSelect.required = false;
    }
    
    updateRecipientCount();
}

function updateRecipientCount() {
    const selected = document.getElementById('recipientsSelected').checked;
    const clientSelect = document.getElementById('client_ids');
    const recipientCount = document.getElementById('recipientCount');
    
    if (selected) {
        const count = clientSelect.selectedOptions.length;
        recipientCount.textContent = count > 0 
            ? `This message will be sent to ${count} selected client(s).`
            : 'Please select at least one client.';
    } else {
        recipientCount.textContent = 'This message will be sent to all registered clients.';
    }
}

// Update count when selection changes
document.addEventListener('DOMContentLoaded', function() {
    const clientSelect = document.getElementById('client_ids');
    if (clientSelect) {
        clientSelect.addEventListener('change', updateRecipientCount);
    }
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
