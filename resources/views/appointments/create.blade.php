@extends('layouts.app')

@section('title', 'Add Appointment')


@section('content')

<div class="container mt-4">
    <form action="{{ route('appointments.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label class="form-label">Visitor Name</label>
            <input type="text" name="visitor_name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Visitor Phone</label>
            <input type="text" name="visitor_phone" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Select Staff</label>
            <select id="staff_id" name="user_id" class="form-select" required>
                <option value="">-- Select Staff --</option>
                @foreach($users as $u)
                    <option value="{{ $u->id }}">{{ $u->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Appointment Date</label>
            <input type="date" id="appointment_date" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Appointment Time</label>
            <select id="appointment_time" name="appointment_time" class="form-select" required>
                <option value="">-- Select Time --</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Create Appointment</button>
    </form>
</div>

@endsection

@section('scripts')
<script>
const allTimes = [
    "09:00 AM","10:00 AM","11:00 AM",
    "12:00 PM","01:00 PM","02:00 PM",
    "03:00 PM","04:00 PM"
];

document.getElementById('appointment_date').addEventListener('change', loadTimes);
document.getElementById('staff_id').addEventListener('change', loadTimes);

function loadTimes() {
    const date = document.getElementById('appointment_date').value;
    const staffId = document.getElementById('staff_id').value;
    const timeSelect = document.getElementById('appointment_time');

    console.log('Loading times for date:', date, 'staff:', staffId);

    if (!date || !staffId) {
        timeSelect.innerHTML = '<option value="">-- Select Date and Staff First --</option>';
        return;
    }

    fetch(`/appointments/unavailable-times?date=${date}&user_id=${staffId}`)
        .then(res => {
            if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
            return res.json();
        })
        .then(data => {
            console.log('Booked times:', data.booked_times);
            const booked = data.booked_times || [];
            timeSelect.innerHTML = '<option value="">-- Select Time --</option>';

            allTimes.forEach(time => {
                const opt = document.createElement('option');
                opt.value = convertToDateTime(date, time);
                opt.textContent = time;

                if (booked.includes(time)) {
                    opt.disabled = true;
                    opt.textContent += " (Booked)";
                }

                timeSelect.appendChild(opt);
            });
        })
        .catch(error => {
            console.error('Error loading times:', error);
            timeSelect.innerHTML = '<option value="">Error loading times</option>';
        });
}

function convertToDateTime(date, time) {
    // Parse the time string and convert to 24-hour format
    const [timePart, period] = time.split(' ');
    let [hours, minutes] = timePart.split(':').map(Number);

    if (period === 'PM' && hours !== 12) hours += 12;
    if (period === 'AM' && hours === 12) hours = 0;

    // Return in format: YYYY-MM-DD HH:mm:ss
    return `${date} ${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:00`;
}
</script>
@endsection
