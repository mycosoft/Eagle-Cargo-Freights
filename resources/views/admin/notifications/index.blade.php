@extends('adminlte::page')

@section('title', 'Notifications')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>Notifications</h1>
        </div>
        <div class="col-sm-6">
            <button type="button" class="btn btn-secondary float-right" onclick="clearAllNotifications()">
                <i class="fas fa-trash"></i> Clear All
            </button>
        </div>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-body p-0">
            @forelse($notifications as $notification)
                <div class="notification-item p-3 border-bottom {{ $notification->is_read ? 'bg-light' : '' }}" data-id="{{ $notification->id }}">
                    <div class="d-flex align-items-start">
                        <div class="mr-3">
                            <i class="{{ $notification->icon ?? 'fas fa-bell' }} text-{{ $notification->color ?? 'primary' }} fa-lg"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center">
                                <strong>{{ $notification->title }}</strong>
                                <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                            </div>
                            <p class="mb-1">{{ $notification->message }}</p>
                            @if($notification->link)
                                <a href="{{ $notification->link }}" class="btn btn-sm btn-primary">View Details</a>
                            @endif
                            <div class="mt-2">
                                @if(!$notification->is_read)
                                    <button type="button" class="btn btn-sm btn-outline-success" onclick="markAsRead({{ $notification->id }})">
                                        <i class="fas fa-check"></i> Mark as Read
                                    </button>
                                @endif
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteNotification({{ $notification->id }})">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center p-5 text-muted">
                    <i class="fas fa-bell-slash fa-3x mb-3"></i>
                    <p class="mb-0">No notifications</p>
                </div>
            @endforelse
        </div>
        @if($notifications->hasPages())
            <div class="card-footer">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
@stop

@section('js')
<script>
function markAsRead(id) {
    fetch(`/admin/notifications/${id}/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        }
    }).then(() => {
        location.reload();
    });
}

function deleteNotification(id) {
    if (confirm('Delete this notification?')) {
        fetch(`/admin/notifications/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            }
        }).then(() => {
            location.reload();
        });
    }
}

function clearAllNotifications() {
    if (confirm('Clear all notifications?')) {
        fetch('{{ route('admin.notifications.clear-all') }}', {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            }
        }).then(() => {
            location.reload();
        });
    }
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