<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visitors;
use App\Models\Staff;
use App\Models\Appointments;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $visitorsToday = Visitors::whereDate('created_at', today())->count();
        $checkedIn = Visitors::whereDate('created_at', today())->count();
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

        // Get all staff from users table
        $allStaff = User::where('role', 'staff')->orderBy('name')->get();

        // Get staff status counts
        $staffInMeeting = User::where('role', 'staff')->where('status', 'busy')->count();
        $staffFree = User::where('role', 'staff')->where('status', 'free')->count();

        // Get visitors in queue (checked in but not checked out)
        $visitorsInQueue = Visitors::whereNull('check_out_time')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get completed visitors (checked out today)
        $completedVisitors = Visitors::whereNotNull('check_out_time')
            ->whereDate('check_out_time', today())
            ->with('user')
            ->orderBy('check_out_time', 'desc')
            ->get();

        // Get total appointments
        $totalAppointments = Appointments::count();
        
        // Get total staff count
        $totalStaff = User::where('role', 'staff')->count();

        // Get users for appointment modal
        $users = User::where('role', 'staff')->orderBy('name')->get();

        return view('dashboard', compact('visitorsToday', 'checkedIn', 'upcomingAppointments', 'recentVisitors', 'pendingAppointments', 'newMessages', 'allStaff', 'staffInMeeting', 'staffFree', 'visitorsInQueue', 'completedVisitors', 'totalAppointments', 'totalStaff', 'users'));
    }

    public function getStaffStats()
    {
        $userId = auth()->id();

        $stats = [
            'today_visits' => Visitors::where('user_id', $userId)->whereDate('created_at', today())->count(),
            'upcoming' => Visitors::where('user_id', $userId)->whereNull('check_out_time')->count(),
            'completed' => Visitors::where('user_id', $userId)->whereNotNull('check_out_time')->count(),
        ];

        return response()->json($stats);
    }
}
