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
            <div class="row g-2">
                <div class="col-4">
                    <select id="appointment_day" class="form-select" required>
                        <option value="">Day</option>
                    </select>
                </div>
                <div class="col-4">
                    <select id="appointment_month" class="form-select" required>
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
                    <select id="appointment_year" class="form-select" required>
                        <option value="">Year</option>
                    </select>
                </div>
            </div>
            <input type="hidden" id="appointment_date" name="appointment_date" value="" required>
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

// Initialize date dropdowns
function initializeDateDropdowns() {
    const daySelect = document.getElementById('appointment_day');
    const monthSelect = document.getElementById('appointment_month');
    const yearSelect = document.getElementById('appointment_year');
    
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
    const day = document.getElementById('appointment_day').value;
    const month = document.getElementById('appointment_month').value;
    const year = document.getElementById('appointment_year').value;
    
    if (day && month && year) {
        const dateInput = document.getElementById('appointment_date');
        dateInput.value = `${year}-${month}-${day}`;
    }
}

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

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    initializeDateDropdowns();
});
</script>
@endsection
