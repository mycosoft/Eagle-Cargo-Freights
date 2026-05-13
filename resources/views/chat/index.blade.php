@extends('adminlte::page')

@section('title', 'Messages')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1><i class="fas fa-comments mr-2"></i>Messages</h1>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Conversations</h3>
                </div>
                <div class="card-body p-0">
                    <div id="conversations-list" style="max-height: 500px; overflow-y: auto;">
                        @forelse($conversations as $conv)
                            <a href="{{ route('chat.show', $conv) }}" class="conversation-item p-3 border-bottom d-block text-dark">
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong>{{ $conv->getOtherParticipant(auth()->user())?->name ?? 'Unknown' }}</strong>
                                    @if($conv->getUnreadCountForUser(auth()->user()) > 0)
                                        <span class="badge badge-primary">{{ $conv->getUnreadCountForUser(auth()->user()) }}</span>
                                    @endif
                                </div>
                                @if($conv->latestMessage)
                                    <p class="mb-0 text-muted small">
                                        @if($conv->latestMessage->user_id === auth()->id())
                                            <span class="text-primary">You:</span>
                                        @endif
                                        {{ Str::limit($conv->latestMessage->body ?? 'Sent an attachment', 40) }}
                                    </p>
                                    <small class="text-muted">{{ $conv->latestMessage->created_at->diffForHumans() }}</small>
                                @else
                                    <p class="mb-0 text-muted small">No messages yet</p>
                                @endif
                            </a>
                        @empty
                            <div class="p-4 text-center text-muted">
                                <i class="fas fa-comment-slash fa-2x mb-2"></i>
                                <p class="mb-0">No conversations yet</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Start a New Conversation</h3>
                </div>
                <div class="card-body">
                    <p>Select a user to start chatting:</p>
                    <div class="list-group">
                        @foreach(\App\Models\User::where('id', '!=', auth()->id())->limit(50)->get() as $user)
                            <a href="{{ route('chat.user', $user) }}" class="list-group-item list-group-item-action">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $user->name }}</strong>
                                        <br><small class="text-muted">{{ $user->email }}</small>
                                    </div>
                                    <i class="fas fa-comment text-primary"></i>
                                </div>
                            </a>
                        @endforeach
                    </div>
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