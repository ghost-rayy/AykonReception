@extends('layouts.app')

@section('title', 'Reception Dashboard')

@section('content')

<style>
    .dash-card {
        transition: transform .2s, box-shadow .2s;
        cursor: pointer;
    }
    .dash-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 22px rgba(0,0,0,0.15);
    }
    .stat-number {
        font-size: 32px;
        font-weight: 700;
    }
    .quick-btn:hover {
        transform: translateY(-3px);
    }
</style>

<div class="container mt-4">

    {{-- Cards Row --}}
    <div class="row g-4">

        <div class="col-md-3">
            <div class="card dash-card shadow-sm border-0 p-3 text-center">
                <h6 class="text-muted">Today's Check-ins</h6>
                <p class="stat-number text-primary" id="checkins">{{ $checkedIn }}</p>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card dash-card shadow-sm border-0 p-3 text-center">
                <h6 class="text-muted">Pending Appointments</h6>
                <p class="stat-number text-warning" id="pending">{{ $pendingAppointments }}</p>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card dash-card shadow-sm border-0 p-3 text-center">
                <h6 class="text-muted">New Messages</h6>
                <p class="stat-number text-success" id="messages">{{ $newMessages }}</p>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card dash-card shadow-sm border-0 p-3 text-center">
                <h6 class="text-muted">Visitors Today</h6>
                <p class="stat-number text-danger" id="visitors">{{ $visitorsToday }}</p>
            </div>
        </div>

    </div>

    {{-- Quick Actions --}}
    <div class="mt-5">
        <h5 class="fw-bold">Quick Actions</h5>
        <div class="d-flex gap-3 mt-3 flex-wrap">
            <a href="{{ route('visitors.index') }}" class="btn btn-primary shadow quick-btn px-4 py-2">+ Register Visitor</a>
            <a href="{{ route('appointments.index') }}" class="btn btn-outline-primary shadow quick-btn px-4 py-2">View Appointments</a>
            <a href="#" class="btn btn-outline-success shadow quick-btn px-4 py-2">Send Message</a>
            <a href="{{ route('visitors.index') }}" class="btn btn-outline-dark shadow quick-btn px-4 py-2">Check-in List</a>
        </div>
    </div>

    {{-- Main Row --}}
    <div class="row mt-5">

        {{-- Recent Activity Table --}}
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header fw-bold">Recent Activity</div>
                <div class="card-body p-0">

                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Visitor</th>
                                <th>Purpose</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentVisitors as $visitor)
                                <tr>
                                    <td>{{ $visitor->name }}</td>
                                    <td>{{ $visitor->purpose }}</td>
                                    <td>{{ $visitor->created_at->format('H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-muted text-center">No recent visitors today</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                </div>
            </div>
        </div>

        {{-- Notifications --}}
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-header fw-bold">Notifications</div>
                <div class="card-body">

                    <div class="alert alert-info py-2">New appointment scheduled.</div>
                    <div class="alert alert-warning py-2">Visitor waiting at reception.</div>
                    <div class="alert alert-success py-2">Message sent successfully.</div>

                </div>
            </div>
        </div>

    </div>

</div>

{{-- Simple Counter Animation --}}
<script>
    function animateValue(id, start, end, duration) {
        if (start === end) {
            document.getElementById(id).textContent = end;
            return;
        }
        let obj = document.getElementById(id);
        let range = end - start;
        let stepTime = Math.abs(Math.floor(duration / range));
        let current = start;
        let timer = setInterval(function() {
            current++;
            obj.textContent = current;
            if (current == end) clearInterval(timer);
        }, stepTime);
    }

    // Animate the stats with real data
    animateValue("checkins", 0, {{ $checkedIn }}, 800);
    animateValue("pending", 0, {{ $pendingAppointments }}, 800);
    animateValue("messages", 0, {{ $newMessages }}, 800);
    animateValue("visitors", 0, {{ $visitorsToday }}, 800);
</script>

@endsection
