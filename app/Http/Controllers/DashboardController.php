<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visitors;
use App\Models\Staff;
use App\Models\Appointments;

class DashboardController extends Controller
{
    public function index()
    {
        $visitorsToday = Visitors::whereDate('created_at', today())->count();
        $checkedIn = Visitors::whereNull('check_out_time')->count();
        $upcomingAppointments = Appointments::where('appointment_time', '>', now())->get();

        // Get recent visitors (last 5 check-ins today)
        $recentVisitors = Visitors::whereDate('created_at', today())
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get pending appointments count
        $pendingAppointments = Appointments::where('status', 'pending')->count();

        // Get new messages count (placeholder - you can implement messaging later)
        $newMessages = 0; // Placeholder

        return view('dashboard', compact('visitorsToday', 'checkedIn', 'upcomingAppointments', 'recentVisitors', 'pendingAppointments', 'newMessages'));
        // return response()->json([
        //     'visitors_today' => $visitorsToday,
        //     'checked_in' => $checkedIn,
        //     'upcoming_appointments' => $upcomingAppointments,
        // ]);
    }
}
