<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Appointments;
use App\Models\Visitors;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    /**
     * Get notifications for the current user
     */
    public function index(Request $request)
    {
        $userId = auth()->id();
        $unreadOnly = $request->get('unread_only', false);
        
        $query = Notification::forUser($userId)
            ->where(function($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', Carbon::now());
            })
            ->orderBy('created_at', 'desc');

        if ($unreadOnly) {
            $query->unread();
        }

        $notifications = $query->limit(50)->get();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => Notification::forUser($userId)->unread()->count(),
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        
        // Check if user has access to this notification
        if ($notification->user_id && $notification->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $userId = auth()->id();
        
        Notification::forUser($userId)
            ->unread()
            ->update([
                'is_read' => true,
                'read_at' => Carbon::now(),
            ]);

        return response()->json(['success' => true]);
    }

    /**
     * Delete notification
     */
    public function destroy($id)
    {
        $notification = Notification::findOrFail($id);
        
        // Check if user has access to this notification
        if ($notification->user_id && $notification->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $notification->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Check and create appointment reminders
     * This should be called periodically (every minute via cron or scheduler)
     */
    public function checkAppointmentReminders()
    {
        $now = Carbon::now();
        $reminderIntervals = [15, 5]; // 15 minutes and 5 minutes before

        foreach ($reminderIntervals as $minutes) {
            $reminderTime = $now->copy()->addMinutes($minutes);
            $timeWindowStart = $reminderTime->copy()->subMinute();
            $timeWindowEnd = $reminderTime->copy()->addMinute();

            $appointments = Appointments::where('status', '!=', 'canceled')
                ->whereBetween('appointment_time', [$timeWindowStart, $timeWindowEnd])
                ->with('user')
                ->get();

            foreach ($appointments as $appointment) {
                // Check if reminder already exists
                $existingReminder = Notification::where('type', 'appointment_reminder')
                    ->where('notifiable_type', Appointments::class)
                    ->where('notifiable_id', $appointment->id)
                    ->where('data->minutes_before', $minutes)
                    ->where('created_at', '>=', $now->copy()->subMinutes(2))
                    ->exists();

                if (!$existingReminder) {
                    Notification::createAppointmentReminder($appointment, $minutes);
                }
            }
        }

        return response()->json(['success' => true, 'checked' => Carbon::now()]);
    }

    /**
     * Check for overdue checkouts
     * This should be called periodically (every 15-30 minutes)
     */
    public function checkOverdueCheckouts()
    {
        $overdueThreshold = 4; // Hours
        $now = Carbon::now();

        $overdueVisitors = Visitors::whereNull('check_out_time')
            ->where('check_in_time', '<', $now->copy()->subHours($overdueThreshold))
            ->with('user')
            ->get();

        foreach ($overdueVisitors as $visitor) {
            $hoursOverdue = $now->diffInHours($visitor->check_in_time);

            // Check if notification already exists (within last hour)
            $existingNotification = Notification::where('type', 'overdue_checkout')
                ->where('notifiable_type', Visitors::class)
                ->where('notifiable_id', $visitor->id)
                ->where('created_at', '>=', $now->copy()->subHour())
                ->exists();

            if (!$existingNotification) {
                Notification::createOverdueCheckout($visitor, $hoursOverdue);
            }
        }

        return response()->json(['success' => true, 'checked' => Carbon::now()]);
    }

    /**
     * Check all alerts (appointment reminders + overdue checkouts)
     * This is the main method to call periodically
     */
    public function checkAllAlerts()
    {
        $this->checkAppointmentReminders();
        $this->checkOverdueCheckouts();

        return response()->json([
            'success' => true,
            'checked_at' => Carbon::now()->toDateTimeString(),
        ]);
    }

    /**
     * Get notification count (unread)
     */
    public function getUnreadCount()
    {
        $userId = auth()->id();
        $count = Notification::forUser($userId)
            ->unread()
            ->where(function($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', Carbon::now());
            })
            ->count();

        return response()->json(['count' => $count]);
    }
}
