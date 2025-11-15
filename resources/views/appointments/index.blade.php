@extends('layouts.app')

@section('title', '📅 Appointments')

@section('content')
<style>
    body {
        background: #f8f9fa;
    }

    /* Page title & search */
    .page-title {
        font-size: 28px;
        font-weight: 700;
        color: #0d6efd;
    }

    .search-input {
        border-radius: 50px;
        padding-left: 40px;
        transition: 0.3s;
    }
    .search-input:focus {
        box-shadow: 0 0 15px rgba(0, 123, 255, 0.3);
    }

    /* Table styling */
    .table tbody tr {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        cursor: pointer;
    }
    .table tbody tr:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    }

    /* Modal styling */
    .modal-content {
        border-radius: 1rem;
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    }

    .btn-primary, .btn-danger {
        transition: 0.2s;
    }
    .btn-primary:hover {
        background: #0b5ed7;
        transform: translateY(-2px);
    }
    .btn-danger:hover {
        background: #dc3545;
        transform: translateY(-2px);
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="page-title">Appointments</h4>
    <div class="d-flex align-items-center gap-3">
        <div class="position-relative">
            <i class="bi bi-search" style="position:absolute; top:12px; left:15px; color:#6c757d;"></i>
            <input type="text" id="searchAppointments" class="form-control search-input" placeholder="Search by visitor, phone, or staff..." style="width: 350px;">
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createAppointmentModal">
            + New Appointment
        </button>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0" id="appointmentsTable">
            <thead class="table-light">
                <tr>
                    <th>Visitor</th>
                    <th>Phone</th>
                    <th>Staff</th>
                    <th>Appointment Time</th>
                    <th>Status</th>
                    <th width="150">Actions</th>
                </tr>
            </thead>
            <tbody id="appointmentsBody">
                @foreach($appointments as $a)
                <tr>
                    <td>{{ $a->visitor_name }}</td>
                    <td>{{ $a->visitor_phone }}</td>
                    <td>{{ $a->user->name }}</td>
                    <td>{{ \Carbon\Carbon::parse($a->appointment_time)->format('Y-m-d h:i A') }}</td>
                    <td>
                        <form action="{{ route('appointments.updateStatus', $a) }}" method="POST">
                            @csrf
                            <select name="status" onchange="this.form.submit()" class="form-select form-select-sm rounded-pill shadow-sm" {{ in_array($a->status, ['confirmed', 'canceled']) ? 'disabled' : '' }}>
                                <option value="pending" {{ $a->status=='pending'?'selected':'' }}>Pending</option>
                                <option value="confirmed" {{ $a->status=='confirmed'?'selected':'' }}>Confirmed</option>
                                <option value="canceled" {{ $a->status=='canceled'?'selected':'' }}>Canceled</option>
                            </select>
                        </form>
                    </td>
                    <td>
                        <form action="{{ route('appointments.destroy', $a) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm" onclick="return confirm('Delete this appointment?')">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-3">
            {{ $appointments->links() }}
        </div>
    </div>
</div>

<!-- Create Appointment Modal -->
<div class="modal fade" id="createAppointmentModal" tabindex="-1" aria-labelledby="createAppointmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 shadow-lg border-0" style="background: #fefefe;">
            <div class="modal-header border-0 pb-0">
                <div>
                    <h5 class="modal-title fw-bold" id="createAppointmentModalLabel">New Appointment</h5>
                    <small class="text-muted">Schedule a visitor appointment</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body pt-3">
                <form action="{{ route('appointments.store') }}" method="POST" id="createAppointmentForm">
                    @csrf

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Visitor Name</label>
                            <input type="text" name="visitor_name" class="form-control form-control-lg rounded-3 shadow-sm" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Visitor Phone</label>
                            <input type="text" name="visitor_phone" class="form-control form-control-lg rounded-3 shadow-sm">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Select Staff</label>
                        <select id="modal_staff_id" name="user_id" class="form-select form-select-lg rounded-3 shadow-sm" required>
                            <option value="">-- Select Staff --</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}">{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Appointment Date</label>
                            <input type="date" id="modal_appointment_date" class="form-control form-control-lg rounded-3 shadow-sm" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Appointment Time</label>
                            <select id="modal_appointment_time" name="appointment_time" class="form-select form-select-lg rounded-3 shadow-sm" required>
                                <option value="">-- Select Time --</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 shadow-sm">
                        <i class="bi bi-calendar-plus me-2"></i> Schedule Appointment
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Search appointments
    document.getElementById('searchAppointments').addEventListener('keyup', function() {
        const query = this.value.toLowerCase();
        document.querySelectorAll('#appointmentsBody tr').forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(query) ? '' : 'none';
        });
    });

    // Appointment times
    const allTimes = ["09:00 AM","10:00 AM","11:00 AM","12:00 PM","01:00 PM","02:00 PM","03:00 PM","04:00 PM"];
    const dateInput = document.getElementById('modal_appointment_date');
    const staffInput = document.getElementById('modal_staff_id');
    const timeSelect = document.getElementById('modal_appointment_time');

    dateInput.addEventListener('change', loadTimes);
    staffInput.addEventListener('change', loadTimes);

    function loadTimes() {
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
                opt.value = `${date} ${time}`;
                opt.textContent = booked.includes(time) ? time + ' (Booked)' : time;
                if(booked.includes(time)) opt.disabled = true;
                timeSelect.appendChild(opt);
            });
        })
        .catch(() => {
            timeSelect.innerHTML = '<option value="">Error loading times</option>';
        });
    }
</script>
@endsection
