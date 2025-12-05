@extends('layouts.app')

@section('title', 'Reception Dashboard')

@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')

<style>
    /* Page Background */
    body {
        background: linear-gradient(135deg, #f5f7fa 0%, #e8ecf1 100%);
        min-height: 100vh;
    }

    /* Dashboard Container */
    .dashboard-wrapper {
        padding: 40px 0 60px;
        animation: fadeIn 0.6s ease-in;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Container max-width for better readability */
    .dashboard-wrapper .container {
        max-width: 1400px;
    }

    /* Stat Cards */
    .stat-card {
        background: white;
        border: none;
        border-radius: 16px;
        padding: 28px 24px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        height: 100%;
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #0d1b2a, #1a2f47);
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
    }

    .stat-card:hover::before {
        transform: scaleX(1);
    }

    .stat-card .stat-icon {
        width: 56px;
        height: 56px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 20px;
        font-size: 26px;
        color: white;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .stat-card.primary .stat-icon {
        background: linear-gradient(135deg, #0d1b2a 0%, #1a2f47 100%);
    }

    .stat-card.warning .stat-icon {
        background: linear-gradient(135deg, #ff9800 0%, #ffb74d 100%);
    }

    .stat-label {
        font-size: 13px;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        margin-bottom: 10px;
    }

    .stat-number {
        font-size: 36px;
        font-weight: 700;
        color: #1f2937;
        margin: 0;
        line-height: 1.2;
        letter-spacing: -0.5px;
    }

    /* Main Dashboard Layout */
    .dashboard-main {
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 28px;
        margin-top: 32px;
    }

    @media (max-width: 1200px) {
        .dashboard-main {
            grid-template-columns: 1fr;
            gap: 24px;
        }
    }

    /* Responsive improvements */
    @media (max-width: 768px) {
        .queue-item {
            grid-template-columns: 1fr;
            gap: 12px;
        }

        .view-controls {
            flex-wrap: wrap;
        }

        .search-input-custom {
            width: 100%;
            margin-top: 12px;
        }

        .stat-card {
            margin-bottom: 16px;
        }
    }

    /* Card Styling */
    .dashboard-card {
        background: white;
        border-radius: 16px;
        padding: 28px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        border: none;
        margin-bottom: 24px;
        transition: all 0.3s ease;
    }

    .dashboard-card:hover {
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }

    .card-header-custom {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        padding-bottom: 20px;
        border-bottom: 2px solid #f3f4f6;
    }

    .card-title {
        font-size: 20px;
        font-weight: 700;
        color: #1f2937;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .card-title i {
        color: #0d1b2a;
        font-size: 22px;
    }

    /* Tabs */
    .custom-tabs {
        display: flex;
        gap: 8px;
        margin-bottom: 20px;
        border-bottom: 2px solid #f3f4f6;
    }

    .custom-tab {
        padding: 12px 20px;
        background: transparent;
        border: none;
        border-bottom: 3px solid transparent;
        font-weight: 600;
        font-size: 14px;
        color: #6b7280;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
        top: 2px;
    }

    .custom-tab.active {
        color: #0d1b2a;
        border-bottom-color: #0d1b2a;
    }

    .custom-tab:hover {
        color: #0d1b2a;
    }

    /* Queue List */
    .queue-list {
        max-height: 600px;
        overflow-y: auto;
        padding-right: 8px;
    }

    .queue-list::-webkit-scrollbar {
        width: 6px;
    }

    .queue-list::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .queue-list::-webkit-scrollbar-thumb {
        background: #0d1b2a;
        border-radius: 10px;
    }

    .queue-item {
        display: grid;
        grid-template-columns: 2fr 2fr 1fr 1fr auto;
        gap: 20px;
        padding: 20px;
        border-bottom: 1px solid #f3f4f6;
        align-items: center;
        transition: all 0.2s ease;
        border-radius: 8px;
        margin-bottom: 4px;
    }

    .queue-item:hover {
        background: #f9fafb;
        transform: translateX(4px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .queue-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
    }

    .patient-name {
        font-weight: 600;
        color: #1f2937;
        font-size: 15px;
        display: flex;
        align-items: center;
    }

    .patient-name i {
        margin-right: 8px;
    }

    .staff-name {
        color: #4b5563;
        font-size: 14px;
    }

    .time-badge {
        display: flex;
        align-items: center;
        gap: 6px;
        color: #6b7280;
        font-size: 14px;
    }

    .status-badge {
        padding: 8px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
        letter-spacing: 0.3px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .status-pending {
        background: #fce7f3;
        color: #be185d;
    }

    .status-followup {
        background: #d1fae5;
        color: #065f46;
    }

    .status-firstvisit {
        background: #dbeafe;
        color: #1e40af;
    }

    .status-completed {
        background: #f3f4f6;
        color: #374151;
    }

    /* Calendar Widget */
    .calendar-widget {
        background: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        margin-bottom: 24px;
        transition: all 0.3s ease;
    }

    .calendar-widget:hover {
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }

    .calendar-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
    }

    .calendar-month {
        font-size: 16px;
        font-weight: 700;
        color: #1f2937;
    }

    .calendar-nav {
        display: flex;
        gap: 8px;
    }

    .calendar-nav-btn {
        width: 32px;
        height: 32px;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .calendar-nav-btn:hover {
        background: #f9fafb;
        border-color: #0d1b2a;
    }

    .calendar-weekdays {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 4px;
        margin-bottom: 8px;
    }

    .calendar-weekday {
        text-align: center;
        font-size: 12px;
        font-weight: 600;
        color: #6b7280;
        padding: 8px;
    }

    .calendar-days {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 4px;
    }

    .calendar-day {
        aspect-ratio: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s ease;
        color: #4b5563;
    }

    .calendar-day:hover {
        background: #f3f4f6;
    }

    .calendar-day.other-month {
        color: #d1d5db;
    }

    .calendar-day.today {
        background: #0d1b2a;
        color: white;
        font-weight: 700;
    }

    .calendar-day.has-appointment {
        background: #10b981;
        color: white;
        font-weight: 600;
        position: relative;
    }

    .calendar-day.has-appointment.today {
        background: linear-gradient(135deg, #0d1b2a 0%, #10b981 100%);
        box-shadow: 0 0 0 2px #10b981;
    }

    .calendar-day.has-appointment:hover {
        background: #059669;
        transform: scale(1.05);
    }

    /* Tooltip styles */
    .calendar-day {
        position: relative;
    }

    .calendar-tooltip {
        position: absolute;
        bottom: calc(100% + 10px);
        left: 50%;
        transform: translateX(-50%);
        background: #1f2937;
        color: white;
        padding: 10px 14px;
        border-radius: 8px;
        font-size: 12px;
        z-index: 1000;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.2s ease, transform 0.2s ease;
        max-width: 220px;
        min-width: 150px;
        white-space: normal;
        line-height: 1.5;
    }

    .calendar-tooltip::after {
        content: '';
        position: absolute;
        top: 100%;
        left: 50%;
        transform: translateX(-50%);
        border: 6px solid transparent;
        border-top-color: #1f2937;
    }

    .calendar-day.has-appointment:hover .calendar-tooltip {
        opacity: 1;
        transform: translateX(-50%) translateY(-4px);
    }

    .tooltip-title {
        font-weight: 600;
        margin-bottom: 4px;
        color: #fff;
    }

    .tooltip-text {
        color: #d1d5db;
        font-size: 11px;
    }

    /* Reminders */
    .reminders-list {
        max-height: 500px;
        overflow-y: auto;
        padding-right: 8px;
    }

    .reminders-list::-webkit-scrollbar {
        width: 6px;
    }

    .reminders-list::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .reminders-list::-webkit-scrollbar-thumb {
        background: #0d1b2a;
        border-radius: 10px;
    }

    .reminder-item {
        background: linear-gradient(135deg, #f9fafb 0%, #ffffff 100%);
        border-radius: 12px;
        padding: 18px;
        margin-bottom: 12px;
        border-left: 4px solid #0d1b2a;
        transition: all 0.2s ease;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .reminder-item:hover {
        transform: translateX(4px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .reminder-patient {
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 6px;
        font-size: 14px;
    }

    .reminder-message {
        color: #6b7280;
        font-size: 13px;
        margin-bottom: 10px;
    }

    .reminder-action {
        display: inline-block;
        padding: 6px 12px;
        background: #0d1b2a;
        color: white;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .reminder-action:hover {
        background: #1a2f47;
        color: white;
    }

    /* News Card */
    .news-card {
        background: linear-gradient(135deg, #0d1b2a 0%, #1a2f47 100%);
        color: white;
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: 0 4px 16px rgba(13, 27, 42, 0.3);
        transition: all 0.3s ease;
    }

    .news-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 24px rgba(13, 27, 42, 0.4);
    }

    .news-title {
        font-size: 16px;
        font-weight: 700;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .news-content {
        font-size: 14px;
        opacity: 0.9;
        line-height: 1.6;
    }

    /* Search and View Controls */
    .view-controls {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .view-icon {
        width: 36px;
        height: 36px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
        background: white;
    }

    .view-icon:hover {
        background: #f9fafb;
        border-color: #0d1b2a;
    }

    .view-icon.active {
        background: #0d1b2a;
        color: white;
        border-color: #0d1b2a;
    }

    .search-input-custom {
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 10px 16px;
        font-size: 14px;
        width: 220px;
        transition: all 0.2s ease;
    }

    .search-input-custom:focus {
        outline: none;
        border-color: #0d1b2a;
        box-shadow: 0 0 0 3px rgba(13, 27, 42, 0.1);
        width: 250px;
    }

    /* Staff Status */
    .staff-status-container {
        max-height: 400px;
        overflow-y: auto;
        padding-right: 8px;
    }

    .staff-status-item {
        padding: 14px 18px;
        border-radius: 10px;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 14px;
        font-weight: 600;
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .staff-status-item:hover {
        transform: translateX(4px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .staff-status-item.active {
        background: #d1fae5;
        color: #065f46;
    }

    .staff-status-item.busy {
        background: #fee2e2;
        color: #991b1b;
    }
</style>

<div class="dashboard-wrapper">
    <div class="container">

        {{-- Page Header --}}
        <div class="mb-4">
            <!-- <h1 class="mb-2" style="font-size: 32px; font-weight: 700; color: #1f2937;">
                <i class="bi bi-speedometer2 me-2" style="color: #0d1b2a;"></i>Dashboard
            </h1> -->
            <p class="text-muted mb-0" style="font-size: 15px;">Welcome back! Here's what's happening today.</p>
        </div>

        {{-- Top Stats Cards --}}
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="stat-card primary">
                    <div class="stat-icon">
                        <i class="bi bi-calendar-event"></i>
                    </div>
                    <div class="stat-label">Total Appointments</div>
                    <p class="stat-number" id="total-appointments">{{ $totalAppointments }}</p>
                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card primary">
                    <div class="stat-icon">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div class="stat-label">Total Staff</div>
                    <p class="stat-number" id="total-staff">{{ $totalStaff }}</p>
                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card warning">
                    <div class="stat-icon">
                        <i class="bi bi-person-check-fill"></i>
                    </div>
                    <div class="stat-label">Today's Check-ins</div>
                    <p class="stat-number" id="checkins">{{ $checkedIn }}</p>
                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card warning">
                    <div class="stat-icon">
                        <i class="bi bi-calendar-x-fill"></i>
                    </div>
                    <div class="stat-label">Pending Appointments</div>
                    <p class="stat-number" id="pending">{{ $pendingAppointments }}</p>
                </div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="dashboard-card" style="margin-top: 32px; margin-bottom: 32px;">
            <div class="card-header-custom">
                <h5 class="card-title">
                    <i class="bi bi-lightning-charge"></i>
                    Quick Actions
            </h5>
            </div>
            <div class="d-flex gap-3 flex-wrap">
                <button class="btn btn-primary quick-action-btn" data-bs-toggle="modal" data-bs-target="#createVisitorModal">
                    <i class="bi bi-person-plus me-2"></i>Register Visitor
                </button>
                <button class="btn btn-primary quick-action-btn" data-bs-toggle="modal" data-bs-target="#createAppointmentModal">
                    <i class="bi bi-calendar-plus me-2"></i>Create Appointment
                </button>
                <a href="{{ route('visitors.index') }}" class="btn btn-outline-primary quick-action-btn">
                    <i class="bi bi-list-check me-2"></i>View All Visitors
                </a>
                <a href="{{ route('appointments.index') }}" class="btn btn-outline-primary quick-action-btn">
                    <i class="bi bi-calendar-event me-2"></i>View Appointments
                </a>
                @if(in_array(auth()->user()->role, ['receptionist', 'staff']))
                    <a href="{{ route('messages.index') }}" class="btn btn-outline-primary quick-action-btn position-relative">
                        <i class="bi bi-chat-dots me-2"></i>Messages
                        @php
                            $unreadCount = \App\Models\Message::where('receiver_id', auth()->id())
                                ->where('is_read', false)
                                ->count();
                        @endphp
                        @if($unreadCount > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="dashboard-messages-badge">
                                {{ $unreadCount }}
                            </span>
                        @endif
                    </a>
                @endif
            </div>
        </div>

        {{-- Main Dashboard Layout --}}
        <div class="dashboard-main">
            {{-- Left Column --}}
            <div>
                {{-- In Queue / Completed Section --}}
                <div class="dashboard-card">
                    <div class="card-header-custom">
                        <div class="custom-tabs">
                            <button class="custom-tab active" data-tab="queue">
                                In Queue (<span id="queue-count">{{ $visitorsInQueue->count() }}</span>)
                            </button>
                            <button class="custom-tab" data-tab="completed">
                                Completed (<span id="completed-count">{{ $completedVisitors->count() }}</span>)
                            </button>
                        </div>
                        <div class="view-controls">
                            <!-- <div class="view-icon active" data-view="list">
                                <i class="bi bi-list"></i>
                            </div> -->
                            <!-- <div class="view-icon" data-view="grid">
                                <i class="bi bi-grid"></i>
                            </div> -->
                            <input type="text" class="search-input-custom" placeholder="Search..." id="queue-search">
                        </div>
                    </div>

                    {{-- Queue Tab Content --}}
                    <div id="queue-content" class="tab-content">
                        <div class="queue-list">
                            @forelse($visitorsInQueue as $visitor)
                                <div class="queue-item" data-name="{{ strtolower($visitor->name) }}" data-staff="{{ strtolower($visitor->user->name ?? '') }}">
                                    <div class="patient-name">
                                    <i class="bi bi-person-circle me-2 text-primary"></i>
                                        {{ $visitor->name }}
                                    </div>
                                    <div class="staff-name">
                                        <i class="bi bi-person-badge me-2 text-muted"></i>
                                        {{ $visitor->user->name ?? 'Unassigned' }}
                                    </div>
                                    <div class="time-badge">
                                        <i class="bi bi-clock"></i>
                                        {{ $visitor->created_at->utc()->format('h:i A') }} GMT
                                    </div>
                                    <div>
                                        <span class="status-badge status-pending">Pending</span>
                                    </div>
                                    <!-- <div>
                                        <i class="bi bi-three-dots-vertical text-muted" style="cursor: pointer;"></i>
                                    </div> -->
                                </div>
                        @empty
                                <div class="text-center text-muted py-5">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    <p>No visitors in queue</p>
                                </div>
                        @endforelse
                        </div>
                    </div>

                    {{-- Completed Tab Content --}}
                    <div id="completed-content" class="tab-content" style="display: none;">
                        <div class="queue-list">
                            @forelse($completedVisitors as $visitor)
                                <div class="queue-item" data-name="{{ strtolower($visitor->name) }}" data-staff="{{ strtolower($visitor->user->name ?? '') }}">
                                    <div class="patient-name">
                                        <i class="bi bi-person-circle me-2 text-success"></i>
                                        {{ $visitor->name }}
                                    </div>
                                    <div class="staff-name">
                                        <i class="bi bi-person-badge me-2 text-muted"></i>
                                        {{ $visitor->user->name ?? 'Unassigned' }}
                                    </div>
                                    <div class="time-badge">
                                        <i class="bi bi-clock"></i>
                                        {{ $visitor->check_out_time->utc()->format('h:i A') }} GMT
                                    </div>
                                    <div>
                                        <span class="status-badge status-completed">Completed</span>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center text-muted py-5">
                                    <i class="bi bi-check-circle fs-1 d-block mb-2"></i>
                                    <p>No completed visits today</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column --}}
            <div>
                {{-- News Card --}}
                <!-- <div class="news-card">
                    <div class="news-title">
                        <i class="bi bi-calendar-event"></i>
                        News from Staff
                    </div>
                    <div class="news-content">
                        @if($staffInMeeting > 0)
                            {{ $staffInMeeting }} staff member(s) currently in meeting
                        @else
                            All staff members are available
                        @endif
                    </div>
                </div> -->

                {{-- Calendar Widget --}}
                <div class="calendar-widget">
                    <div class="calendar-header">
                        <div class="calendar-month" id="calendar-month">
                            {{ now()->utc()->format('F Y') }}
                        </div>
                        <div class="calendar-nav">
                            <button class="calendar-nav-btn" id="prev-month">
                                <i class="bi bi-chevron-left"></i>
                            </button>
                            <button class="calendar-nav-btn" id="next-month">
                                <i class="bi bi-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                    <div class="calendar-weekdays">
                        <div class="calendar-weekday">Mo</div>
                        <div class="calendar-weekday">Tu</div>
                        <div class="calendar-weekday">We</div>
                        <div class="calendar-weekday">Th</div>
                        <div class="calendar-weekday">Fr</div>
                        <div class="calendar-weekday">Sa</div>
                        <div class="calendar-weekday">Su</div>
                    </div>
                    <div class="calendar-days" id="calendar-days">
                        <!-- Calendar days will be generated by JavaScript -->
                    </div>
                </div>

                {{-- Staff Status --}}
                <div class="dashboard-card">
                    <div class="card-header-custom">
                        <h5 class="card-title">
                            <i class="bi bi-people-fill"></i>
                            Staff Status
                        </h5>
                        <small id="staff-status-last-update" class="text-muted">
                    <i class="bi bi-arrow-clockwise"></i> <span>Updating...</span>
                </small>
            </div>
                    <div class="staff-status-container" id="all-staff-container">
                @forelse($allStaff as $staff)
                            <div class="staff-status-item {{ $staff->status === 'busy' ? 'busy' : 'active' }}">
                        <i class="bi {{ $staff->status === 'busy' ? 'bi-calendar-x-fill' : 'bi-check-circle-fill' }}"></i>
                        <span>{{ $staff->name }}</span>
                    </div>
                @empty
                            <div class="text-muted text-center py-3">
                        <i class="bi bi-people me-2"></i>No staff found
                    </div>
                @endforelse
            </div>
        </div>

                {{-- Reminders --}}
                <!-- <div class="dashboard-card">
                    <div class="card-header-custom">
                        <h5 class="card-title">
                            <i class="bi bi-bell-fill"></i>
                            Reminders
                        </h5>
                    </div>
                    <div class="reminders-list">
                        @if($pendingAppointments > 0)
                            <div class="reminder-item">
                                <div class="reminder-patient">Appointment Reminder</div>
                                <div class="reminder-message">
                                    {{ $pendingAppointments }} pending appointment(s) require attention
                                </div>
                                <a href="{{ route('appointments.index') }}" class="reminder-action">
                                    View Appointments
                                </a>
                            </div>
                        @endif
                        @if($visitorsInQueue->count() > 0)
                            <div class="reminder-item">
                                <div class="reminder-patient">Visitor Queue</div>
                                <div class="reminder-message">
                                    {{ $visitorsInQueue->count() }} visitor(s) waiting in queue
                                </div>
                                <a href="{{ route('visitors.index') }}" class="reminder-action">
                                    View Queue
                                </a>
                            </div>
                        @endif
                        @if($pendingAppointments == 0 && $visitorsInQueue->count() == 0)
                            <div class="text-center text-muted py-4">
                                <i class="bi bi-bell-slash fs-1 d-block mb-2"></i>
                                <p>No reminders</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div> -->
    </div>

    </div>
</div>

<script>
    // Tab switching
    document.querySelectorAll('.custom-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            const tabName = this.dataset.tab;
            
            // Update tab states
            document.querySelectorAll('.custom-tab').forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            
            // Update content visibility
            document.getElementById('queue-content').style.display = tabName === 'queue' ? 'block' : 'none';
            document.getElementById('completed-content').style.display = tabName === 'completed' ? 'block' : 'none';
        });
    });

    // Search functionality
    document.getElementById('queue-search').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const activeTab = document.querySelector('.custom-tab.active').dataset.tab;
        const container = document.getElementById(activeTab + '-content');
        
        container.querySelectorAll('.queue-item').forEach(item => {
            const name = item.dataset.name || '';
            const staff = item.dataset.staff || '';
            
            if (name.includes(searchTerm) || staff.includes(searchTerm)) {
                item.style.display = 'grid';
            } else {
                item.style.display = 'none';
            }
        });
    });

    // Calendar functionality - Using UTC/GMT
    function getUTCDate() {
        const now = new Date();
        return new Date(Date.UTC(now.getUTCFullYear(), now.getUTCMonth(), now.getUTCDate()));
    }
    
    // Appointments data from server
    const appointmentsData = @json($allAppointments ?? []);
    
    // Create a map of dates to appointments for quick lookup
    const appointmentsByDate = {};
    appointmentsData.forEach(appointment => {
        const date = appointment.date;
        if (!appointmentsByDate[date]) {
            appointmentsByDate[date] = [];
        }
        appointmentsByDate[date].push(appointment);
    });
    
    // Initialize with current UTC date
    let currentDate = getUTCDate();
    
    function formatDateKey(year, month, day) {
        // Format as YYYY-MM-DD for comparison
        const monthStr = String(month + 1).padStart(2, '0');
        const dayStr = String(day).padStart(2, '0');
        return `${year}-${monthStr}-${dayStr}`;
    }
    
    function renderCalendar() {
        const year = currentDate.getUTCFullYear();
        const month = currentDate.getUTCMonth();
        
        const firstDay = new Date(Date.UTC(year, month, 1));
        const lastDay = new Date(Date.UTC(year, month + 1, 0));
        const daysInMonth = lastDay.getUTCDate();
        const startingDayOfWeek = firstDay.getUTCDay();
        
        const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 
                          'July', 'August', 'September', 'October', 'November', 'December'];
        
        document.getElementById('calendar-month').textContent = `${monthNames[month]} ${year}`;
        
        const calendarDays = document.getElementById('calendar-days');
        calendarDays.innerHTML = '';
        
        // Add empty cells for days before the first day of the month
        // Adjust for Monday as first day (0 = Sunday, so we shift)
        const adjustedStartDay = startingDayOfWeek === 0 ? 6 : startingDayOfWeek - 1;
        for (let i = 0; i < adjustedStartDay; i++) {
            const emptyDay = document.createElement('div');
            emptyDay.className = 'calendar-day other-month';
            calendarDays.appendChild(emptyDay);
        }
        
        // Add days of the month
        const today = getUTCDate();
        for (let day = 1; day <= daysInMonth; day++) {
            const dayElement = document.createElement('div');
            dayElement.className = 'calendar-day';
            dayElement.textContent = day;
            
            const dateKey = formatDateKey(year, month, day);
            const dayAppointments = appointmentsByDate[dateKey] || [];
            
            // Highlight today using UTC - only if viewing current month
            if (year === today.getUTCFullYear() && month === today.getUTCMonth() && day === today.getUTCDate()) {
                dayElement.classList.add('today');
            }
            
            // Highlight dates with appointments
            if (dayAppointments.length > 0) {
                dayElement.classList.add('has-appointment');
                
                // Create tooltip with appointment details
                const tooltip = document.createElement('div');
                tooltip.className = 'calendar-tooltip';
                
                let tooltipContent = '';
                dayAppointments.forEach((apt, index) => {
                    if (index > 0) tooltipContent += '<br>';
                    tooltipContent += `<div class="tooltip-title">${apt.visitor_name || 'Visitor'}</div>`;
                    if (apt.purpose) {
                        tooltipContent += `<div class="tooltip-text">${apt.purpose}</div>`;
                    }
                    if (apt.time) {
                        tooltipContent += `<div class="tooltip-text">Time: ${apt.time}</div>`;
                    }
                });
                
                tooltip.innerHTML = tooltipContent;
                dayElement.appendChild(tooltip);
            }
            
            calendarDays.appendChild(dayElement);
        }
    }
    
    document.getElementById('prev-month').addEventListener('click', function() {
        currentDate = new Date(Date.UTC(currentDate.getUTCFullYear(), currentDate.getUTCMonth() - 1, 1));
        renderCalendar();
    });
    
    document.getElementById('next-month').addEventListener('click', function() {
        currentDate = new Date(Date.UTC(currentDate.getUTCFullYear(), currentDate.getUTCMonth() + 1, 1));
        renderCalendar();
    });
    
    renderCalendar();

    // Staff status updates
    function updateStaffStatus() {
        const timestamp = new Date().getTime();
        fetch(`/api/staff/status-updates?t=${timestamp}`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            },
            credentials: 'same-origin',
        })
        .then(response => response.json())
        .then(data => {
            if (data.staff && data.staff.length > 0) {
                let html = '';
                data.staff.forEach(staff => {
                    const statusClass = (staff.status === 'busy') ? 'busy' : 'active';
                    const statusIcon = (staff.status === 'busy') ? 'bi-calendar-x-fill' : 'bi-check-circle-fill';
                    
                    html += `
                        <div class="staff-status-item ${statusClass}">
                            <i class="bi ${statusIcon}"></i>
                            <span>${staff.name || 'Unknown'}</span>
                        </div>
                    `;
                });
                document.getElementById('all-staff-container').innerHTML = html;
            }
            
            // Update timestamp - Using UTC
            const lastUpdateEl = document.getElementById('staff-status-last-update');
            if (lastUpdateEl) {
                const now = new Date();
                const utcTime = now.toLocaleTimeString('en-US', { timeZone: 'UTC', hour12: true });
                lastUpdateEl.innerHTML = `<i class="bi bi-arrow-clockwise"></i> <span>${utcTime} GMT</span>`;
            }
        })
        .catch(error => {
            console.error('Error updating staff status:', error);
        });
    }

    // Update staff status every 2 seconds
    updateStaffStatus();
    setInterval(updateStaffStatus, 2000);
</script>

{{-- Quick Action Button Styles --}}
<style>
    .quick-action-btn {
        padding: 14px 28px;
        font-weight: 600;
        border-radius: 12px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        font-size: 15px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .quick-action-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
    }

    .btn-primary {
        background: linear-gradient(135deg, #0d1b2a 0%, #1a2f47 100%);
        border: none;
        color: white;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #1a2f47 0%, #0d1b2a 100%);
        color: white;
    }

    .btn-outline-primary {
        border: 2px solid #0d1b2a;
        color: #0d1b2a;
        background: white;
    }

    .btn-outline-primary:hover {
        background: #0d1b2a;
        color: white;
        border-color: #0d1b2a;
    }

    /* Camera Styles */
    .camera-section {
        border: 2px dashed #d1d5db;
        border-radius: 12px;
        padding: 20px;
        background: #f9fafb;
        transition: all 0.3s ease;
    }

    .camera-section.active {
        border-color: #0d1b2a;
        background: linear-gradient(135deg, #e0f2fe 0%, #dbeafe 100%);
    }

    .camera-controls {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 16px;
    }

    .btn-camera {
        border-radius: 10px;
        padding: 10px 20px;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s ease;
        border: 2px solid;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-camera-start {
        background: #0d1b2a;
        color: white;
        border-color: transparent;
    }

    .btn-camera-start:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(13, 27, 42, 0.3);
        color: white;
    }

    .btn-camera-capture {
        background: linear-gradient(135deg, #10b981 0%, #34d399 100%);
        color: white;
        border-color: transparent;
    }

    .btn-camera-capture:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(16, 185, 129, 0.3);
        color: white;
    }

    .btn-camera-stop {
        background: white;
        color: #6b7280;
        border-color: #d1d5db;
    }

    .btn-camera-stop:hover {
        background: #f3f4f6;
        border-color: #9ca3af;
        color: #374151;
    }

    #cameraContainer {
        position: relative;
        display: inline-block;
        width: 400px;
        height: 400px;
        border-radius: 50%;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        border: 3px solid white;
        margin: 16px auto;
        text-align: center;
    }

    #cameraVideo {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    #photoPreviewImg {
        width: 300px;
        height: 300px;
        max-width: 300px;
        border-radius: 50%;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        border: 3px solid white;
        object-fit: cover;
    }

    .photo-preview-container {
        position: relative;
        display: inline-block;
        width: 300px;
        height: 300px;
        border-radius: 50%;
        overflow: hidden;
    }

    .btn-remove-photo {
        position: absolute;
        top: 5px;
        right: 5px;
        background: rgba(239, 68, 68, 0.9);
        color: white;
        border: none;
        border-radius: 50%;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-remove-photo:hover {
        background: rgba(220, 38, 38, 1);
        transform: scale(1.1);
    }

    .camera-info {
        margin-top: 16px;
        padding: 12px;
        background: #eff6ff;
        border-radius: 8px;
        font-size: 13px;
        color: #1e40af;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .form-section {
        margin-bottom: 28px;
        padding-bottom: 28px;
        border-bottom: 1px solid #e5e7eb;
    }

    .form-section:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    .form-section-title {
        font-size: 17px;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        padding-bottom: 12px;
        border-bottom: 2px solid #f3f4f6;
    }

    .form-section-title i {
        color: #0d1b2a;
        font-size: 18px;
    }

    .form-label {
        font-weight: 600;
        color: #374151;
        margin-bottom: 8px;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .form-label i {
        color: #0d1b2a;
    }

    .form-control, .form-select {
        border-radius: 10px;
        border: 1px solid #e5e7eb;
        padding: 12px 16px;
        transition: all 0.2s ease;
    }

    .form-control:focus, .form-select:focus {
        border-color: #0d1b2a;
        box-shadow: 0 0 0 3px rgba(13, 27, 42, 0.1);
    }

    .modal-content {
        border-radius: 16px;
        border: none;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    }

    .modal-header {
        border-bottom: 2px solid #f3f4f6;
        padding: 24px 28px;
        border-radius: 16px 16px 0 0;
    }

    .modal-body {
        padding: 28px;
    }

    .modal-title {
        font-size: 22px;
        font-weight: 700;
        color: #1f2937;
            display: flex;
            align-items: center;
        gap: 10px;
    }

    .modal-title i {
        color: #0d1b2a;
    }
</style>

{{-- Create Visitor Modal --}}
<div class="modal fade" id="createVisitorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title">
                        <i class="bi bi-person-plus-fill"></i>Register Visitor
                    </h5>
                    <small>Fill in visitor details and capture photo</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form method="POST" action="{{ route('visitors.store') }}" enctype="multipart/form-data" id="visitorForm">
                    @csrf

                    {{-- Personal Information Section --}}
                    <div class="form-section">
                        <div class="form-section-title">
                            <i class="bi bi-person-badge"></i>Personal Information
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">
                                    <i class="bi bi-person"></i>Full Name
                                </label>
                                <input name="name" class="form-control" placeholder="Enter visitor's full name" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">
                                    <i class="bi bi-telephone"></i>Phone Number
                                </label>
                                <input name="phone" type="tel" class="form-control" placeholder="Enter phone number" required>
                            </div>
                        </div>
                    </div>

                    {{-- Visit Details Section --}}
                    <div class="form-section">
                        <div class="form-section-title">
                            <i class="bi bi-calendar-event"></i>Visit Details
                        </div>
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="bi bi-briefcase"></i>Purpose of Visit
                            </label>
                            <input name="purpose" class="form-control" placeholder="e.g., Meeting, Delivery, Interview" required>
                        </div>
                        <div>
                            <label class="form-label">
                                <i class="bi bi-person-check"></i>Staff to Visit
                            </label>
                            <select name="staff_to_visit" class="form-select" required>
                                <option value="">Select Staff Member</option>
                                @foreach($allStaff as $s)
                                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Camera Section --}}
                    <div class="form-section">
                        <div class="form-section-title">
                            <i class="bi bi-camera"></i>Visitor Photo
                        </div>
                        <div class="camera-section" id="cameraSection">
                            <div class="camera-controls">
                                <button type="button" class="btn btn-camera btn-camera-start" id="startCameraBtn">
                                    <i class="bi bi-camera"></i>Start Camera
                                </button>
                                <button type="button" class="btn btn-camera btn-camera-capture d-none" id="capturePhotoBtn">
                                    <i class="bi bi-camera-fill"></i>Capture Photo
                                </button>
                                <button type="button" class="btn btn-camera btn-camera-stop d-none" id="stopCameraBtn">
                                    <i class="bi bi-stop-circle"></i>Stop Camera
                                </button>
                            </div>

                            <div id="cameraContainer" style="display: none;">
                                <video id="cameraVideo" autoplay playsinline></video>
                                <canvas id="photoCanvas" style="display:none;"></canvas>
                            </div>

                            <div id="photoPreview" style="display: none; text-align: center; margin: 16px 0;">
                                <div class="photo-preview-container">
                                    <img id="photoPreviewImg" src="" alt="Photo Preview">
                                    <button type="button" class="btn-remove-photo" id="removePhotoBtn" title="Remove Photo">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </div>
                            </div>

                            <input type="hidden" name="photo_data" id="photoDataInput">
                            <div class="camera-info">
                                <i class="bi bi-info-circle"></i>
                                <span>Start the camera to capture visitor's photo in real-time. Ensure good lighting for best results.</span>
                            </div>
                        </div>
                    </div>

                    <button class="btn btn-primary w-100" type="submit">
                        <i class="bi bi-check-circle me-2"></i>Check In Visitor
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Create Appointment Modal --}}
<div class="modal fade" id="createAppointmentModal" tabindex="-1" aria-labelledby="createAppointmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title" id="createAppointmentModalLabel">
                        <i class="bi bi-calendar-plus-fill"></i>New Appointment
                    </h5>
                    <small>Schedule a visitor appointment</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form action="{{ route('appointments.store') }}" method="POST" id="createAppointmentForm">
                    @csrf

                    {{-- Visitor Information Section --}}
                    <div class="form-section">
                        <div class="form-section-title">
                            <i class="bi bi-person-badge"></i>Visitor Information
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">
                                    <i class="bi bi-person"></i>Visitor Name
                                </label>
                                <input type="text" name="visitor_name" class="form-control" placeholder="Enter visitor's name" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">
                                    <i class="bi bi-telephone"></i>Visitor Phone
                                </label>
                                <input type="text" name="visitor_phone" class="form-control" placeholder="Enter phone number">
                            </div>
                        </div>
                    </div>

                    {{-- Appointment Details Section --}}
                    <div class="form-section">
                        <div class="form-section-title">
                            <i class="bi bi-calendar-event"></i>Appointment Details
                        </div>
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="bi bi-briefcase"></i>Purpose
                            </label>
                            <input type="text" name="purpose" class="form-control" placeholder="e.g., Meeting, Consultation, Interview" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="bi bi-person-check"></i>Select Staff
                            </label>
                            <select id="modal_staff_id" name="user_id" class="form-select" required>
                                <option value="">-- Select Staff Member --</option>
                                @foreach($users as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">
                                    <i class="bi bi-calendar"></i>Appointment Date
                                </label>
                                <div class="row g-2">
                                    <div class="col-4">
                                        <select id="modal_appointment_day" class="form-select" required>
                                            <option value="">Day</option>
                                        </select>
                                    </div>
                                    <div class="col-4">
                                        <select id="modal_appointment_month" class="form-select" required>
                                            <option value="">Month</option>
                                            <option value="01">January</option>
                                            <option value="02">February</option>
                                            <option value="03">March</option>
                                            <option value="04">April</option>
                                            <option value="05">May</option>
                                            <option value="06">June</option>
                                            <option value="07">July</option>
                                            <option value="08">August</option>
                                            <option value="09">September</option>
                                            <option value="10">October</option>
                                            <option value="11">November</option>
                                            <option value="12">December</option>
                                        </select>
                                    </div>
                                    <div class="col-4">
                                        <select id="modal_appointment_year" class="form-select" required>
                                            <option value="">Year</option>
                                        </select>
                                    </div>
                                </div>
                                <input type="hidden" id="modal_appointment_date" value="">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">
                                    <i class="bi bi-clock"></i>Appointment Time
                                </label>
                                <select id="modal_appointment_time" name="appointment_time" class="form-select" required>
                                    <option value="">-- Select Time --</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-calendar-plus me-2"></i>Schedule Appointment
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Camera functionality for visitor modal
    let stream = null;
    const startCameraBtn = document.getElementById('startCameraBtn');
    const capturePhotoBtn = document.getElementById('capturePhotoBtn');
    const stopCameraBtn = document.getElementById('stopCameraBtn');
    const cameraContainer = document.getElementById('cameraContainer');
    const cameraVideo = document.getElementById('cameraVideo');
    const photoCanvas = document.getElementById('photoCanvas');
    const photoPreview = document.getElementById('photoPreview');
    const photoPreviewImg = document.getElementById('photoPreviewImg');
    const removePhotoBtn = document.getElementById('removePhotoBtn');
    const photoDataInput = document.getElementById('photoDataInput');

    if (startCameraBtn) {
        startCameraBtn.addEventListener('click', async function() {
            try {
                stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' }, audio: false });
                cameraVideo.srcObject = stream;
                cameraContainer.style.display = 'block';
                document.getElementById('cameraSection').classList.add('active');
                startCameraBtn.classList.add('d-none');
                capturePhotoBtn.classList.remove('d-none');
                stopCameraBtn.classList.remove('d-none');
            } catch (error) {
                console.error(error);
                alert('Unable to access camera. Please check your camera permissions and try again.');
            }
        });
    }

    if (capturePhotoBtn) {
        capturePhotoBtn.addEventListener('click', function() {
            const ctx = photoCanvas.getContext('2d');
            photoCanvas.width = cameraVideo.videoWidth;
            photoCanvas.height = cameraVideo.videoHeight;
            ctx.drawImage(cameraVideo, 0, 0);

            const imageData = photoCanvas.toDataURL('image/jpeg', 0.8);
            photoPreviewImg.src = imageData;
            photoPreview.style.display = 'block';
            photoDataInput.value = imageData;

            stopCamera();
        });
    }

    if (stopCameraBtn) {
        stopCameraBtn.addEventListener('click', stopCamera);
    }

    if (removePhotoBtn) {
        removePhotoBtn.addEventListener('click', () => {
            photoPreview.style.display = 'none';
            photoPreviewImg.src = '';
            photoDataInput.value = '';
        });
    }

    function stopCamera() {
        if (stream) stream.getTracks().forEach(track => track.stop());
        stream = null;
        cameraContainer.style.display = 'none';
        cameraVideo.srcObject = null;
        document.getElementById('cameraSection').classList.remove('active');
        startCameraBtn.classList.remove('d-none');
        capturePhotoBtn.classList.add('d-none');
        stopCameraBtn.classList.add('d-none');
    }

    // Reset visitor modal when closed
    const visitorModal = document.getElementById('createVisitorModal');
    if (visitorModal) {
        visitorModal.addEventListener('hidden.bs.modal', function() {
            stopCamera();
            document.getElementById('visitorForm').reset();
            photoPreview.style.display = 'none';
            photoPreviewImg.src = '';
            photoDataInput.value = '';
            document.getElementById('cameraSection').classList.remove('active');
        });
    }

    // Appointment modal date dropdowns
    function initializeDateDropdowns() {
        const daySelect = document.getElementById('modal_appointment_day');
        const monthSelect = document.getElementById('modal_appointment_month');
        const yearSelect = document.getElementById('modal_appointment_year');
        
        if (!daySelect || !monthSelect || !yearSelect) return;
        
        function updateDays() {
            const month = monthSelect.value;
            const year = yearSelect.value;
            const currentDay = daySelect.value;
            
            daySelect.innerHTML = '<option value="">Day</option>';
            
            if (month && year) {
                const daysInMonth = new Date(year, month, 0).getDate();
                for (let i = 1; i <= daysInMonth; i++) {
                    const option = document.createElement('option');
                    option.value = String(i).padStart(2, '0');
                    option.textContent = i;
                    if (currentDay === option.value) {
                        option.selected = true;
                    }
                    daySelect.appendChild(option);
                }
            }
        }
        
        // Populate years (current year and 10 years forward)
        const currentYear = new Date().getFullYear();
        for (let i = currentYear; i <= currentYear + 10; i++) {
            const option = document.createElement('option');
            option.value = i;
            option.textContent = i;
            yearSelect.appendChild(option);
        }
        
        const today = new Date();
        monthSelect.value = String(today.getMonth() + 1).padStart(2, '0');
        yearSelect.value = today.getFullYear();
        updateDays();
        daySelect.value = String(today.getDate()).padStart(2, '0');
        updateDateInput();
        
        monthSelect.addEventListener('change', function() {
            updateDays();
            updateDateInput();
            loadTimes();
        });
        
        yearSelect.addEventListener('change', function() {
            updateDays();
            updateDateInput();
            loadTimes();
        });
        
        daySelect.addEventListener('change', function() {
            updateDateInput();
            loadTimes();
        });
    }
    
    function updateDateInput() {
        const day = document.getElementById('modal_appointment_day')?.value;
        const month = document.getElementById('modal_appointment_month')?.value;
        const year = document.getElementById('modal_appointment_year')?.value;
        
        if (day && month && year) {
            const dateInput = document.getElementById('modal_appointment_date');
            if (dateInput) {
                dateInput.value = `${year}-${month}-${day}`;
            }
        }
    }

    // Appointment times
    const allTimes = ["09:00 AM","10:00 AM","11:00 AM","12:00 PM","01:00 PM","02:00 PM","03:00 PM","04:00 PM"];
    
    function loadTimes() {
        const dateInput = document.getElementById('modal_appointment_date');
        const staffInput = document.getElementById('modal_staff_id');
        const timeSelect = document.getElementById('modal_appointment_time');
        
        if (!dateInput || !staffInput || !timeSelect) return;
        
        const date = dateInput.value;
        const staffId = staffInput.value;
        if (!date || !staffId) {
            timeSelect.innerHTML = '<option value="">-- Select Date and Staff First --</option>';
            return;
        }

        fetch(`/appointments/unavailable-times?date=${date}&user_id=${staffId}`)
        .then(res => res.json())
        .then(data => {
            const booked = data.booked_times || [];
            timeSelect.innerHTML = '<option value="">-- Select Time --</option>';
            allTimes.forEach(time => {
                const opt = document.createElement('option');
                opt.value = convertToDateTime(date, time);
                opt.textContent = booked.includes(time) ? time + ' (Booked)' : time;
                if(booked.includes(time)) opt.disabled = true;
                timeSelect.appendChild(opt);
            });
        })
        .catch(() => {
            timeSelect.innerHTML = '<option value="">Error loading times</option>';
        });
    }
    
    function convertToDateTime(date, time) {
        const [timePart, period] = time.split(' ');
        let [hours, minutes] = timePart.split(':').map(Number);

        if (period === 'PM' && hours !== 12) hours += 12;
        if (period === 'AM' && hours === 12) hours = 0;

        return `${date} ${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:00`;
    }

    // Update unread message count
    function updateUnreadMessageCount() {
        @if(in_array(auth()->user()->role, ['receptionist', 'staff']))
        fetch('/messages/unread-count', {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            const badge = document.getElementById('dashboard-messages-badge');
            if (data.count > 0) {
                if (badge) {
                    badge.textContent = data.count;
                } else {
                    const messagesLink = document.querySelector('a[href="{{ route("messages.index") }}"]');
                    if (messagesLink) {
                        const newBadge = document.createElement('span');
                        newBadge.className = 'position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger';
                        newBadge.id = 'dashboard-messages-badge';
                        newBadge.textContent = data.count;
                        messagesLink.appendChild(newBadge);
                    }
                }
            } else {
                if (badge) {
                    badge.remove();
                }
            }
        })
        .catch(error => console.error('Error updating unread count:', error));
        @endif
    }

    // Update unread count on page load and periodically
    @if(in_array(auth()->user()->role, ['receptionist', 'staff']))
    updateUnreadMessageCount();
    setInterval(updateUnreadMessageCount, 10000); // Update every 10 seconds
    @endif

    // Initialize appointment modal when shown
    const appointmentModal = document.getElementById('createAppointmentModal');
    if (appointmentModal) {
        appointmentModal.addEventListener('shown.bs.modal', function() {
            initializeDateDropdowns();
        });
        
        const staffInput = document.getElementById('modal_staff_id');
        if (staffInput) {
            staffInput.addEventListener('change', loadTimes);
        }
    }
</script>

@endsection
