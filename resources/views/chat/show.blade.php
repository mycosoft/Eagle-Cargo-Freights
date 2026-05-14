@extends('adminlte::page')

@section('title', 'Chat - ' . ($conversation->getOtherParticipant(auth()->user())?->name ?? 'Messages'))

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>
                @if($conversation->type === 'direct')
                    <i class="fas fa-user mr-2"></i>
                    {{ $conversation->getOtherParticipant(auth()->user())?->name ?? 'Conversation' }}
                @else
                    <i class="fas fa-users mr-2"></i>
                    {{ $conversation->subject ?? 'Group Chat' }}
                    <small class="text-muted">({{ $conversation->participants->count() }} members)</small>
                @endif
            </h1>
        </div>
        <div class="col-sm-6">
            <a href="{{ route('chat.index') }}" class="btn btn-secondary float-right">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card" style="height: calc(100vh - 200px); display: flex; flex-direction: column;">
            <div class="card-body flex-grow-1" style="overflow-y: auto; padding: 1rem;" id="messages-container">
                @forelse($messages as $message)
                    <div class="message-wrapper {{ $message->user_id === auth()->id() ? 'message-mine' : 'message-other' }}">
                        <div class="message {{ $message->user_id === auth()->id() ? 'bg-primary text-white' : 'bg-light' }}">
                            @if($message->isImage())
                                <img src="{{ Storage::url($message->attachment_path) }}" alt="{{ $message->attachment_name }}" class="chat-image" style="max-width: 300px; max-height: 200px; border-radius: 8px;">
                            @endif
                            @if($message->body)
                                <p class="mb-0">{{ $message->body }}</p>
                            @endif
                            @if($message->isFile())
                                <a href="{{ Storage::url($message->attachment_path) }}" target="_blank" class="text-{{ $message->user_id === auth()->id() ? 'white' : 'primary' }}">
                                    <i class="fas fa-file"></i> {{ $message->attachment_name }}
                                </a>
                            @endif
                        </div>
                        <small class="text-muted d-block mt-1">
                            {{ $message->created_at->format('M d, H:i') }}
                            @if($message->user_id === auth()->id() && $message->is_read)
                                <i class="fas fa-check-double text-success ml-1"></i>
                            @endif
                        </small>
                    </div>
                @empty
                    <div class="text-center text-muted p-5">
                        <i class="fas fa-comment-dots fa-3x mb-3"></i>
                        <p>No messages yet. Start the conversation!</p>
                    </div>
                @endforelse
            </div>
            <div class="card-footer">
                <form id="message-form" enctype="multipart/form-data">
                    @csrf
                    <div class="input-group">
                        <input type="text" name="body" id="message-input" class="form-control" placeholder="Type a message..." autocomplete="off">
                        <div class="input-group-append">
                            <label class="btn btn-secondary" for="attachment-input" style="cursor: pointer;">
                                <i class="fas fa-paperclip"></i>
                                <input type="file" id="attachment-input" name="attachment" style="display: none;" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx">
                            </label>
                        </div>
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </div>
                    <div id="attachment-preview" class="mt-2" style="display: none;">
                        <span class="badge badge-info"></span>
                        <button type="button" class="btn btn-sm btn-danger" onclick="clearAttachment()"><i class="fas fa-times"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
.message-wrapper {
    margin-bottom: 1rem;
    max-width: 75%;
}
.message-mine {
    margin-left: auto;
    text-align: right;
}
.message-other {
    margin-right: auto;
}
.message {
    padding: 0.75rem 1rem;
    border-radius: 1rem;
    display: inline-block;
    max-width: 100%;
    word-wrap: break-word;
}
.message-mine .message {
    border-bottom-right-radius: 0;
}
.message-other .message {
    border-bottom-left-radius: 0;
}
.chat-image {
    display: block;
    margin-bottom: 0.5rem;
}
</style>
@stop

@section('js')
<script>
const conversationUuid = '{{ $conversation->uuid }}';
let lastMessageId = {{ $messages->max('id') ?? 0 }};

$(document).ready(function() {
    scrollToBottom();

    $('#message-form').on('submit', function(e) {
        e.preventDefault();
        sendMessage();
    });

    $('#attachment-input').on('change', function() {
        const file = this.files[0];
        if (file) {
            $('#attachment-preview').show().find('span').text(file.name);
        }
    });

    setInterval(pollMessages, 3000);
});

function scrollToBottom() {
    const container = $('#messages-container');
    container.scrollTop(container[0].scrollHeight);
}

function sendMessage() {
    const formData = new FormData();
    formData.append('body', $('#message-input').val());
    const fileInput = $('#attachment-input')[0];
    if (fileInput.files[0]) {
        formData.append('attachment', fileInput.files[0]);
    }

    if (!$('#message-input').val().trim() && !fileInput.files[0]) {
        return;
    }

    $.ajax({
        url: `/chat/${conversationUuid}/send`,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            console.log('Success:', response);
            addMessageToChat(response.message);
            $('#message-input').val('');
            clearAttachment();
            scrollToBottom();
        },
        error: function(xhr) {
            console.log('Error:', xhr.responseText);
            alert('Failed to send message: ' + (xhr.responseJSON?.error || xhr.statusText));
        }
    });
}

function addMessageToChat(message) {
    const isMine = message.is_mine;
    const messageClass = isMine ? 'message-mine' : 'message-other';
    const bubbleClass = isMine ? 'bg-primary text-white' : 'bg-light';

    let attachmentHtml = '';
    if (message.attachment_path) {
        if (message.is_image) {
            attachmentHtml = `<img src="/storage/${message.attachment_path}" alt="${message.attachment_name}" class="chat-image" style="max-width: 300px; max-height: 200px; border-radius: 8px;">`;
        } else {
            attachmentHtml = `<a href="/storage/${message.attachment_path}" target="_blank" class="text-${isMine ? 'white' : 'primary'}"><i class="fas fa-file"></i> ${message.attachment_name}</a>`;
        }
    }

    const html = `
        <div class="message-wrapper ${messageClass}">
            <div class="message ${bubbleClass}">
                ${attachmentHtml}
                ${message.body ? `<p class="mb-0">${message.body}</p>` : ''}
            </div>
            <small class="text-muted d-block mt-1">
                ${new Date(message.created_at).toLocaleTimeString('en-US', {hour: '2-digit', minute:'2-digit'})}
                ${isMine && message.is_read ? '<i class="fas fa-check-double text-success ml-1"></i>' : ''}
            </small>
        </div>
    `;

    $('#messages-container').append(html);
    lastMessageId = message.id;
}

function clearAttachment() {
    $('#attachment-input').val('');
    $('#attachment-preview').hide();
}

function pollMessages() {
    $.get(`/chat/${conversationUuid}/messages`, function(messages) {
        const newMessages = messages.filter(m => m.id > lastMessageId);
        if (newMessages.length > 0) {
            newMessages.forEach(addMessageToChat);
            scrollToBottom();
        }
    });
}
</script>
@stop

@section('footer')
    <strong>Copyright &copy; {{ date('Y') }} <a href="#">Eagle Cargo Freights</a>.</strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
        <b>Support Call</b> +256 200 991 118
    </div>
@stop