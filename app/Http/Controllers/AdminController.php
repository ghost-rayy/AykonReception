<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Visitors;
use App\Models\Appointments;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->isAdmin()) {
                abort(403, 'Unauthorized access. Admin privileges required.');
            }
            return $next($request);
        });
    }

    /**
     * Admin Dashboard
     */
    public function dashboard()
    {
        // Statistics
        $stats = [
            'total_users' => User::count(),
            'total_staff' => User::where('role', 'staff')->count(),
            'total_receptionists' => User::where('role', 'receptionist')->count(),
            'total_admins' => User::where('role', 'admin')->count(),
            'visitors_today' => Visitors::whereDate('created_at', today())->count(),
            'visitors_this_week' => Visitors::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count(),
            'visitors_this_month' => Visitors::whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->count(),
            'total_visitors' => Visitors::count(),
            'checked_in_now' => Visitors::whereNull('check_out_time')->count(),
            'appointments_today' => Appointments::whereDate('appointment_time', today())->count(),
            'appointments_upcoming' => Appointments::where('appointment_time', '>', now())->count(),
            'appointments_pending' => Appointments::where('status', 'pending')->count(),
            'total_appointments' => Appointments::count(),
            'messages_today' => Message::whereDate('created_at', today())->count(),
        ];

        // Recent Activity
        $recentVisitors = Visitors::with('user')->orderBy('created_at', 'desc')->limit(10)->get();
        $recentAppointments = Appointments::with('user')->orderBy('created_at', 'desc')->limit(10)->get();
        $recentUsers = User::orderBy('created_at', 'desc')->limit(5)->get();

        // Charts Data
        $visitorTrends = $this->getVisitorTrends();
        $appointmentStatusBreakdown = $this->getAppointmentStatusBreakdown();
        $userRoleDistribution = $this->getUserRoleDistribution();

        return view('admin.dashboard', compact('stats', 'recentVisitors', 'recentAppointments', 'recentUsers', 'visitorTrends', 'appointmentStatusBreakdown', 'userRoleDistribution'));
    }

    /**
     * User Management - List all users
     */
    public function users()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show create user form
     */
    public function createUser()
    {
        return view('admin.users.create');
    }

    /**
     * Store new user
     */
    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,receptionist,staff',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'status' => $request->role === 'staff' ? 'free' : null,
        ]);

        return redirect()->route('admin.users')->with('success', 'User created successfully.');
    }

    /**
     * Show edit user form
     */
    public function editUser($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update user
     */
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'phone' => 'required|string|max:20|unique:users,phone,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:admin,receptionist,staff',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => $request->role,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Only set status for staff
        if ($request->role === 'staff' && !$user->status) {
            $data['status'] = 'free';
        } elseif ($request->role !== 'staff') {
            $data['status'] = null;
        }

        $user->update($data);

        return redirect()->route('admin.users')->with('success', 'User updated successfully.');
    }

    /**
     * Delete user
     */
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);

        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users')->with('error', 'You cannot delete your own account.');
        }

        // Check if user has related data
        $hasVisitors = Visitors::where('user_id', $user->id)->exists();
        $hasAppointments = Appointments::where('user_id', $user->id)->exists();

        if ($hasVisitors || $hasAppointments) {
            return redirect()->route('admin.users')->with('error', 'Cannot delete user with associated visitors or appointments.');
        }

        $user->delete();

        return redirect()->route('admin.users')->with('success', 'User deleted successfully.');
    }

    /**
     * System Settings
     */
    public function settings()
    {
        $settings = [
            'site_name' => config('app.name', 'Aykon Reception'),
            'timezone' => config('app.timezone', 'UTC'),
            'date_format' => 'Y-m-d',
            'time_format' => 'H:i',
            'visitor_photo_required' => true,
            'auto_checkout_hours' => 8,
            'appointment_reminder_hours' => 24,
            'max_visitors_per_day' => null,
        ];

        return view('admin.settings', compact('settings'));
    }

    /**
     * Update System Settings
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'site_name' => 'required|string|max:255',
            'timezone' => 'required|string',
            'date_format' => 'required|string',
            'time_format' => 'required|string',
            'visitor_photo_required' => 'boolean',
            'auto_checkout_hours' => 'nullable|integer|min:1|max:24',
            'appointment_reminder_hours' => 'nullable|integer|min:1|max:168',
            'max_visitors_per_day' => 'nullable|integer|min:1',
        ]);

        // In a real application, you would save these to a settings table or config file
        // For now, we'll just show a success message
        return redirect()->route('admin.settings')->with('success', 'Settings updated successfully. (Note: In production, implement proper settings storage)');
    }

    /**
     * System Logs / Activity Logs
     */
    public function logs()
    {
        // In a real application, you would have an ActivityLog model
        // For now, we'll show recent system activities from existing models
        $activities = collect();

        // Get recent user activities
        $recentUsers = User::orderBy('updated_at', 'desc')->limit(20)->get();
        foreach ($recentUsers as $user) {
            $activities->push([
                'type' => 'user',
                'action' => $user->created_at->eq($user->updated_at) ? 'created' : 'updated',
                'description' => "User {$user->name} ({$user->role}) was " . ($user->created_at->eq($user->updated_at) ? 'created' : 'updated'),
                'user' => $user->name,
                'timestamp' => $user->updated_at,
            ]);
        }

        // Get recent visitor activities
        $recentVisitors = Visitors::orderBy('created_at', 'desc')->limit(20)->get();
        foreach ($recentVisitors as $visitor) {
            $activities->push([
                'type' => 'visitor',
                'action' => $visitor->check_out_time ? 'checked_out' : 'checked_in',
                'description' => "Visitor {$visitor->name} " . ($visitor->check_out_time ? 'checked out' : 'checked in'),
                'user' => $visitor->user->name ?? 'System',
                'timestamp' => $visitor->check_out_time ?? $visitor->check_in_time,
            ]);
        }

        // Sort by timestamp and paginate
        $activities = $activities->sortByDesc('timestamp')->take(50);

        return view('admin.logs', compact('activities'));
    }

    /**
     * Data Export
     */
    public function exportData(Request $request)
    {
        $type = $request->get('type', 'visitors');
        $format = $request->get('format', 'csv');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        switch ($type) {
            case 'visitors':
                return $this->exportVisitors($format, $startDate, $endDate);
            case 'appointments':
                return $this->exportAppointments($format, $startDate, $endDate);
            case 'users':
                return $this->exportUsers($format);
            default:
                return redirect()->route('admin.dashboard')->with('error', 'Invalid export type.');
        }
    }

    /**
     * Database Backup (simplified - in production use proper backup tools)
     */
    public function backup()
    {
        // In production, use proper backup tools like spatie/laravel-backup
        return redirect()->route('admin.dashboard')->with('info', 'Backup feature requires proper backup package installation.');
    }

    /**
     * System Health Check
     */
    public function health()
    {
        $health = [
            'database' => $this->checkDatabase(),
            'storage' => $this->checkStorage(),
            'cache' => $this->checkCache(),
            'disk_space' => $this->getDiskSpace(),
        ];

        return view('admin.health', compact('health'));
    }

    // Helper Methods

    private function getVisitorTrends()
    {
        $last7Days = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $count = Visitors::whereDate('created_at', $date)->count();
            $last7Days[] = [
                'date' => $date->format('M d'),
                'count' => $count,
            ];
        }
        return $last7Days;
    }

    private function getAppointmentStatusBreakdown()
    {
        return [
            'pending' => Appointments::where('status', 'pending')->count(),
            'confirmed' => Appointments::where('status', 'confirmed')->count(),
            'canceled' => Appointments::where('status', 'canceled')->count(),
        ];
    }

    private function getUserRoleDistribution()
    {
        return [
            'admin' => User::where('role', 'admin')->count(),
            'receptionist' => User::where('role', 'receptionist')->count(),
            'staff' => User::where('role', 'staff')->count(),
        ];
    }

    private function exportVisitors($format, $startDate, $endDate)
    {
        $query = Visitors::with('user')->orderBy('created_at', 'desc');
        
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        $visitors = $query->get();

        if ($format === 'csv') {
            $filename = 'visitors_' . date('Y-m-d_His') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function() use ($visitors) {
                $file = fopen('php://output', 'w');
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
                fputcsv($file, ['Name', 'Phone', 'Purpose', 'Staff', 'Check In', 'Check Out', 'Status']);
                
                foreach ($visitors as $visitor) {
                    fputcsv($file, [
                        $visitor->name,
                        $visitor->phone,
                        $visitor->purpose,
                        $visitor->user->name ?? 'N/A',
                        $visitor->check_in_time ? $visitor->check_in_time->format('Y-m-d H:i:s') : 'N/A',
                        $visitor->check_out_time ? $visitor->check_out_time->format('Y-m-d H:i:s') : 'N/A',
                        $visitor->status,
                    ]);
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        return redirect()->back()->with('error', 'Only CSV format is currently supported.');
    }

    private function exportAppointments($format, $startDate, $endDate)
    {
        $query = Appointments::with('user')->orderBy('appointment_time', 'desc');
        
        if ($startDate && $endDate) {
            $query->whereBetween('appointment_time', [$startDate, $endDate]);
        }

        $appointments = $query->get();

        if ($format === 'csv') {
            $filename = 'appointments_' . date('Y-m-d_His') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function() use ($appointments) {
                $file = fopen('php://output', 'w');
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                fputcsv($file, ['Visitor Name', 'Visitor Phone', 'Staff', 'Appointment Time', 'Purpose', 'Status']);
                
                foreach ($appointments as $appointment) {
                    fputcsv($file, [
                        $appointment->visitor_name,
                        $appointment->visitor_phone,
                        $appointment->user->name ?? 'N/A',
                        $appointment->appointment_time ? Carbon::parse($appointment->appointment_time)->format('Y-m-d H:i:s') : 'N/A',
                        $appointment->purpose,
                        $appointment->status,
                    ]);
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        return redirect()->back()->with('error', 'Only CSV format is currently supported.');
    }

    private function exportUsers($format)
    {
        $users = User::orderBy('created_at', 'desc')->get();

        if ($format === 'csv') {
            $filename = 'users_' . date('Y-m-d_His') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function() use ($users) {
                $file = fopen('php://output', 'w');
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                fputcsv($file, ['Name', 'Email', 'Phone', 'Role', 'Status', 'Created At']);
                
                foreach ($users as $user) {
                    fputcsv($file, [
                        $user->name,
                        $user->email,
                        $user->phone,
                        $user->role,
                        $user->status ?? 'N/A',
                        $user->created_at->format('Y-m-d H:i:s'),
                    ]);
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        return redirect()->back()->with('error', 'Only CSV format is currently supported.');
    }

    private function checkDatabase()
    {
        try {
            DB::connection()->getPdo();
            return ['status' => 'healthy', 'message' => 'Database connection successful'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    private function checkStorage()
    {
        try {
            $writable = is_writable(storage_path());
            return [
                'status' => $writable ? 'healthy' : 'warning',
                'message' => $writable ? 'Storage is writable' : 'Storage may not be writable'
            ];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    private function checkCache()
    {
        try {
            cache()->put('health_check', 'ok', 1);
            $result = cache()->get('health_check') === 'ok';
            return [
                'status' => $result ? 'healthy' : 'warning',
                'message' => $result ? 'Cache is working' : 'Cache may not be working properly'
            ];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    private function getDiskSpace()
    {
        $bytes = disk_free_space(base_path());
        $total = disk_total_space(base_path());
        $used = $total - $bytes;
        $percent = $total > 0 ? round(($used / $total) * 100, 2) : 0;

        return [
            'free' => $this->formatBytes($bytes),
            'used' => $this->formatBytes($used),
            'total' => $this->formatBytes($total),
            'percent' => $percent,
            'status' => $percent > 90 ? 'warning' : ($percent > 80 ? 'caution' : 'healthy'),
        ];
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}

