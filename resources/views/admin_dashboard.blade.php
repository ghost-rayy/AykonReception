@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold">Admin Dashboard</h3>

    {{-- Logged-in user --}}
    <div class="d-flex align-items-center gap-3">
        <div class="text-end">
            <small class="text-muted">Logged in as</small><br>
            <strong>{{ auth()->user()->name }}</strong>
        </div>
        <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=0D8ABC&color=fff&size=45"
             class="rounded-circle shadow-sm" alt="user">
    </div>
</div>

<style>
    .dash-card {
        border: none;
        border-radius: 16px;
        padding: 25px 20px;
        transition: transform .25s ease, box-shadow .25s ease;
        background: #fff;
    }
    .dash-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.12);
    }
    .dash-icon {
        font-size: 35px;
        opacity: .85;
        margin-bottom: 5px;
    }
    .section-header {
        background: linear-gradient(135deg, #ff6b35, #f7931e);
        color: white;
        border-radius: 16px 16px 0 0;
        padding: 18px;
    }
</style>

<div class="row g-4">

    <div class="col-md-4">
        <a href="{{ route('staff.index') }}" class="text-decoration-none text-dark">
            <div class="dash-card shadow-sm text-center">
                <div class="dash-icon"><i class="bi bi-people-fill"></i></div>
                <h6 class="text-muted mb-1">Total Staff</h6>
                <h2 class="fw-bold">{{ \App\Models\Staff::count() }}</h2>
            </div>
        </a>
    </div>

    <div class="col-md-4">
        <a href="{{ route('visitors.index') }}" class="text-decoration-none text-dark">
            <div class="dash-card shadow-sm text-center">
                <div class="dash-icon"><i class="bi bi-person-check-fill"></i></div>
                <h6 class="text-muted mb-1">Today's Visitors</h6>
                <h2 class="fw-bold">{{ \App\Models\Visitors::whereDate('created_at', today())->count() }}</h2>
            </div>
        </a>
    </div>

    <div class="col-md-4">
        <a href="{{ route('visitors.index') }}" class="text-decoration-none text-dark">
            <div class="dash-card shadow-sm text-center">
                <div class="dash-icon"><i class="bi bi-door-open-fill"></i></div>
                <h6 class="text-muted mb-1">Currently Inside</h6>
                <h2 class="fw-bold">{{ \App\Models\Visitors::whereNull('check_out_time')->count() }}</h2>
            </div>
        </a>
    </div>

    <div class="col-md-4">
        <a href="{{ route('appointments.index') }}" class="text-decoration-none text-dark">
            <div class="dash-card shadow-sm text-center">
                <div class="dash-icon"><i class="bi bi-calendar-event-fill"></i></div>
                <h6 class="text-muted mb-1">Upcoming Appointments</h6>
                <h2 class="fw-bold">{{ \App\Models\Appointments::where('appointment_time', '>', now())->count() }}</h2>
            </div>
        </a>
    </div>

    <div class="col-md-4">
        <a href="{{ route('appointments.index') }}" class="text-decoration-none text-dark">
            <div class="dash-card shadow-sm text-center">
                <div class="dash-icon"><i class="bi bi-hourglass-split"></i></div>
                <h6 class="text-muted mb-1">Pending Appointments</h6>
                <h2 class="fw-bold">{{ \App\Models\Appointments::where('status', 'pending')->count() }}</h2>
            </div>
        </a>
    </div>

    <div class="col-md-4">
        <a href="{{ route('appointments.index') }}" class="text-decoration-none text-dark">
            <div class="dash-card shadow-sm text-center">
                <div class="dash-icon"><i class="bi bi-list-check"></i></div>
                <h6 class="text-muted mb-1">Total Appointments</h6>
                <h2 class="fw-bold">{{ \App\Models\Appointments::count() }}</h2>
            </div>
        </a>
    </div>

    <div class="col-md-4">
        <div class="dash-card shadow-sm text-center">
            <div class="dash-icon"><i class="bi bi-person-badge-fill"></i></div>
            <h6 class="text-muted mb-1">Total Users</h6>
            <h2 class="fw-bold">{{ \App\Models\User::count() }}</h2>
        </div>
    </div>

    <div class="col-md-4">
        <div class="dash-card shadow-sm text-center">
            <div class="dash-icon"><i class="bi bi-person-fill-check"></i></div>
            <h6 class="text-muted mb-1">Receptionists</h6>
            <h2 class="fw-bold">{{ \App\Models\User::where('role', 'receptionist')->count() }}</h2>
        </div>
    </div>

    <div class="col-md-4">
        <div class="dash-card shadow-sm text-center">
            <div class="dash-icon"><i class="bi bi-shield-fill"></i></div>
            <h6 class="text-muted mb-1">Admins</h6>
            <h2 class="fw-bold">{{ \App\Models\User::where('role', 'admin')->count() }}</h2>
        </div>
    </div>

</div>

{{-- Recent Activity --}}
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card shadow-sm border-0">
            <div class="section-header">
                <h5 class="mb-0">Recent Activity</h5>
            </div>
            <div class="card-body">
                <p class="text-muted">Admin dashboard with overview of all system activities.</p>
                <!-- Add more admin-specific content here -->
            </div>
        </div>
    </div>
</div>

@endsection
