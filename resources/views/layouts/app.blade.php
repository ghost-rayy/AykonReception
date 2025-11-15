<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aykon Reception</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
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
            background: rgba(0,212,255,0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 5px;
            box-shadow: 0 4px 12px rgba(0,212,255,0.4);
        }
        .sidebar-logo-icon img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        .sidebar-logo-text {
            font-size: 26px;
            font-weight: 700;
            background: linear-gradient(135deg, #00d4ff, #0099ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
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
            border: 2px solid #0099ff;
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
                <a href="{{ route('admin_dashboard') }}" class="{{ request()->routeIs('admin_dashboard') ? 'active' : '' }}">Admin Dashboard</a>
            @elseif(auth()->user()->role === 'staff')
                <a href="{{ route('staff_dashboard') }}" class="{{ request()->routeIs('staff_dashboard') ? 'active' : '' }}">Staff Dashboard</a>
            @else
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>
            @endif

            @if(in_array(auth()->user()->role, ['admin', 'receptionist']))
                <a href="{{ route('staff.index') }}" class="{{ request()->routeIs('staff.*') ? 'active' : '' }}">Staff</a>
                <a href="{{ route('visitors.index') }}" class="{{ request()->routeIs('visitors.*') ? 'active' : '' }}">Visitors</a>
                <a href="{{ route('appointments.index')}}" class="{{ request()->routeIs('appointments.*') ? 'active' : '' }}">Appointments</a>
            @endif

            @if(in_array(auth()->user()->role, ['receptionist']))
                <a href="{{ route('messages.index') }}" class="{{ request()->routeIs('messages.*') ? 'active' : '' }}">Messages</a>
            @endif
        @endauth
    </div>


    <!-- MAIN CONTENT -->
    <div class="content">

        <!-- TOPBAR -->
        <div class="topbar">

            <h4 class="fw-bold m-0">@yield('title')</h4>

            @auth
            <div class="dropdown">
                <div class="user-box dropdown-toggle" data-bs-toggle="dropdown">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=0099ff&color=fff">
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

</body>
</html>
