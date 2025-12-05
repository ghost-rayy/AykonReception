@extends('layouts.app')

@section('title', 'Staff Dashboard')

@section('content')
@php
    $receptionist = \App\Models\User::where('role', 'receptionist')->first();
@endphp
<div class="container py-4">
    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Stats Cards - Compact & Clean -->
            <div class="row g-3 mb-4">
                <div class="col-12 col-sm-6 col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center py-4">
                            <i class="bi bi-calendar-check text-primary fs-2 mb-2"></i>
                            <p class="text-muted small mb-1">Today's Visits</p>
                            <h4 class="mb-0 text-primary fw-bold" id="today-visits-count">
                                {{ \App\Models\Visitors::where('user_id', auth()->id())->whereDate('created_at', today())->count() }}
                            </h4>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center py-4">
                            <i class="bi bi-clock-history text-warning fs-2 mb-2"></i>
                            <p class="text-muted small mb-1">Upcoming</p>
                            <h4 class="mb-0 text-warning fw-bold" id="upcoming-count">
                                {{ \App\Models\Visitors::where('user_id', auth()->id())->whereNull('check_out_time')->count() }}
                            </h4>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center py-4">
                            <i class="bi bi-check2-all text-success fs-2 mb-2"></i>
                            <p class="text-muted small mb-1">Completed</p>
                            <h4 class="mb-0 text-success fw-bold" id="completed-count">
                                {{ \App\Models\Visitors::where('user_id', auth()->id())->whereNotNull('check_out_time')->count() }}
                            </h4>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Status Notification Cards --}}
            <div class="row g-3 mb-4">
                <div class="col-12 col-sm-6">
                    <div class="card border-0 shadow-sm h-100 status-card status-card-free bg-success-subtle {{ $currentStatus === 'free' ? 'active' : '' }}" data-status="free" style="cursor: pointer; transition: all 0.3s ease;">
                        <div class="card-body text-center py-4 position-relative">
                            <div class="status-indicator" id="free-indicator" style="display: {{ $currentStatus === 'free' ? 'block' : 'none' }};">
                                <span class="badge bg-success position-absolute top-0 start-50 translate-middle">
                                    <i class="bi bi-check-circle-fill me-1"></i>Active
                                </span>
                            </div>
                            <i class="bi bi-check-circle text-success fs-1 mb-3" id="free-icon"></i>
                            <h5 class="mb-2 text-success fw-bold" id="free-title">I'm Free</h5>
                            <p class="text-muted small mb-0" id="free-subtitle">Click to notify receptionist</p>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6">
                    <div class="card border-0 shadow-sm h-100 status-card status-card-busy bg-danger-subtle {{ $currentStatus === 'busy' ? 'active' : '' }}" data-status="busy" style="cursor: pointer; transition: all 0.3s ease;">
                        <div class="card-body text-center py-4 position-relative">
                            <div class="status-indicator" id="busy-indicator" style="display: {{ $currentStatus === 'busy' ? 'block' : 'none' }};">
                                <span class="badge bg-danger position-absolute top-0 start-50 translate-middle">
                                    <i class="bi bi-calendar-x-fill me-1"></i>Active
                                </span>
                            </div>
                            <i class="bi bi-calendar-x text-danger fs-1 mb-3" id="busy-icon"></i>
                            <h5 class="mb-2 text-danger fw-bold" id="busy-title">In Meeting</h5>
                            <p class="text-muted small mb-0" id="busy-subtitle">Click to notify receptionist</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 fw-semibold text-dark">
                        <i class="bi bi-clock-history me-2 text-primary"></i>My Visitors Activity
                    </h6>
                </div>
                <div class="card-body p-0">
                    @php
                        $activities = collect();

                       $recentVisitors = \App\Models\Visitors::where('user_id', auth()->id())
                        ->latest()->take(10)->get()
                        ->map(fn($v) => [
                            'type' => 'visitor',

                            // Photo key for Blade
                            'photo' => $v->photo_path ? asset('storage/' . $v->photo_path) : null,

                            // Icon for when there is no photo
                            'icon' => 'bi-person-fill',

                            'color' => $v->status === 'checked_in'
                                ? 'warning'
                                : ($v->status === 'checked_out' ? 'success' : 'danger'),

                            'title' => $v->name,

                            'subtitle' => ($v->status === 'checked_in'
                                ? 'Checked in'
                                : ($v->status === 'checked_out' ? 'Checked out' : 'Cancelled'))
                                . ' • '
                                . ($v->status === 'checked_out'
                                    ? $v->check_out_time->diffForHumans()
                                    : $v->check_in_time->diffForHumans()),

                            'id' => $v->id
                        ]);

                        $recentAppointments = \App\Models\Appointments::where('user_id', auth()->id())
                            ->latest()->take(10)->get()
                            ->map(fn($a) => [
                                'type' => 'appointment',
                                'photo' => null, // Appointments don't have photos
                                'icon' => 'bi-calendar-event',
                                'color' => $a->status === 'completed' ? 'secondary' : 'primary',
                                'title' => $a->visitor_name ?? 'Appointment',
                                'subtitle' => $a->appointment_time->format('M d, H:i') . ' • ' . ucfirst($a->status),
                                'id' => $a->id
                            ]);

                        $activities = $recentVisitors->merge($recentAppointments)->sortByDesc(fn($i) => $i['id'] ?? now())->take(8);
                    @endphp

                    @if($activities->count())
                        <div class="list-group list-group-flush">
                            @foreach($activities as $activity)
                                <a href="#" class="list-group-item list-group-item-action py-3 px-4 activity-item"
                                   data-type="{{ $activity['type'] }}" data-id="{{ $activity['id'] ?? '' }}">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            @if(!empty($activity['photo']))
                                                <img src="{{ $activity['photo'] }}" alt="{{ $activity['title'] }}" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                                            @else
                                                <div class="avatar avatar-sm bg-{{ $activity['color'] ?? 'primary' }}-subtle text-{{ $activity['color'] ?? 'primary' }} rounded-circle">
                                                    <i class="bi {{ $activity['icon'] ?? 'bi-circle' }} fs-5"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <div class="fw-semibold text-dark">{{ $activity['title'] }}</div>
                                            <small class="text-muted">{{ $activity['subtitle'] }}</small>
                                        </div>
                                        <i class="bi bi-chevron-right text-muted"></i>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-center text-muted py-5">No recent activity</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Sidebar -->
        <div class="col-lg-4">
            <!-- Notifications Card -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-semibold text-dark">
                            <i class="bi bi-bell-fill me-2 text-primary"></i>Notifications
                        </h6>
                        <small class="text-muted" id="notifications-last-update">
                            <i class="bi bi-arrow-clockwise"></i> <span>Just now</span>
                        </small>
                    </div>
                </div>
                <div class="card-body p-0" style="max-height: 400px; overflow-y: auto;">
                    <div id="notifications-container">
                        <div class="text-center py-4">
                            <div class="spinner-border spinner-border-sm text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="text-muted mt-2 small">Loading notifications...</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Appointments Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <ul class="nav nav-tabs card-header-tabs" id="sidebarTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="appointments-tab" data-bs-toggle="tab" data-bs-target="#appointments" type="button" role="tab" aria-controls="appointments" aria-selected="true">
                                <i class="bi bi-calendar-event me-1"></i>My Appointments
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body p-0">
                    <div class="tab-content" id="sidebarTabsContent">
                        <!-- Appointments Tab -->
                        <div class="tab-pane fade show active" id="appointments" role="tabpanel" aria-labelledby="appointments-tab">
                            @php
                                $sidebarAppointments = \App\Models\Appointments::where('user_id', auth()->id())
                                    ->latest()->take(10)->get();
                            @endphp
                            @if($sidebarAppointments->count())
                                <div class="list-group list-group-flush">
                                    @foreach($sidebarAppointments as $appointment)
                                        @php
                                            $statusColor = match($appointment->status) {
                                                'pending' => 'warning',
                                                'confirmed' => 'success',
                                                'completed' => 'success',
                                                'canceled' => 'danger',
                                                default => 'secondary'
                                            };
                                        @endphp
                                        <a href="#" class="list-group-item list-group-item-action py-3 px-4 activity-item"
                                           data-type="appointment" data-id="{{ $appointment->id }}">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0">
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <div class="fw-semibold text-dark">{{ $appointment->visitor_name ?? 'Appointment' }}</div>
                                                    <small class="text-muted">{{ $appointment->appointment_time->format('M d, H:i') }} • <span class="text-{{ $statusColor }}">{{ ucfirst($appointment->status) }}</span></small>
                                                </div>
                                                <div class="d-flex align-items-center">
                                                    <!-- <button class="btn btn-sm btn-outline-primary me-2" onclick="showAppointmentModal({{ $appointment->id }})">
                                                        <i class="bi bi-eye"></i> View
                                                    </button> -->
                                                    <i class="bi bi-chevron-right text-muted"></i>
                                                </div>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-center text-muted py-5">No appointments</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Details Modal -->
<div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header border-0 pb-0" style="background: linear-gradient(135deg, #0d1b2a, #1a2f47); border-radius: 16px 16px 0 0;">
                <div class="d-flex align-items-center gap-3 w-100">
                    <div id="modalIcon" class="text-white" style="font-size: 2rem;">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="modal-title text-white fw-bold mb-0" id="detailsModalLabel">Details</h5>
                        <small class="text-white-50" id="modalSubtitle">Loading...</small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body p-4" id="modalBody">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="text-muted mt-3">Loading details...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .avatar { width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; }
    .avatar-xs { width: 32px; height: 32px; font-size: 0.85rem; }
    .avatar-sm { width: 40px; height: 40px; font-size: 0.85rem; }
    .activity-item:hover, .notification-item:hover { background-color: #f8f9fa; cursor: pointer; }
    .card { transition: transform 0.2s; border-radius: 12px; }
    .card:hover { transform: translateY(-2px); }
    .status-card:hover { transform: translateY(-4px); box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
    
    /* Status Card Switch Styles */
    .status-card {
        position: relative;
        opacity: 0.7;
        transition: all 0.3s ease;
    }
    
    .status-card.active {
        opacity: 1;
        border: 3px solid #0d1b2a !important;
        box-shadow: 0 8px 25px rgba(13, 27, 42, 0.3) !important;
        transform: scale(1.02);
    }
    
    .status-card.active .status-indicator {
        display: block !important;
    }
    
    .status-card:not(.active) {
        opacity: 0.6;
        filter: grayscale(0.3);
    }
    
    .status-card:not(.active):hover {
        opacity: 0.85;
        filter: grayscale(0);
    }
    
    /* Modal Styles */
    .modal-content {
        border-radius: 16px;
        overflow: hidden;
    }
    
    .detail-item {
        transition: all 0.2s ease;
        border: 1px solid #e2e8f0;
    }
    
    .detail-item:hover {
        border-color: #0d1b2a;
        box-shadow: 0 2px 8px rgba(13, 27, 42, 0.1);
    }
    
    .modal-header {
        background: linear-gradient(135deg, #0d1b2a, #1a2f47);
    }
    
    /* Notification Styles */
    .notification-item {
        transition: all 0.2s ease;
        border-left-width: 3px !important;
    }
    
    .notification-item:hover {
        background-color: #f8f9fa !important;
        transform: translateX(2px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    #notifications-container {
        scrollbar-width: thin;
        scrollbar-color: #dee2e6 transparent;
    }
    
    #notifications-container::-webkit-scrollbar {
        width: 6px;
    }
    
    #notifications-container::-webkit-scrollbar-track {
        background: transparent;
    }
    
    #notifications-container::-webkit-scrollbar-thumb {
        background-color: #dee2e6;
        border-radius: 3px;
    }
    
    #notifications-container::-webkit-scrollbar-thumb:hover {
        background-color: #adb5bd;
    }
    
    #notifications-last-update {
        font-size: 0.75rem;
    }
</style>

@endsection

@section('scripts')
<script>
    // Click handlers
    document.querySelectorAll('.activity-item').forEach(el => {
        el.addEventListener('click', function(e) {
            e.preventDefault();
            const type = this.dataset.type;
            const id = this.dataset.id;
            if (type === 'visitor') showVisitorModal(id);
            if (type === 'appointment') showAppointmentModal(id);
        });
    });

    // Function to update status (switch behavior)
    function updateStatus(newStatus) {
        // Immediately update UI to show switch behavior
        const freeCard = document.querySelector('.status-card-free');
        const busyCard = document.querySelector('.status-card-busy');
        
        if (newStatus === 'free') {
            freeCard.classList.add('active');
            busyCard.classList.remove('active');
            document.getElementById('free-indicator').style.display = 'block';
            document.getElementById('busy-indicator').style.display = 'none';
        } else if (newStatus === 'busy') {
            busyCard.classList.add('active');
            freeCard.classList.remove('active');
            document.getElementById('busy-indicator').style.display = 'block';
            document.getElementById('free-indicator').style.display = 'none';
        }

        // Update status via API
        fetch('/messages/status', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            credentials: 'same-origin',
            body: JSON.stringify({
                status: newStatus
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to update status: ' + response.status);
            }
            return response.json();
        })
        .then(statusData => {
            if (statusData.success) {
                // Status update was successful - show success immediately
                const successMessage = newStatus === 'free'
                    ? 'Status updated successfully! You are now free.'
                    : 'Status updated successfully! You are now in a meeting.';
                showSuccessModal(successMessage);
                
                // Try to send notification to receptionist (non-blocking)
                const receptionistId = {{ $receptionist ? $receptionist->id : 'null' }};
                if (receptionistId) {
                    const message = newStatus === 'free' 
                        ? `{{ auth()->user()->name }} is now free`
                        : `{{ auth()->user()->name }} is now in a meeting`;
                    
                    // Send message in background - don't wait for it or show errors
                    fetch('/messages', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        credentials: 'same-origin',
                        body: JSON.stringify({
                            receiver_id: receptionistId,
                            message: message
                        })
                    }).catch(err => {
                        // Silently fail - status was already updated successfully
                        console.log('Could not send notification to receptionist:', err);
                    });
                }
            } else {
                // Revert UI if update failed
                updateStatusCards();
                throw new Error('Failed to update status');
            }
        })
        .catch(error => {
            console.error('Error updating status:', error);
            // Revert UI on error
            updateStatusCards();
            alert('Error updating status. Please try again.');
        });
    }

    // Status card click handlers with switch behavior
    document.querySelector('.status-card-free').addEventListener('click', function() {
        // Don't do anything if already active
        if (this.classList.contains('active')) {
            return;
        }
        updateStatus('free');
    });

    document.querySelector('.status-card-busy').addEventListener('click', function() {
        // Don't do anything if already active
        if (this.classList.contains('active')) {
            return;
        }
        updateStatus('busy');
    });

    // Update status card appearance based on current status (switch behavior)
    function updateStatusCards() {
        fetch('/messages/status', {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            credentials: 'same-origin',
            cache: 'no-cache'
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to fetch status: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            const freeCard = document.querySelector('.status-card-free');
            const busyCard = document.querySelector('.status-card-busy');
            const currentStatus = data.status || 'free';
            
            // Only update if status actually changed
            const freeIsActive = freeCard.classList.contains('active');
            const busyIsActive = busyCard.classList.contains('active');
            
            // Reset all cards
            freeCard.classList.remove('active');
            busyCard.classList.remove('active');
            document.getElementById('free-indicator').style.display = 'none';
            document.getElementById('busy-indicator').style.display = 'none';

            // Activate the current status card
            if (currentStatus === 'free') {
                freeCard.classList.add('active');
                document.getElementById('free-indicator').style.display = 'block';
            } else if (currentStatus === 'busy') {
                busyCard.classList.add('active');
                document.getElementById('busy-indicator').style.display = 'block';
            }
        })
        .catch(error => {
            console.error('Error getting current status:', error);
            // Don't reset the UI if API call fails - keep the server-side rendered state
        });
    }

    // Update status cards on page load (to sync with any changes, but initial state is already set server-side)
    // Use a small delay to ensure DOM is ready
    setTimeout(updateStatusCards, 100);

    // Load and refresh notifications every 2 seconds
    function loadStaffNotifications() {
        const container = document.getElementById('notifications-container');
        const lastUpdateEl = document.getElementById('notifications-last-update');
        
        if (!container) {
            console.error('Notifications container not found');
            return;
        }

        // Update timestamp
        if (lastUpdateEl) {
            const now = new Date();
            const timeStr = now.toLocaleTimeString();
            lastUpdateEl.innerHTML = `<i class="bi bi-arrow-clockwise"></i> <span>${timeStr}</span>`;
        }

        // Add timestamp to prevent caching
        const timestamp = new Date().getTime();
        fetch(`/messages/staff-notifications?t=${timestamp}`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Cache-Control': 'no-cache',
                'Pragma': 'no-cache'
            },
            credentials: 'same-origin',
            cache: 'no-store'
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to load notifications: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            let html = '';
            
            // Combine visitors and appointments
            const allNotifications = [];
            
            // Add visitors
            if (data.visitors && data.visitors.length > 0) {
                data.visitors.forEach(visitor => {
                    allNotifications.push({
                        ...visitor,
                        sortTime: new Date(visitor.check_in_time)
                    });
                });
            }
            
            // Add appointments
            if (data.appointments && data.appointments.length > 0) {
                data.appointments.forEach(appointment => {
                    allNotifications.push({
                        ...appointment,
                        sortTime: new Date(appointment.appointment_time)
                    });
                });
            }
            
            // Sort by time (newest first)
            allNotifications.sort((a, b) => b.sortTime - a.sortTime);
            
            if (allNotifications.length > 0) {
                allNotifications.forEach(item => {
                    if (item.type === 'visitor') {
                        const photoHtml = item.photo_path 
                            ? `<img src="${item.photo_path}" alt="${item.name}" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">`
                            : `<div class="avatar avatar-sm bg-info-subtle text-info rounded-circle">
                                <i class="bi bi-person-fill fs-5"></i>
                            </div>`;
                        
                        html += `
                            <div class="list-group-item list-group-item-action py-3 px-4 notification-item" 
                                 data-type="visitor" 
                                 data-id="${item.id}"
                                 style="cursor: pointer; border-left: 3px solid #0dcaf0;">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        ${photoHtml}
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="fw-semibold text-dark">${item.name}</div>
                                            <span class="badge bg-info">Visitor</span>
                                        </div>
                                        <small class="text-muted d-block">
                                            <i class="bi bi-telephone me-1"></i>${item.phone || 'N/A'}
                                        </small>
                                        <small class="text-muted d-block">
                                            <i class="bi bi-clock me-1"></i>Checked in ${item.check_in_human || 'recently'}
                                        </small>
                                        ${item.purpose ? `<small class="text-muted d-block"><i class="bi bi-briefcase me-1"></i>${item.purpose}</small>` : ''}
                                    </div>
                                    <i class="bi bi-chevron-right text-muted ms-2"></i>
                                </div>
                            </div>
                        `;
                    } else if (item.type === 'appointment') {
                        const statusColor = item.status === 'pending' ? 'warning' 
                            : item.status === 'confirmed' ? 'success'
                            : item.status === 'completed' ? 'success'
                            : item.status === 'canceled' ? 'danger'
                            : 'secondary';
                        
                        html += `
                            <div class="list-group-item list-group-item-action py-3 px-4 notification-item" 
                                 data-type="appointment" 
                                 data-id="${item.id}"
                                 style="cursor: pointer; border-left: 3px solid #0d6efd;">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div class="avatar avatar-sm bg-primary-subtle text-primary rounded-circle">
                                            <i class="bi bi-calendar-event fs-5"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="fw-semibold text-dark">${item.visitor_name || 'Appointment'}</div>
                                            <span class="badge bg-primary">Appointment</span>
                                        </div>
                                        <small class="text-muted d-block">
                                            <i class="bi bi-calendar-check me-1"></i>${item.appointment_time_formatted || 'N/A'}
                                        </small>
                                        <small class="text-muted d-block">
                                            <i class="bi bi-clock me-1"></i>${item.appointment_time_human || 'soon'}
                                        </small>
                                        ${item.purpose ? `<small class="text-muted d-block"><i class="bi bi-briefcase me-1"></i>${item.purpose}</small>` : ''}
                                        <span class="badge bg-${statusColor} mt-1">${item.status ? item.status.charAt(0).toUpperCase() + item.status.slice(1) : 'N/A'}</span>
                                    </div>
                                    <i class="bi bi-chevron-right text-muted ms-2"></i>
                                </div>
                            </div>
                        `;
                    }
                });
            } else {
                html = '<div class="text-center text-muted py-5"><i class="bi bi-inbox fs-1 d-block mb-2"></i><p class="mb-0">No new notifications</p></div>';
            }
            
            container.innerHTML = html;
            
            // Add click handlers to notification items
            container.querySelectorAll('.notification-item').forEach(item => {
                item.addEventListener('click', function() {
                    const type = this.dataset.type;
                    const id = this.dataset.id;
                    if (type === 'visitor') {
                        showVisitorModal(id);
                    } else if (type === 'appointment') {
                        showAppointmentModal(id);
                    }
                });
            });
        })
        .catch(error => {
            console.error('Error loading notifications:', error);
            container.innerHTML = '<div class="alert alert-warning py-2 m-3"><i class="bi bi-exclamation-triangle me-2"></i>Error loading notifications</div>';
        });
    }

    // Load notifications on page load
    loadStaffNotifications();
    
    // Refresh notifications every 2 seconds
    setInterval(loadStaffNotifications, 2000);

    // Success modal function
    function showSuccessModal(message) {
        // Create modal HTML
        const modalHtml = `
            <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body text-center py-4">
                            <div class="mb-3">
                                <i class="bi bi-check-circle-fill text-success" style="font-size: 3rem;"></i>
                            </div>
                            <h5 class="modal-title mb-3" id="successModalLabel">Success!</h5>
                            <p class="mb-0">${message}</p>
                        </div>
                        <div class="modal-footer border-0 justify-content-center">
                            <button type="button" class="btn btn-success px-4" data-bs-dismiss="modal">OK</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Remove existing modal if present
        const existingModal = document.getElementById('successModal');
        if (existingModal) {
            existingModal.remove();
        }

        // Add modal to page
        document.body.insertAdjacentHTML('beforeend', modalHtml);

        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('successModal'));
        modal.show();

        // Auto remove modal after it's hidden
        document.getElementById('successModal').addEventListener('hidden.bs.modal', function() {
            this.remove();
        });
    }

    // Modal functions
    function showVisitorModal(id) {
        const modal = new bootstrap.Modal(document.getElementById('detailsModal'));
        const modalLabel = document.getElementById('detailsModalLabel');
        const modalSubtitle = document.getElementById('modalSubtitle');
        const modalBody = document.getElementById('modalBody');
        const modalIcon = document.getElementById('modalIcon');
        
        // Show loading state
        modalLabel.textContent = 'Visitor Details';
        modalSubtitle.textContent = 'Loading...';
        modalIcon.innerHTML = '<i class="bi bi-person-fill"></i>';
        modalBody.innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="text-muted mt-3">Loading visitor details...</p>
            </div>
        `;
        modal.show();

        // Fetch visitor details
        fetch(`/visitors/${id}/details`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        })
        .then(response => {
            if (!response.ok) throw new Error('Failed to load visitor details');
            return response.json();
        })
        .then(data => {
            const checkInTime = data.check_in_time ? new Date(data.check_in_time).toLocaleString() : 'N/A';
            const checkOutTime = data.check_out_time ? new Date(data.check_out_time).toLocaleString() : 'Not checked out';
            const statusBadge = data.status === 'checked_in' 
                ? '<span class="badge bg-warning">Checked In</span>'
                : data.status === 'checked_out'
                ? '<span class="badge bg-success">Checked Out</span>'
                : '<span class="badge bg-danger">Cancelled</span>';
            
            const photoHtml = data.photo_path 
                ? `<img src="/storage/${data.photo_path}" alt="${data.name}" class="rounded-circle shadow-sm" style="width: 120px; height: 120px; object-fit: cover; border: 4px solid #0d1b2a;">`
                : `<div class="rounded-circle shadow-sm d-flex align-items-center justify-content-center bg-primary text-white" style="width: 120px; height: 120px; margin: 0 auto; font-size: 3rem;"><i class="bi bi-person-fill"></i></div>`;

            modalLabel.textContent = data.name;
            modalSubtitle.textContent = 'Visitor Information';
            modalBody.innerHTML = `
                <div class="text-center mb-4">
                    ${photoHtml}
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="detail-item p-3 bg-light rounded-3">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="bi bi-person text-primary fs-5"></i>
                                <label class="text-muted small mb-0">Full Name</label>
                            </div>
                            <div class="fw-semibold text-dark">${data.name || 'N/A'}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item p-3 bg-light rounded-3">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="bi bi-telephone text-primary fs-5"></i>
                                <label class="text-muted small mb-0">Phone Number</label>
                            </div>
                            <div class="fw-semibold text-dark">${data.phone || 'N/A'}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item p-3 bg-light rounded-3">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="bi bi-briefcase text-primary fs-5"></i>
                                <label class="text-muted small mb-0">Purpose</label>
                            </div>
                            <div class="fw-semibold text-dark">${data.purpose || 'N/A'}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item p-3 bg-light rounded-3">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="bi bi-person-badge text-primary fs-5"></i>
                                <label class="text-muted small mb-0">Status</label>
                            </div>
                            <div>${statusBadge}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item p-3 bg-light rounded-3">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="bi bi-box-arrow-in-right text-primary fs-5"></i>
                                <label class="text-muted small mb-0">Check-In Time</label>
                            </div>
                            <div class="fw-semibold text-dark">${checkInTime}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item p-3 bg-light rounded-3">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="bi bi-box-arrow-right text-primary fs-5"></i>
                                <label class="text-muted small mb-0">Check-Out Time</label>
                            </div>
                            <div class="fw-semibold text-dark">${checkOutTime}</div>
                        </div>
                    </div>
                </div>
            `;
        })
        .catch(error => {
            console.error('Error loading visitor details:', error);
            modalBody.innerHTML = `
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Failed to load visitor details. Please try again.
                </div>
            `;
        });
    }

    function showAppointmentModal(id) {
        const modal = new bootstrap.Modal(document.getElementById('detailsModal'));
        const modalLabel = document.getElementById('detailsModalLabel');
        const modalSubtitle = document.getElementById('modalSubtitle');
        const modalBody = document.getElementById('modalBody');
        const modalIcon = document.getElementById('modalIcon');
        
        // Show loading state
        modalLabel.textContent = 'Appointment Details';
        modalSubtitle.textContent = 'Loading...';
        modalIcon.innerHTML = '<i class="bi bi-calendar-event"></i>';
        modalBody.innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="text-muted mt-3">Loading appointment details...</p>
            </div>
        `;
        modal.show();

        // Fetch appointment details
        fetch(`/appointments/${id}/details`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        })
        .then(response => {
            if (!response.ok) throw new Error('Failed to load appointment details');
            return response.json();
        })
        .then(data => {
            const appointmentTime = data.appointment_time ? new Date(data.appointment_time).toLocaleString() : 'N/A';
            const statusColor = data.status === 'pending' ? 'warning' 
                : data.status === 'confirmed' ? 'success'
                : data.status === 'completed' ? 'success'
                : data.status === 'canceled' ? 'danger'
                : 'secondary';
            const statusBadge = `<span class="badge bg-${statusColor}">${data.status ? data.status.charAt(0).toUpperCase() + data.status.slice(1) : 'N/A'}</span>`;
            
            modalLabel.textContent = data.visitor_name || 'Appointment';
            modalSubtitle.textContent = 'Appointment Information';
            modalBody.innerHTML = `
                <div class="text-center mb-4">
                    <div class="rounded-circle shadow-sm d-flex align-items-center justify-content-center bg-primary text-white mx-auto" style="width: 120px; height: 120px; font-size: 3rem;">
                        <i class="bi bi-calendar-event"></i>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="detail-item p-3 bg-light rounded-3">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="bi bi-person text-primary fs-5"></i>
                                <label class="text-muted small mb-0">Visitor Name</label>
                            </div>
                            <div class="fw-semibold text-dark">${data.visitor_name || 'N/A'}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item p-3 bg-light rounded-3">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="bi bi-telephone text-primary fs-5"></i>
                                <label class="text-muted small mb-0">Visitor Phone</label>
                            </div>
                            <div class="fw-semibold text-dark">${data.visitor_phone || 'N/A'}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item p-3 bg-light rounded-3">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="bi bi-calendar-check text-primary fs-5"></i>
                                <label class="text-muted small mb-0">Appointment Time</label>
                            </div>
                            <div class="fw-semibold text-dark">${appointmentTime}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item p-3 bg-light rounded-3">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="bi bi-info-circle text-primary fs-5"></i>
                                <label class="text-muted small mb-0">Status</label>
                            </div>
                            <div>${statusBadge}</div>
                        </div>
                    </div>
                    ${data.purpose ? `
                    <div class="col-12">
                        <div class="detail-item p-3 bg-light rounded-3">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="bi bi-briefcase text-primary fs-5"></i>
                                <label class="text-muted small mb-0">Purpose</label>
                            </div>
                            <div class="fw-semibold text-dark">${data.purpose}</div>
                        </div>
                    </div>
                    ` : ''}
                    ${data.user ? `
                    <div class="col-12">
                        <div class="detail-item p-3 bg-light rounded-3">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="bi bi-person-badge text-primary fs-5"></i>
                                <label class="text-muted small mb-0">Assigned Staff</label>
                            </div>
                            <div class="fw-semibold text-dark">${data.user.name || 'N/A'}</div>
                        </div>
                    </div>
                    ` : ''}
                </div>
            `;
        })
        .catch(error => {
            console.error('Error loading appointment details:', error);
            modalBody.innerHTML = `
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Failed to load appointment details. Please try again.
                </div>
            `;
        });
    }
</script>
@endsection
