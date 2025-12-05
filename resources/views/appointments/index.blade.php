@extends('layouts.app')

@section('title', 'Appointments')

@section('content')
<style>
    /* Page Background */
    body {
        background: linear-gradient(135deg, #f5f7fa 0%, #e8ecf1 100%);
        min-height: 100vh;
    }

    .appointments-page-wrapper {
        padding: 30px 0 50px;
        animation: fadeIn 0.6s ease-in;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Page Header */
    .page-header {
        background: white;
        border-radius: 16px;
        padding: 30px;
        margin-bottom: 30px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
    }

    .page-title {
        font-size: 32px;
        font-weight: 700;
        color: #1f2937;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .page-title i {
        color: #0d1b2a;
        font-size: 36px;
    }

    /* Search Bar */
    .search-container {
        position: relative;
        flex: 1;
        max-width: 500px;
    }

    .search-input {
        border-radius: 50px;
        padding: 14px 20px 14px 50px;
        border: 2px solid #e5e7eb;
        transition: all 0.3s ease;
        font-size: 15px;
        background: white;
    }

    .search-input:focus {
        border-color: #0d1b2a;
        box-shadow: 0 0 0 4px rgba(13, 27, 42, 0.1);
        outline: none;
    }

    .search-icon {
        position: absolute;
        left: 18px;
        top: 50%;
        transform: translateY(-50%);
        color: #6b7280;
        font-size: 18px;
        z-index: 10;
    }

    /* Table Container */
    .table-container {
        background: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    /* Modern Table */
    .modern-table {
        border-collapse: separate !important;
        border-spacing: 0 12px !important;
        margin: 0;
    }

    .modern-table thead {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }

    .modern-table thead th {
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        color: #374151;
        background: transparent;
        border-bottom: 2px solid #e5e7eb;
        padding: 16px 12px;
        letter-spacing: 0.5px;
    }

    .modern-table thead th:first-child {
        border-top-left-radius: 12px;
    }

    .modern-table thead th:last-child {
        border-top-right-radius: 12px;
    }

    .modern-table tbody tr {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }

    .modern-table tbody tr:hover {
        background: #f9fafb;
        box-shadow: 0 4px 12px rgba(13, 27, 42, 0.1);
        transform: translateY(-2px);
    }

    .modern-table tbody tr td {
        padding: 20px 12px;
        border-top: none;
        border-bottom: none;
        vertical-align: middle;
    }

    /* Status Badge */
    .status-select {
        border-radius: 8px;
        border: 2px solid #e5e7eb;
        padding: 6px 10px;
        font-size: 13px;
        font-weight: 600;
        transition: all 0.3s ease;
        min-width: 120px;
    }

    .status-select:focus {
        border-color: #0d1b2a;
        box-shadow: 0 0 0 3px rgba(13, 27, 42, 0.1);
        outline: none;
    }

    .status-select option[value="pending"] {
        color: #f59e0b;
    }

    .status-select option[value="confirmed"] {
        color: #10b981;
    }

    .status-select option[value="canceled"] {
        color: #ef4444;
    }

    /* Buttons */
    .btn-new-appointment {
        background: #0d1b2a;
        color: white;
        border: none;
        border-radius: 12px;
        padding: 12px 24px;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-new-appointment:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(13, 27, 42, 0.3);
        color: white;
    }

    .btn-delete {
        background: linear-gradient(135deg, #ef4444 0%, #f87171 100%);
        color: white;
        border: none;
        border-radius: 8px;
        padding: 6px 14px;
        font-size: 13px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-delete:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        color: white;
    }

    /* Modals */
    .modal-content {
        border: none;
        border-radius: 20px;
        box-shadow: 0 25px 70px rgba(0, 0, 0, 0.25);
        overflow: hidden;
    }

    .modal-header {
        background: #0d1b2a;
        color: white;
        border: none;
        padding: 28px 30px;
        position: relative;
    }

    .modal-header::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: rgba(255, 255, 255, 0.2);
    }

    .modal-header .btn-close {
        filter: brightness(0) invert(1);
        opacity: 0.9;
        transition: all 0.3s ease;
    }

    .modal-header .btn-close:hover {
        opacity: 1;
        transform: rotate(90deg);
    }

    .modal-title {
        font-size: 24px;
        font-weight: 700;
        color: white;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .modal-title i {
        font-size: 28px;
    }

    .modal-header small {
        color: rgba(255, 255, 255, 0.9);
        font-size: 13px;
        margin-top: 4px;
        display: block;
    }

    .modal-body {
        padding: 30px;
        background: #fafbfc;
    }

    .form-section {
        background: white;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        border: 1px solid #f0f0f0;
    }

    .form-section-title {
        font-size: 14px;
        font-weight: 700;
        color: #0d1b2a;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .form-section-title i {
        font-size: 16px;
    }

    .modal-body label {
        font-weight: 600;
        color: #374151;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
    }

    .modal-body label i {
        color: #0d1b2a;
        font-size: 16px;
    }

    .modal-body .form-control,
    .modal-body .form-select {
        border-radius: 10px;
        border: 2px solid #e5e7eb;
        padding: 14px 16px;
        transition: all 0.3s ease;
        font-size: 15px;
        background: white;
    }

    .modal-body .form-control:focus,
    .modal-body .form-select:focus {
        border-color: #0d1b2a;
        box-shadow: 0 0 0 4px rgba(13, 27, 42, 0.1);
        outline: none;
        background: #fafbfc;
    }

    .modal-body .btn-primary {
        background: #0d1b2a;
        border: none;
        border-radius: 12px;
        padding: 16px;
        font-weight: 700;
        font-size: 16px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(13, 27, 42, 0.3);
        margin-top: 10px;
    }

    .modal-body .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(13, 27, 42, 0.4);
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #9ca3af;
    }

    .empty-state i {
        font-size: 64px;
        margin-bottom: 16px;
        opacity: 0.5;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .search-container {
            max-width: 100%;
        }

        .page-title {
            font-size: 24px;
        }

        .modern-table {
            font-size: 13px;
        }
    }
</style>

<div class="appointments-page-wrapper">
    <div class="container">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">
                <!-- <i class="bi bi-calendar-event-fill"></i>
                Appointments Management -->
            </h1>
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <div class="search-container">
                    <i class="bi bi-search search-icon"></i>
                    <input type="text" id="searchAppointments" class="form-control search-input" placeholder="Search by visitor, phone, or staff...">
                </div>
                <button class="btn btn-new-appointment" data-bs-toggle="modal" data-bs-target="#createAppointmentModal">
                    <i class="bi bi-plus-circle"></i>New Appointment
                </button>
            </div>
        </div>

        <!-- Table Container -->
        <div class="table-container">
            <div class="table-responsive">
                <table class="table modern-table align-middle" id="appointmentsTable">
                    <thead>
                        <tr>
                            <th>Visitor</th>
                            <th>Phone</th>
                            <th>Staff</th>
                            <th>Appointment Time</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="appointmentsBody">
                        @forelse($appointments as $a)
                        <tr>
                            <td>
                                <div class="fw-semibold text-dark">{{ $a->visitor_name }}</div>
                            </td>
                            <td>
                                <div class="text-muted">
                                    <i class="bi bi-telephone-fill me-1"></i>{{ $a->visitor_phone }}
                                </div>
                            </td>
                            <td>
                                <div class="text-dark">
                                    <i class="bi bi-person-badge me-1 text-primary"></i>{{ $a->user->name }}
                                </div>
                            </td>
                            <td>
                                <div class="text-dark">{{ \Carbon\Carbon::parse($a->appointment_time)->format('M d, Y') }}</div>
                                <div class="text-muted small">{{ \Carbon\Carbon::parse($a->appointment_time)->format('h:i A') }}</div>
                            </td>
                            <td>
                                <form action="{{ route('appointments.updateStatus', $a) }}" method="POST" class="d-inline">
                                    @csrf
                                    <select name="status" onchange="this.form.submit()" class="status-select" {{ in_array($a->status, ['confirmed', 'canceled']) ? 'disabled' : '' }}>
                                        <option value="pending" {{ $a->status=='pending'?'selected':'' }}>Pending</option>
                                        <option value="confirmed" {{ $a->status=='confirmed'?'selected':'' }}>Confirmed</option>
                                        <option value="canceled" {{ $a->status=='canceled'?'selected':'' }}>Canceled</option>
                                    </select>
                                </form>
                            </td>
                            <td class="text-end">
                                <form action="{{ route('appointments.destroy', $a) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this appointment?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-delete">
                                        <i class="bi bi-trash me-1"></i>Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="bi bi-calendar-x"></i>
                                    <h5 class="mt-3">No appointments found</h5>
                                    <p class="text-muted">Create your first appointment to get started</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($appointments->hasPages())
            <div class="p-3">
                {{ $appointments->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Create Appointment Modal -->
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

@endsection

@section('scripts')
<script>
    // Enhanced search with smooth animations
    document.getElementById('searchAppointments').addEventListener('keyup', function() {
        const query = this.value.toLowerCase().trim();
        const rows = document.querySelectorAll('#appointmentsBody tr');
        let visibleCount = 0;

        rows.forEach((row, index) => {
            const text = row.textContent.toLowerCase();
            const matches = text.includes(query);
            
            if (matches) {
                row.style.display = '';
                row.style.animation = `fadeIn 0.3s ease ${index * 0.05}s both`;
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
    });

    // Add smooth fade-in animation for rows on load
    document.addEventListener('DOMContentLoaded', function() {
        const rows = document.querySelectorAll('#appointmentsBody tr');
        rows.forEach((row, index) => {
            row.style.opacity = '0';
            row.style.transform = 'translateY(10px)';
            setTimeout(() => {
                row.style.transition = 'all 0.4s ease';
                row.style.opacity = '1';
                row.style.transform = 'translateY(0)';
            }, index * 50);
        });
    });

    // Initialize date dropdowns
    function initializeDateDropdowns() {
        const daySelect = document.getElementById('modal_appointment_day');
        const monthSelect = document.getElementById('modal_appointment_month');
        const yearSelect = document.getElementById('modal_appointment_year');
        
        // Populate days (will be updated based on month/year)
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
        
        // Set current date as default
        const today = new Date();
        monthSelect.value = String(today.getMonth() + 1).padStart(2, '0');
        yearSelect.value = today.getFullYear();
        updateDays();
        daySelect.value = String(today.getDate()).padStart(2, '0');
        updateDateInput();
        
        // Add event listeners
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
    
    // Update hidden date input when day/month/year changes
    function updateDateInput() {
        const day = document.getElementById('modal_appointment_day').value;
        const month = document.getElementById('modal_appointment_month').value;
        const year = document.getElementById('modal_appointment_year').value;
        
        if (day && month && year) {
            const dateInput = document.getElementById('modal_appointment_date');
            dateInput.value = `${year}-${month}-${day}`;
        }
    }

    // Appointment times
    const allTimes = ["09:00 AM","10:00 AM","11:00 AM","12:00 PM","01:00 PM","02:00 PM","03:00 PM","04:00 PM"];
    const staffInput = document.getElementById('modal_staff_id');
    const timeSelect = document.getElementById('modal_appointment_time');

    staffInput.addEventListener('change', loadTimes);

    function loadTimes() {
        const dateInput = document.getElementById('modal_appointment_date');
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
    
    // Convert time to datetime format
    function convertToDateTime(date, time) {
        // Parse the time string and convert to 24-hour format
        const [timePart, period] = time.split(' ');
        let [hours, minutes] = timePart.split(':').map(Number);

        if (period === 'PM' && hours !== 12) hours += 12;
        if (period === 'AM' && hours === 12) hours = 0;

        // Return in format: YYYY-MM-DD HH:mm:ss
        return `${date} ${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:00`;
    }

    // Initialize date dropdowns when modal is shown
    document.getElementById('createAppointmentModal').addEventListener('shown.bs.modal', function() {
        initializeDateDropdowns();
    });
</script>
@endsection
