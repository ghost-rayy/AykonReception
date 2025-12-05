<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aykon Reception</title>
    @stack('head')

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.min.css">

    <style>
        /* Color Scheme Override */
        :root {
            --bs-primary: #0d1b2a;
            --bs-primary-rgb: 13, 27, 42;
        }
        
        .text-primary, .text-primary-custom {
            color: #0d1b2a !important;
        }
        
        .bg-primary, .bg-primary-custom {
            background-color: #0d1b2a !important;
        }
        
        .btn-primary {
            background-color: #0d1b2a;
            border-color: #0d1b2a;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #1a2f47;
            border-color: #1a2f47;
            color: white;
        }
        
        .btn-outline-primary {
            color: #0d1b2a;
            border-color: #0d1b2a;
            background: white;
        }
        
        .btn-outline-primary:hover {
            background-color: #0d1b2a;
            border-color: #0d1b2a;
            color: white;
        }
        
        .border-primary {
            border-color: #0d1b2a !important;
        }
        
        /* For white backgrounds on dark elements */
        .bg-white-custom {
            background-color: white !important;
        }
        
        .text-white-custom {
            color: white !important;
        }
        
        /* Notifications */
        .notification-bell {
            position: relative;
            cursor: pointer;
            padding: 10px 14px;
            border-radius: 8px;
            transition: all 0.2s;
            color: #0d1b2a !important;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #e5e7eb;
            background: #f9fafb;
        }
        
        .notification-bell:hover {
            background: #0d1b2a;
            border-color: #0d1b2a;
        }
        
        .notification-bell:hover i {
            color: white !important;
        }
        
        .notification-bell i {
            color: #0d1b2a !important;
            font-size: 22px;
            transition: color 0.2s;
        }
        
        .notification-badge {
            position: absolute;
            top: 4px;
            right: 4px;
            background: #ef4444;
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        
        .notification-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            width: 380px;
            max-height: 500px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
            z-index: 1000;
            display: none;
            overflow: hidden;
            margin-top: 8px;
        }
        
        .notification-dropdown.show {
            display: block;
            animation: slideDown 0.3s ease;
        }
        
        .notification-header {
            padding: 16px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f9fafb;
        }
        
        .notification-list {
            max-height: 400px;
            overflow-y: auto;
        }
        
        .notification-item {
            padding: 12px 16px;
            border-bottom: 1px solid #f3f4f6;
            cursor: pointer;
            transition: background 0.2s;
            display: flex;
            align-items: start;
            gap: 12px;
        }
        
        .notification-item:hover {
            background: #f9fafb;
        }
        
        .notification-item.unread {
            background: #eff6ff;
            border-left: 3px solid #0d1b2a;
        }
        
        .notification-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }
        
        .notification-content {
            flex: 1;
        }
        
        .notification-title {
            font-weight: 600;
            font-size: 14px;
            color: #1f2937;
            margin-bottom: 4px;
        }
        
        .notification-message {
            font-size: 13px;
            color: #6b7280;
            line-height: 1.4;
        }
        
        .notification-time {
            font-size: 11px;
            color: #9ca3af;
            margin-top: 4px;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        body {
            background: #eef1f5;
            font-family: 'Segoe UI', sans-serif;
        }

        /* Sidebar */
        .sidebar {
            width: 240px;
            background: #0d1b2a;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            padding-top: 20px;
            color: white;
            box-shadow: 4px 0 12px rgba(0,0,0,0.15);
            z-index: 999;
        }
        .sidebar a {
            color: #cdd0d4;
            display: block;
            padding: 14px 22px;
            text-decoration: none;
            font-size: 15px;
            font-weight: 500;
            transition: 0.2s ease;
            border-radius: 6px;
            margin: 4px 10px;
        }
        .sidebar a:hover {
            background: #1b263b;
            color: #fff;
            transform: translateX(4px);
        }
        .sidebar .active {
            background: #415a77;
            color: #fff;
        }

        /* Sidebar Logo */
        .sidebar-logo {
            text-align: center;
            margin-bottom: 30px;
            padding: 15px 0;
            border-bottom: 2px solid #1b263b;
        }
        .sidebar-logo-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .sidebar-logo-icon {
            width: 52px;
            height: 52px;
            border-radius: 8px;
            background: rgba(255,255,255,0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 5px;
            box-shadow: 0 4px 12px rgba(255,255,255,0.2);
        }
        .sidebar-logo-icon img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        .sidebar-logo-text {
            font-size: 26px;
            font-weight: 700;
            color: white;
        }

        /* Content */
        .content {
            margin-left: 240px;
            padding: 25px;
        }

        /* Topbar */
        .topbar {
            background: white;
            padding: 12px 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 15px rgba(0,0,0,0.08);
        }

        .user-box {
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            padding: 8px 12px;
            border-radius: 8px;
            transition: 0.2s;
        }
        .user-box:hover {
            background: #f1f3f6;
        }
        .user-box img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 2px solid white;
        }

        /* Dropdown */
        .dropdown-menu {
            box-shadow: 0 4px 18px rgba(0,0,0,0.1);
            border-radius: 10px;
        }

        /* Modal */
        .modal-message {
            display: none;
            position: fixed;
            z-index: 99999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.4);
            animation: fadeIn 0.3s ease-in;
        }
        .modal-message.show {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .modal-message-content {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            max-width: 500px;
            min-width: 300px;
            animation: slideDown 0.3s ease-out;
        }

        @keyframes fadeIn { from { opacity: 0 } to { opacity: 1 } }
        @keyframes slideDown {
            from { transform: translateY(-40px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        /* Responsive sidebar */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .content {
                margin-left: 0;
                padding: 15px;
            }
        }

        /* Consistent button styles */
        .btn {
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        /* Consistent card styles */
        .card {
            border-radius: 12px;
            border: none;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        /* Consistent form styles */
        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #dee2e6;
            transition: all 0.2s ease;
        }
        .form-control:focus, .form-select:focus {
            border-color: #0d1b2a;
            box-shadow: 0 0 0 0.2rem rgba(13, 27, 42, 0.25);
        }

    </style>
</head>
<body>

    <!-- SIDEBAR -->
    <div class="sidebar">
        <div class="sidebar-logo">
            <div class="sidebar-logo-container">
                <div class="sidebar-logo-icon">
                    <img src="{{ asset('copy1.png') }}" alt="Aykon Logo">
                </div>
                <h4 class="sidebar-logo-text">AYKON</h4>
            </div>
        </div>

        @auth
            @if(auth()->user()->role === 'admin')
                <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2 me-2"></i>Admin Dashboard
                </a>
                <a href="{{ route('admin.users') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="bi bi-people me-2"></i>User Management
                </a>
                <!-- <a href="{{ route('admin.settings') }}" class="{{ request()->routeIs('admin.settings*') ? 'active' : '' }}">
                    <i class="bi bi-gear me-2"></i>System Settings
                </a> -->
                <!-- <a href="{{ route('admin.logs') }}" class="{{ request()->routeIs('admin.logs') ? 'active' : '' }}">
                    <i class="bi bi-journal-text me-2"></i>Activity Logs
                </a> -->
                <!-- <a href="{{ route('admin.health') }}" class="{{ request()->routeIs('admin.health') ? 'active' : '' }}">
                    <i class="bi bi-heart-pulse me-2"></i>System Health
                </a> -->
                <hr style="border-color: #1b263b; margin: 10px 0;">
            @elseif(auth()->user()->role === 'staff')
                <a href="{{ route('staff_dashboard') }}" class="{{ request()->routeIs('staff_dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2 me-2"></i>Staff Dashboard
                </a>
            @else
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2 me-2"></i>Dashboard
                </a>
            @endif

            @if(in_array(auth()->user()->role, ['admin', 'receptionist']))
                <a href="{{ route('staff.index') }}" class="{{ request()->routeIs('staff.*') ? 'active' : '' }}">
                    <i class="bi bi-people me-2"></i>Staff
                </a>
                <a href="{{ route('visitors.index') }}" class="{{ request()->routeIs('visitors.*') ? 'active' : '' }}">
                    <i class="bi bi-person-check me-2"></i>Visitors
                </a>
                <a href="{{ route('appointments.index')}}" class="{{ request()->routeIs('appointments.*') ? 'active' : '' }}">
                    <i class="bi bi-calendar-event me-2"></i>Appointments
                </a>
            @endif

            @if(auth()->user()->role === 'admin')
                <a href="{{ route('reports.index') }}" class="{{ request()->routeIs('reports.*') ? 'active' : '' }}">
                    <i class="bi bi-graph-up me-2"></i>Reports
                </a>
            @endif

            @if(in_array(auth()->user()->role, ['receptionist', 'staff']))
                <a href="{{ route('messages.index') }}" class="{{ request()->routeIs('messages.*') ? 'active' : '' }}">
                    <i class="bi bi-chat-dots me-2"></i>Messages
                    @php
                        $unreadCount = \App\Models\Message::where('receiver_id', auth()->id())
                            ->where('is_read', false)
                            ->count();
                    @endphp
                    @if($unreadCount > 0)
                        <span class="badge bg-danger ms-2" id="messages-badge">{{ $unreadCount }}</span>
                    @endif
                </a>
            @endif
        @endauth
    </div>


    <!-- MAIN CONTENT -->
    <div class="content">

        <!-- TOPBAR -->
        <div class="topbar">

            <h4 class="fw-bold m-0">@yield('title')</h4>

            @auth
            <div class="d-flex align-items-center gap-3">
                @if(in_array(auth()->user()->role, ['receptionist', 'admin']))
                <!-- Notifications -->
                <div class="position-relative" style="position: relative;">
                    <div class="notification-bell" onclick="toggleNotifications()" title="Notifications">
                        <i class="bi bi-bell"></i>
                        <span id="notification-badge" class="notification-badge" style="display: none;">0</span>
                    </div>
                    <div id="notification-dropdown" class="notification-dropdown">
                        <div class="notification-header">
                            <h6 class="mb-0 fw-bold">Notifications</h6>
                            <div>
                                <button class="btn btn-sm btn-link p-0" onclick="markAllNotificationsRead()" id="mark-all-read-btn" style="display: none;">Mark all read</button>
                            </div>
                        </div>
                        <div id="notification-list" class="notification-list">
                            <div class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                                <small>No notifications</small>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <div class="dropdown">
                    <div class="user-box dropdown-toggle" data-bs-toggle="dropdown">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=0d1b2a&color=fff">
                        <div>
                            <strong>{{ auth()->user()->name }}</strong><br>
                            <small class="text-muted">{{ ucfirst(auth()->user()->role) }}</small>
                        </div>
                    </div>

                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#">Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="dropdown-item text-danger">Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
            @endauth

        </div>

        @yield('content')

    </div>


    <!-- MESSAGE MODAL -->
    <div id="messageModal" class="modal-message">
        <div class="modal-message-content">
            <div class="modal-message-header">
                <h5 id="modalTitle">Message</h5>
            </div>
            <div class="modal-message-body" id="modalBody"></div>
            <div class="modal-message-footer text-end">
                <button class="btn btn-sm btn-secondary" onclick="closeModal()">Close</button>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Modal Script -->
    <script>
        function showModal(title, message, type = 'info') {
            const modal = document.getElementById('messageModal');
            document.getElementById('modalTitle').textContent = title;
            document.getElementById('modalBody').textContent = message;

            modal.classList.remove('success', 'error', 'info');
            modal.classList.add(type);
            modal.classList.add('show');
        }

        function closeModal() {
            document.getElementById('messageModal').classList.remove('show');
        }

        window.onclick = function(event) {
            const modal = document.getElementById('messageModal');
            if (event.target === modal) closeModal();
        }

        @if ($message = Session::get('success'))
            showModal('Success', '{{ $message }}', 'success');
        @endif

        @if ($message = Session::get('error'))
            showModal('Error', '{{ $message }}', 'error');
        @endif

        @if ($errors->any())
            let errorMsg = '';
            @foreach ($errors->all() as $error)
                errorMsg += '• {{ $error }}\n';
            @endforeach

            showModal('Validation Errors', errorMsg, 'error');
        @endif
    </script>

    @yield('scripts')

    @if(in_array(auth()->user()->role ?? '', ['receptionist', 'admin']))
    <!-- Notification System -->
    <script>
        let notificationCheckInterval;
        let notificationDropdownOpen = false;

        function toggleNotifications() {
            const dropdown = document.getElementById('notification-dropdown');
            notificationDropdownOpen = !notificationDropdownOpen;
            
            if (notificationDropdownOpen) {
                dropdown.classList.add('show');
                loadNotifications();
            } else {
                dropdown.classList.remove('show');
            }
        }

        function loadNotifications() {
            fetch('{{ route("notifications.index") }}', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                updateNotificationBadge(data.unread_count);
                renderNotifications(data.notifications);
            })
            .catch(error => {
                console.error('Error loading notifications:', error);
            });
        }

        function updateNotificationBadge(count) {
            const badge = document.getElementById('notification-badge');
            if (count > 0) {
                badge.textContent = count > 99 ? '99+' : count;
                badge.style.display = 'flex';
            } else {
                badge.style.display = 'none';
            }
        }

        function renderNotifications(notifications) {
            const container = document.getElementById('notification-list');
            const markAllBtn = document.getElementById('mark-all-read-btn');
            
            if (notifications.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                        <small>No notifications</small>
                    </div>
                `;
                markAllBtn.style.display = 'none';
                return;
            }

            const unreadCount = notifications.filter(n => !n.is_read).length;
            markAllBtn.style.display = unreadCount > 0 ? 'block' : 'none';

            const colorMap = {
                'info': '#3b82f6',
                'warning': '#f59e0b',
                'danger': '#ef4444',
                'success': '#10b981',
                'primary': '#0d1b2a'
            };

            container.innerHTML = notifications.map(notif => {
                const timeAgo = getTimeAgo(notif.created_at);
                const bgColor = colorMap[notif.color] || '#3b82f6';
                
                return `
                    <div class="notification-item ${notif.is_read ? '' : 'unread'}" onclick="handleNotificationClick(${notif.id}, ${notif.is_read})">
                        <div class="notification-icon" style="background: ${bgColor}20; color: ${bgColor};">
                            <i class="bi ${notif.icon || 'bi-bell'}"></i>
                        </div>
                        <div class="notification-content">
                            <div class="notification-title">${escapeHtml(notif.title)}</div>
                            <div class="notification-message">${escapeHtml(notif.message)}</div>
                            <div class="notification-time">${timeAgo}</div>
                        </div>
                    </div>
                `;
            }).join('');
        }

        function handleNotificationClick(id, isRead) {
            if (!isRead) {
                markNotificationAsRead(id);
            }
        }

        function markNotificationAsRead(id) {
            fetch(`/notifications/${id}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(() => {
                loadNotifications();
            })
            .catch(error => {
                console.error('Error marking notification as read:', error);
            });
        }

        function markAllNotificationsRead() {
            fetch('{{ route("notifications.read-all") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(() => {
                loadNotifications();
            })
            .catch(error => {
                console.error('Error marking all as read:', error);
            });
        }

        function getTimeAgo(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diffMs = now - date;
            const diffMins = Math.floor(diffMs / 60000);
            const diffHours = Math.floor(diffMs / 3600000);
            const diffDays = Math.floor(diffMs / 86400000);

            if (diffMins < 1) return 'Just now';
            if (diffMins < 60) return `${diffMins}m ago`;
            if (diffHours < 24) return `${diffHours}h ago`;
            if (diffDays < 7) return `${diffDays}d ago`;
            return date.toLocaleDateString();
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('notification-dropdown');
            const bell = document.querySelector('.notification-bell');
            
            if (notificationDropdownOpen && !dropdown.contains(event.target) && !bell.contains(event.target)) {
                notificationDropdownOpen = false;
                dropdown.classList.remove('show');
            }
        });

        // Check for new notifications every 30 seconds
        function startNotificationPolling() {
            // Initial load
            loadNotifications();
            
            // Check unread count every 30 seconds
            setInterval(() => {
                fetch('{{ route("notifications.unread-count") }}', {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    updateNotificationBadge(data.count);
                })
                .catch(error => console.error('Error checking unread count:', error));
            }, 30000);

            // Check for alerts every minute
            setInterval(() => {
                fetch('{{ route("notifications.check-alerts") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                })
                .then(() => {
                    // Reload notifications if dropdown is open
                    if (notificationDropdownOpen) {
                        loadNotifications();
                    } else {
                        // Just update badge
                        fetch('{{ route("notifications.unread-count") }}', {
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        })
                        .then(response => response.json())
                        .then(data => updateNotificationBadge(data.count))
                        .catch(() => {});
                    }
                })
                .catch(error => console.error('Error checking alerts:', error));
            }, 60000);
        }

        // Start polling when page loads
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', startNotificationPolling);
        } else {
            startNotificationPolling();
        }
    </script>
    @endif

</body>
</html>
