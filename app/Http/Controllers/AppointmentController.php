<?php
namespace App\Http\Controllers;

use App\Models\Appointments;
use App\Models\User;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index() {
        $appointments = Appointments::with('user')->orderBy('appointment_time','desc')->paginate(15);
        $users = User::where('role', 'staff')->get();
        return view('appointments.index', compact('appointments', 'users'));
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
        ]);

            $exists = Appointments::where('user_id', $r->user_id)
                ->where('appointment_time', $r->appointment_time)
                ->exists();

            if ($exists) {
                return back()->withErrors(['appointment_time' => 'This time is already booked for this staff member.']);
            }

        Appointments::create($r->only(['visitor_name','visitor_phone','user_id','appointment_time']));
        return redirect()->route('appointments.index')->with('success','Appointment created.');
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

        $appointments = Appointments::where('user_id', $r->user_id)
            ->whereDate('appointment_time', $date)
            ->pluck('appointment_time')
            ->map(function($time) {
                return \Carbon\Carbon::parse($time)->format('h:i A');
            });

        return response()->json([
            'booked_times' => $appointments
        ]);
    }

}
