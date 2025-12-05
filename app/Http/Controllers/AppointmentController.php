<?php
namespace App\Http\Controllers;

use App\Models\Appointments;
use App\Models\RecurringAppointment;
use App\Models\AppointmentReminder;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AppointmentController extends Controller
{
    public function index(Request $request) {
        $query = Appointments::with(['user', 'recurringAppointment'])->orderBy('appointment_time','desc');

        // Search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('visitor_name', 'like', "%{$request->search}%")
                  ->orWhere('visitor_phone', 'like', "%{$request->search}%")
                  ->orWhere('visitor_email', 'like', "%{$request->search}%")
                  ->orWhere('purpose', 'like', "%{$request->search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('appointment_time', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('appointment_time', '<=', $request->date_to);
        }

        // Filter by staff
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $appointments = $query->paginate(15);
        $users = User::where('role', 'staff')->get();
        return view('appointments.index', compact('appointments', 'users'));
    }

    public function calendar(Request $request)
    {
        $view = $request->get('view', 'month'); // month, week, day
        $date = $request->get('date', Carbon::now()->format('Y-m-d'));
        $selectedDate = Carbon::parse($date);

        $users = User::where('role', 'staff')->get();
        $selectedUserId = $request->get('user_id');

        $query = Appointments::with('user')
            ->where('status', '!=', 'canceled');

        if ($selectedUserId) {
            $query->where('user_id', $selectedUserId);
        }

        switch ($view) {
            case 'day':
                $startDate = $selectedDate->copy()->startOfDay();
                $endDate = $selectedDate->copy()->endOfDay();
                break;
            case 'week':
                $startDate = $selectedDate->copy()->startOfWeek();
                $endDate = $selectedDate->copy()->endOfWeek();
                break;
            default: // month
                $startDate = $selectedDate->copy()->startOfMonth();
                $endDate = $selectedDate->copy()->endOfMonth();
        }

        $appointments = $query->whereBetween('appointment_time', [$startDate, $endDate])
            ->orderBy('appointment_time')
            ->get()
            ->groupBy(function($apt) {
                return $apt->appointment_time->format('Y-m-d');
            });

        return view('appointments.calendar', compact('appointments', 'view', 'selectedDate', 'users', 'selectedUserId'));
    }

    public function create() {
        $users = User::where('role', 'staff')->get();
        return view('appointments.create', compact('users'));
    }

    public function store(Request $r) {
        $r->validate([
            'visitor_name'=>'required',
            'user_id'=>'required|exists:users,id',
            'appointment_time'=>'required|date',
            'purpose'=>'required',
            'duration_minutes' => 'nullable|integer|min:15|max:480',
            'visitor_email' => 'nullable|email',
        ]);

        $appointmentTime = Carbon::parse($r->appointment_time);
        $duration = $r->duration_minutes ?? 60;
        $endTime = $appointmentTime->copy()->addMinutes($duration);

        // Enhanced conflict detection
        $conflict = Appointments::where('user_id', $r->user_id)
            ->where('status', '!=', 'canceled')
            ->where(function($query) use ($appointmentTime, $endTime, $duration) {
                $query->whereBetween('appointment_time', [$appointmentTime, $endTime->copy()->subSecond()])
                      ->orWhere(function($q) use ($appointmentTime, $duration) {
                          $q->where('appointment_time', '<', $appointmentTime)
                            ->whereRaw("DATE_ADD(appointment_time, INTERVAL COALESCE(duration_minutes, 60) MINUTE) > ?", [$appointmentTime]);
                      });
            })
            ->exists();

        if ($conflict) {
            return back()->withErrors(['appointment_time' => 'This time slot conflicts with an existing appointment.'])->withInput();
        }

        $appointment = Appointments::create([
            'visitor_name' => $r->visitor_name,
            'visitor_phone' => $r->visitor_phone,
            'visitor_email' => $r->visitor_email,
            'user_id' => $r->user_id,
            'appointment_time' => $appointmentTime,
            'duration_minutes' => $duration,
            'purpose' => $r->purpose,
            'notes' => $r->notes,
            'status' => 'pending',
        ]);

        // Create default reminders
        $this->createDefaultReminders($appointment);

        return redirect()->route('appointments.index')->with('success','Appointment created.');
    }

    public function reschedule(Request $request, Appointments $appointment)
    {
        $request->validate([
            'appointment_time' => 'required|date|after:now',
            'reason' => 'nullable|string|max:500',
        ]);

        $newTime = Carbon::parse($request->appointment_time);
        $duration = $appointment->duration_minutes ?? 60;
        $endTime = $newTime->copy()->addMinutes($duration);

        // Check for conflicts
        $conflict = Appointments::where('user_id', $appointment->user_id)
            ->where('id', '!=', $appointment->id)
            ->where('status', '!=', 'canceled')
            ->where(function($query) use ($newTime, $endTime, $duration) {
                $query->whereBetween('appointment_time', [$newTime, $endTime->copy()->subSecond()])
                      ->orWhere(function($q) use ($newTime, $duration) {
                          $q->where('appointment_time', '<', $newTime)
                            ->whereRaw("DATE_ADD(appointment_time, INTERVAL COALESCE(duration_minutes, 60) MINUTE) > ?", [$newTime]);
                      });
            })
            ->exists();

        if ($conflict) {
            return back()->withErrors(['appointment_time' => 'The new time conflicts with an existing appointment.'])->withInput();
        }

        // Store original time if first reschedule
        if (!$appointment->original_appointment_time) {
            $appointment->original_appointment_time = $appointment->appointment_time;
        }

        $appointment->update([
            'appointment_time' => $newTime,
            'rescheduled_at' => Carbon::now(),
            'rescheduled_by' => auth()->id(),
            'notes' => ($appointment->notes ?? '') . "\n\nRescheduled: " . ($request->reason ?? 'No reason provided'),
        ]);

        // Update reminders
        $this->updateRemindersForReschedule($appointment);

        return back()->with('success', 'Appointment rescheduled successfully.');
    }

    public function checkConflict(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'appointment_time' => 'required|date',
            'duration_minutes' => 'nullable|integer|min:15|max:480',
            'exclude_id' => 'nullable|exists:appointments,id',
        ]);

        $appointmentTime = Carbon::parse($request->appointment_time);
        $duration = $request->duration_minutes ?? 60;
        $endTime = $appointmentTime->copy()->addMinutes($duration);

        $conflicts = Appointments::where('user_id', $request->user_id)
            ->where('status', '!=', 'canceled')
            ->where(function($query) use ($appointmentTime, $endTime, $duration) {
                $query->whereBetween('appointment_time', [$appointmentTime, $endTime->copy()->subSecond()])
                      ->orWhere(function($q) use ($appointmentTime, $duration) {
                          $q->where('appointment_time', '<', $appointmentTime)
                            ->whereRaw("DATE_ADD(appointment_time, INTERVAL COALESCE(duration_minutes, 60) MINUTE) > ?", [$appointmentTime]);
                      });
            });

        if ($request->exclude_id) {
            $conflicts->where('id', '!=', $request->exclude_id);
        }

        $conflictingAppointments = $conflicts->get();

        return response()->json([
            'has_conflict' => $conflictingAppointments->isNotEmpty(),
            'conflicts' => $conflictingAppointments->map(function($apt) {
                return [
                    'id' => $apt->id,
                    'visitor_name' => $apt->visitor_name,
                    'time' => $apt->appointment_time->format('Y-m-d H:i'),
                    'duration' => $apt->duration_minutes ?? 60,
                ];
            }),
        ]);
    }

    public function updateStatus(Appointments $appointment, Request $r) {
        $r->validate(['status'=>'required|in:pending,confirmed,canceled']);
        $appointment->update(['status'=>$r->status]);
        return back()->with('success','Appointment status updated.');
    }

    public function destroy(Appointments $appointment) {
        $appointment->delete();
        return back()->with('success','Appointment removed.');
    }

    public function getUnavailableTimes(Request $r)
    {
        $r->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date'
        ]);

        $date = $r->date;
        $duration = $r->get('duration_minutes', 60);

        $appointments = Appointments::where('user_id', $r->user_id)
            ->where('status', '!=', 'canceled')
            ->whereDate('appointment_time', $date)
            ->get();

        $unavailableSlots = [];
        foreach ($appointments as $apt) {
            $start = Carbon::parse($apt->appointment_time);
            $aptDuration = $apt->duration_minutes ?? 60;
            $end = $start->copy()->addMinutes($aptDuration);
            
            // Add all 15-minute slots that are occupied
            $current = $start->copy();
            while ($current < $end) {
                $unavailableSlots[] = $current->format('H:i');
                $current->addMinutes(15);
            }
        }

        return response()->json([
            'booked_times' => array_unique($unavailableSlots),
            'appointments' => $appointments->map(function($apt) {
                return [
                    'time' => Carbon::parse($apt->appointment_time)->format('h:i A'),
                    'visitor' => $apt->visitor_name,
                    'duration' => $apt->duration_minutes ?? 60,
                ];
            }),
        ]);
    }

    // Recurring Appointments
    public function recurringIndex()
    {
        $recurring = RecurringAppointment::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return view('appointments.recurring.index', compact('recurring'));
    }

    public function createRecurring()
    {
        $users = User::where('role', 'staff')->get();
        return view('appointments.recurring.create', compact('users'));
    }

    public function storeRecurring(Request $request)
    {
        $request->validate([
            'visitor_name' => 'required|string|max:255',
            'visitor_phone' => 'required|string',
            'visitor_email' => 'nullable|email',
            'user_id' => 'required|exists:users,id',
            'purpose' => 'required|string',
            'appointment_time' => 'required|date_format:H:i',
            'duration_minutes' => 'nullable|integer|min:15|max:480',
            'recurrence_type' => 'required|in:daily,weekly,monthly,yearly',
            'recurrence_interval' => 'nullable|integer|min:1',
            'recurrence_days' => 'nullable|array',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'nullable|date|after:start_date',
            'occurrences' => 'nullable|integer|min:1|max:1000',
        ]);

        $recurring = RecurringAppointment::create([
            'visitor_name' => $request->visitor_name,
            'visitor_phone' => $request->visitor_phone,
            'visitor_email' => $request->visitor_email,
            'user_id' => $request->user_id,
            'purpose' => $request->purpose,
            'notes' => $request->notes,
            'appointment_time' => Carbon::parse($request->appointment_time),
            'duration_minutes' => $request->duration_minutes ?? 60,
            'recurrence_type' => $request->recurrence_type,
            'recurrence_interval' => $request->recurrence_interval ?? 1,
            'recurrence_days' => $request->recurrence_days,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => 'active',
            'occurrences' => $request->occurrences,
        ]);

        // Generate initial appointments
        $recurring->generateAppointments($request->start_date, Carbon::parse($request->start_date)->addMonths(3));

        return redirect()->route('appointments.recurring.index')->with('success', 'Recurring appointment created successfully.');
    }

    public function generateRecurringAppointments(RecurringAppointment $recurring)
    {
        $appointments = $recurring->generateAppointments();
        return back()->with('success', count($appointments) . ' appointments generated.');
    }

    // Reminders
    public function createReminder(Request $request, Appointments $appointment)
    {
        $request->validate([
            'type' => 'required|in:email,sms',
            'remind_before_hours' => 'required|integer|min:1|max:168',
            'message' => 'nullable|string|max:1000',
        ]);

        $reminderTime = $appointment->appointment_time->copy()->subHours($request->remind_before_hours);

        AppointmentReminder::create([
            'appointment_id' => $appointment->id,
            'type' => $request->type,
            'remind_before_hours' => $request->remind_before_hours,
            'scheduled_at' => $reminderTime,
            'message' => $request->message,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Reminder created successfully.');
    }

    private function createDefaultReminders(Appointments $appointment)
    {
        // Create 24-hour reminder
        AppointmentReminder::create([
            'appointment_id' => $appointment->id,
            'type' => 'email',
            'remind_before_hours' => 24,
            'scheduled_at' => $appointment->appointment_time->copy()->subHours(24),
            'status' => 'pending',
        ]);

        // Create 2-hour reminder
        AppointmentReminder::create([
            'appointment_id' => $appointment->id,
            'type' => 'email',
            'remind_before_hours' => 2,
            'scheduled_at' => $appointment->appointment_time->copy()->subHours(2),
            'status' => 'pending',
        ]);
    }

    private function updateRemindersForReschedule(Appointments $appointment)
    {
        foreach ($appointment->reminders()->where('status', 'pending')->get() as $reminder) {
            $newScheduledAt = $appointment->appointment_time->copy()->subHours($reminder->remind_before_hours);
            $reminder->update(['scheduled_at' => $newScheduledAt]);
        }
    }
}
