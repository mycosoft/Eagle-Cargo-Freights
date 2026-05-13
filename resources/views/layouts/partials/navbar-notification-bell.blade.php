<li class="nav-item dropdown" id="notification-bell">
    <a class="nav-link" data-toggle="dropdown" href="#" aria-expanded="false">
        <i class="fas fa-bell"></i>
        <span class="badge badge-warning navbar-badge" id="notification-count" style="display: none;">0</span>
    </a>
    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" style="max-height: 400px; overflow-y: auto;">
        <div class="dropdown-header">
            <strong>Notifications</strong>
            <a href="#" class="float-right text-muted small" onclick="clearAllNotifications(event)">Clear all</a>
        </div>
        <div id="notification-list">
            <div class="text-center p-3 text-muted">
                <i class="fas fa-bell-slash fa-2x mb-2"></i>
                <p class="mb-0">No notifications</p>
            </div>
        </div>
        <div class="dropdown-divider"></div>
        <a href="{{ route('admin.notifications.index') }}" class="dropdown-item dropdown-footer">View All Notifications</a>
    </div>
</li>

@push('js')
<script>
function loadNotifications() {
    fetch('{{ route('admin.notifications.unread') }}')
        .then(response => response.json())
        .then(data => {
            const countEl = document.getElementById('notification-count');
            const listEl = document.getElementById('notification-list');

            if (data.count > 0) {
                countEl.textContent = data.count > 99 ? '99+' : data.count;
                countEl.style.display = 'inline';

                let html = '';
                data.notifications.forEach(notification => {
                    html += `
                        <a href="${notification.link || '#'}" class="dropdown-item notification-item ${notification.is_read ? 'read' : 'unread'}"
                           data-id="${notification.id}" onclick="markNotificationRead(${notification.id})">
                            <i class="${notification.icon || 'fas fa-bell'} mr-2 text-${notification.color}"></i>
                            <div>
                                <strong>${notification.title}</strong>
                                <p class="mb-0 text-muted small">${notification.message.substring(0, 50)}${notification.message.length > 50 ? '...' : ''}</p>
                                <small class="text-muted">${formatTimeAgo(notification.created_at)}</small>
                            </div>
                        </a>
                    `;
                });
                listEl.innerHTML = html;
            } else {
                countEl.style.display = 'none';
                listEl.innerHTML = `
                    <div class="text-center p-3 text-muted">
                        <i class="fas fa-bell-slash fa-2x mb-2"></i>
                        <p class="mb-0">No notifications</p>
                    </div>
                `;
            }
        })
        .catch(error => console.error('Error loading notifications:', error));
}

function markNotificationRead(id) {
    fetch(`/admin/notifications/${id}/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        }
    });
}

function markAllNotificationsRead(event) {
    if (event) event.preventDefault();
    fetch('{{ route('admin.notifications.mark-all-read') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        }
    }).then(() => {
        loadNotifications();
    });
}

function formatTimeAgo(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const seconds = Math.floor((now - date) / 1000);

    if (seconds < 60) return 'Just now';
    if (seconds < 3600) return Math.floor(seconds / 60) + ' min ago';
    if (seconds < 86400) return Math.floor(seconds / 3600) + ' hours ago';
    if (seconds < 604800) return Math.floor(seconds / 86400) + ' days ago';
    return date.toLocaleDateString();
}

document.addEventListener('DOMContentLoaded', function() {
    loadNotifications();
    setInterval(loadNotifications, 30000);
    loadChatUnreadCount();
    setInterval(loadChatUnreadCount, 30000);
});

function loadChatUnreadCount() {
    console.log('Fetching chat unread count...');
    fetch('/chat/unread-count')
        .then(function(response) {
            console.log('Chat count response:', response.status);
            if (!response.ok) {
                console.error('Chat count error status:', response.status);
                return;
            }
            return response.json();
        })
        .then(function(data) {
            if (!data) return;
            console.log('Chat count data:', data);
            var countEl = document.getElementById('chat-unread-count');
            if (!countEl) return;
            if (data.count > 0) {
                countEl.textContent = data.count > 99 ? '99+' : data.count;
                countEl.style.display = 'inline';
            } else {
                countEl.style.display = 'none';
            }
        })
        .catch(function(error) {
            console.error('Error loading chat count:', error);
        });
}

function clearAllNotifications(event) {
    if (event) event.preventDefault();
    if (confirm('Clear all notifications?')) {
        fetch('{{ route('admin.notifications.clear-all') }}', {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            }
        }).then(() => {
            loadNotifications();
        });
    }
}

$(document).on('click', '.notification-item', function() {
    const id = $(this).data('id');
    if (id) {
        markNotificationRead(id);
    }
});
</script>

<style>
.notification-item.unread {
    background-color: #f8f9fa;
}
.notification-item.read {
    opacity: 0.7;
}
#notification-bell .dropdown-menu {
    width: 350px;
}
</style>
@endpush