<?php

namespace App\Http\Controllers;

use App\Models\Visitors;
use App\Models\Appointments;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function getVisitorReports(Request $request)
    {
        $period = $request->get('period', 'daily'); // daily, weekly, monthly
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $query = Visitors::query();

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        } else {
            switch ($period) {
                case 'weekly':
                    $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                    break;
                case 'monthly':
                    $query->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]);
                    break;
                default: // daily
                    $query->whereDate('created_at', Carbon::today());
            }
        }

        $visitors = $query->get();
        
        // Group by date
        $grouped = $visitors->groupBy(function($visitor) use ($period) {
            if ($period === 'daily') {
                return $visitor->created_at->format('Y-m-d');
            } elseif ($period === 'weekly') {
                return $visitor->created_at->format('Y-W');
            } else {
                return $visitor->created_at->format('Y-m');
            }
        });

        $data = [];
        $labels = [];
        $values = [];

        foreach ($grouped as $key => $group) {
            $labels[] = $this->formatLabel($key, $period);
            $values[] = $group->count();
            $data[] = [
                'period' => $this->formatLabel($key, $period),
                'count' => $group->count(),
                'checked_in' => $group->where('status', 'checked_in')->count(),
                'checked_out' => $group->where('status', 'checked_out')->count(),
            ];
        }

        return response()->json([
            'labels' => $labels,
            'values' => $values,
            'data' => $data,
            'total' => $visitors->count(),
            'checked_in' => $visitors->where('status', 'checked_in')->count(),
            'checked_out' => $visitors->where('status', 'checked_out')->count(),
        ]);
    }

    public function getStaffPerformance(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth());

        $staff = User::where('role', 'staff')->get();

        $performance = $staff->map(function($user) use ($startDate, $endDate) {
            $visitors = Visitors::where('user_id', $user->id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get();

            $appointments = Appointments::where('user_id', $user->id)
                ->whereBetween('appointment_time', [$startDate, $endDate])
                ->get();

            return [
                'id' => $user->id,
                'name' => $user->name,
                'total_visitors' => $visitors->count(),
                'checked_in' => $visitors->where('status', 'checked_in')->count(),
                'checked_out' => $visitors->where('status', 'checked_out')->count(),
                'total_appointments' => $appointments->count(),
                'confirmed_appointments' => $appointments->where('status', 'confirmed')->count(),
                'pending_appointments' => $appointments->where('status', 'pending')->count(),
                'canceled_appointments' => $appointments->where('status', 'canceled')->count(),
                'average_visit_duration' => $this->calculateAverageDuration($visitors),
            ];
        });

        return response()->json([
            'staff' => $performance,
            'labels' => $performance->pluck('name')->toArray(),
            'visitor_counts' => $performance->pluck('total_visitors')->toArray(),
            'appointment_counts' => $performance->pluck('total_appointments')->toArray(),
        ]);
    }

    public function getAppointmentStatistics(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth());

        $appointments = Appointments::whereBetween('appointment_time', [$startDate, $endDate])->get();

        // Status breakdown
        $statusBreakdown = [
            'pending' => $appointments->where('status', 'pending')->count(),
            'confirmed' => $appointments->where('status', 'confirmed')->count(),
            'canceled' => $appointments->where('status', 'canceled')->count(),
        ];

        // Daily appointments
        $dailyAppointments = $appointments->groupBy(function($apt) {
            return Carbon::parse($apt->appointment_time)->format('Y-m-d');
        })->map(function($group) {
            return $group->count();
        });

        // Hourly distribution
        $hourlyDistribution = $appointments->groupBy(function($apt) {
            return Carbon::parse($apt->appointment_time)->format('H:00');
        })->map(function($group) {
            return $group->count();
        })->sortKeys();

        return response()->json([
            'total' => $appointments->count(),
            'status_breakdown' => $statusBreakdown,
            'daily_appointments' => [
                'labels' => $dailyAppointments->keys()->toArray(),
                'values' => $dailyAppointments->values()->toArray(),
            ],
            'hourly_distribution' => [
                'labels' => $hourlyDistribution->keys()->toArray(),
                'values' => $hourlyDistribution->values()->toArray(),
            ],
        ]);
    }

    public function getVisitorTrends(Request $request)
    {
        $days = $request->get('days', 30);
        $startDate = Carbon::now()->subDays($days);
        $endDate = Carbon::now();

        $visitors = Visitors::whereBetween('created_at', [$startDate, $endDate])
            ->get()
            ->groupBy(function($visitor) {
                return $visitor->created_at->format('Y-m-d');
            });

        $appointments = Appointments::whereBetween('appointment_time', [$startDate, $endDate])
            ->get()
            ->groupBy(function($apt) {
                return Carbon::parse($apt->appointment_time)->format('Y-m-d');
            });

        $labels = [];
        $visitorData = [];
        $appointmentData = [];

        $current = $startDate->copy();
        while ($current <= $endDate) {
            $dateKey = $current->format('Y-m-d');
            $labels[] = $current->format('M d');
            $visitorData[] = $visitors->get($dateKey) ? $visitors->get($dateKey)->count() : 0;
            $appointmentData[] = $appointments->get($dateKey) ? $appointments->get($dateKey)->count() : 0;
            $current->addDay();
        }

        return response()->json([
            'labels' => $labels,
            'visitors' => $visitorData,
            'appointments' => $appointmentData,
        ]);
    }

    public function exportReport(Request $request)
    {
        $type = $request->get('type'); // visitor, staff, appointment
        $format = $request->get('format', 'csv'); // csv, excel, pdf
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        switch ($type) {
            case 'visitor':
                return $this->exportVisitorReport($format, $startDate, $endDate);
            case 'staff':
                return $this->exportStaffReport($format, $startDate, $endDate);
            case 'appointment':
                return $this->exportAppointmentReport($format, $startDate, $endDate);
            default:
                return redirect()->back()->with('error', 'Invalid report type');
        }
    }

    private function exportVisitorReport($format, $startDate, $endDate)
    {
        $query = Visitors::with('user')->orderBy('created_at', 'desc');
        
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        $visitors = $query->get();

        if ($format === 'csv') {
            return $this->exportToCSV($visitors, 'visitors', [
                'Name', 'Phone', 'Purpose', 'Staff', 'Check-in Time', 'Check-out Time', 'Status'
            ], function($visitor) {
                return [
                    $visitor->name,
                    $visitor->phone,
                    $visitor->purpose,
                    $visitor->user->name ?? 'N/A',
                    $visitor->check_in_time ? $visitor->check_in_time->format('Y-m-d H:i:s') : 'N/A',
                    $visitor->check_out_time ? $visitor->check_out_time->format('Y-m-d H:i:s') : 'N/A',
                    $visitor->status,
                ];
            });
        }

        // For Excel and PDF, we'll use a simple approach
        // You can install maatwebsite/excel and barryvdh/laravel-dompdf for better support
        return response()->json(['message' => 'Excel and PDF export coming soon']);
    }

    private function exportStaffReport($format, $startDate, $endDate)
    {
        $startDate = $startDate ?: Carbon::now()->startOfMonth();
        $endDate = $endDate ?: Carbon::now()->endOfMonth();

        $staff = User::where('role', 'staff')->get();
        $data = [];

        foreach ($staff as $user) {
            $visitors = Visitors::where('user_id', $user->id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get();

            $appointments = Appointments::where('user_id', $user->id)
                ->whereBetween('appointment_time', [$startDate, $endDate])
                ->get();

            $data[] = [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'total_visitors' => $visitors->count(),
                'total_appointments' => $appointments->count(),
                'confirmed_appointments' => $appointments->where('status', 'confirmed')->count(),
            ];
        }

        if ($format === 'csv') {
            return $this->exportToCSV(collect($data), 'staff_performance', [
                'Name', 'Email', 'Phone', 'Total Visitors', 'Total Appointments', 'Confirmed Appointments'
            ], function($item) {
                return [
                    $item['name'],
                    $item['email'],
                    $item['phone'],
                    $item['total_visitors'],
                    $item['total_appointments'],
                    $item['confirmed_appointments'],
                ];
            });
        }

        return response()->json(['message' => 'Excel and PDF export coming soon']);
    }

    private function exportAppointmentReport($format, $startDate, $endDate)
    {
        $query = Appointments::with('user')->orderBy('appointment_time', 'desc');
        
        if ($startDate && $endDate) {
            $query->whereBetween('appointment_time', [$startDate, $endDate]);
        }

        $appointments = $query->get();

        if ($format === 'csv') {
            return $this->exportToCSV($appointments, 'appointments', [
                'Visitor Name', 'Visitor Phone', 'Staff', 'Appointment Time', 'Purpose', 'Status'
            ], function($appointment) {
                return [
                    $appointment->visitor_name,
                    $appointment->visitor_phone,
                    $appointment->user->name ?? 'N/A',
                    $appointment->appointment_time ? Carbon::parse($appointment->appointment_time)->format('Y-m-d H:i:s') : 'N/A',
                    $appointment->purpose,
                    $appointment->status,
                ];
            });
        }

        return response()->json(['message' => 'Excel and PDF export coming soon']);
    }

    private function exportToCSV($data, $filename, $csvHeaders, $callback)
    {
        $filename = $filename . '_' . date('Y-m-d_His') . '.csv';
        
        $httpHeaders = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $streamCallback = function() use ($data, $csvHeaders, $callback) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Add CSV headers
            fputcsv($file, $csvHeaders);
            
            // Add data
            foreach ($data as $item) {
                fputcsv($file, $callback($item));
            }
            
            fclose($file);
        };

        return response()->stream($streamCallback, 200, $httpHeaders);
    }

    private function formatLabel($key, $period)
    {
        if ($period === 'daily') {
            return Carbon::parse($key)->format('M d, Y');
        } elseif ($period === 'weekly') {
            $parts = explode('-', $key);
            return "Week {$parts[1]}, {$parts[0]}";
        } else {
            return Carbon::parse($key . '-01')->format('M Y');
        }
    }

    private function calculateAverageDuration($visitors)
    {
        $checkedOut = $visitors->filter(function($v) {
            return $v->check_out_time && $v->check_in_time;
        });

        if ($checkedOut->isEmpty()) {
            return 0;
        }

        $totalMinutes = $checkedOut->sum(function($v) {
            return $v->check_in_time->diffInMinutes($v->check_out_time);
        });

        return round($totalMinutes / $checkedOut->count(), 2);
    }
}


