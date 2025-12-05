@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')

<style>
    .dash-card {
        border: none;
        border-radius: 12px;
        padding: 25px 20px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        background: #fff;
        height: 100%;
    }
    .dash-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.12);
    }
    .dash-icon {
        font-size: 32px;
        opacity: 0.85;
        margin-bottom: 8px;
        color: #0d1b2a;
    }
    .section-header {
        background: #0d1b2a;
        color: white;
        border-radius: 12px 12px 0 0;
        padding: 18px 20px;
    }
    .stat-number {
        font-size: 28px;
        font-weight: 700;
        color: #0d1b2a;
        margin: 0;
    }
    .stat-label {
        font-size: 13px;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }
</style>

<div class="row g-4">

    <div class="col-md-4">
        <a href="{{ route('staff.index') }}" class="text-decoration-none text-dark">
            <div class="dash-card shadow-sm text-center">
                <div class="dash-icon"><i class="bi bi-people-fill"></i></div>
                <div class="stat-label">Total Staff</div>
                <p class="stat-number">{{ \App\Models\Staff::count() }}</p>
            </div>
        </a>
    </div>

    <div class="col-md-4">
        <a href="{{ route('visitors.index') }}" class="text-decoration-none text-dark">
            <div class="dash-card shadow-sm text-center">
                <div class="dash-icon"><i class="bi bi-person-check-fill"></i></div>
                <div class="stat-label">Today's Visitors</div>
                <p class="stat-number">{{ \App\Models\Visitors::whereDate('created_at', today())->count() }}</p>
            </div>
        </a>
    </div>

    <div class="col-md-4">
        <a href="{{ route('visitors.index') }}" class="text-decoration-none text-dark">
            <div class="dash-card shadow-sm text-center">
                <div class="dash-icon"><i class="bi bi-door-open-fill"></i></div>
                <div class="stat-label">Currently Inside</div>
                <p class="stat-number">{{ \App\Models\Visitors::whereNull('check_out_time')->count() }}</p>
            </div>
        </a>
    </div>

    <div class="col-md-4">
        <a href="{{ route('appointments.index') }}" class="text-decoration-none text-dark">
            <div class="dash-card shadow-sm text-center">
                <div class="dash-icon"><i class="bi bi-calendar-event-fill"></i></div>
                <div class="stat-label">Upcoming Appointments</div>
                <p class="stat-number">{{ \App\Models\Appointments::where('appointment_time', '>', now())->count() }}</p>
            </div>
        </a>
    </div>

    <div class="col-md-4">
        <a href="{{ route('appointments.index') }}" class="text-decoration-none text-dark">
            <div class="dash-card shadow-sm text-center">
                <div class="dash-icon"><i class="bi bi-hourglass-split"></i></div>
                <div class="stat-label">Pending Appointments</div>
                <p class="stat-number">{{ \App\Models\Appointments::where('status', 'pending')->count() }}</p>
            </div>
        </a>
    </div>

    <div class="col-md-4">
        <a href="{{ route('appointments.index') }}" class="text-decoration-none text-dark">
            <div class="dash-card shadow-sm text-center">
                <div class="dash-icon"><i class="bi bi-list-check"></i></div>
                <div class="stat-label">Total Appointments</div>
                <p class="stat-number">{{ \App\Models\Appointments::count() }}</p>
            </div>
        </a>
    </div>

    <div class="col-md-4">
        <div class="dash-card shadow-sm text-center">
            <div class="dash-icon"><i class="bi bi-person-badge-fill"></i></div>
            <div class="stat-label">Total Users</div>
            <p class="stat-number">{{ \App\Models\User::count() }}</p>
        </div>
    </div>

    <div class="col-md-4">
        <div class="dash-card shadow-sm text-center">
            <div class="dash-icon"><i class="bi bi-person-fill-check"></i></div>
            <div class="stat-label">Receptionists</div>
            <p class="stat-number">{{ \App\Models\User::where('role', 'receptionist')->count() }}</p>
        </div>
    </div>

    <div class="col-md-4">
        <div class="dash-card shadow-sm text-center">
            <div class="dash-icon"><i class="bi bi-shield-fill"></i></div>
            <div class="stat-label">Admins</div>
            <p class="stat-number">{{ \App\Models\User::where('role', 'admin')->count() }}</p>
        </div>
    </div>

</div>

{{-- Recent Activity --}}
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card shadow-sm border-0">
            <div class="section-header">
                <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Recent Activity</h5>
            </div>
            <div class="card-body">
                <p class="text-muted mb-0">Admin dashboard with overview of all system activities.</p>
            </div>
        </div>
    </div>
</div>

@endsection
