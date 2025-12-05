<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'icon',
        'color',
        'notifiable_type',
        'notifiable_id',
        'data',
        'is_read',
        'read_at',
        'expires_at',
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function notifiable()
    {
        return $this->morphTo();
    }

    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => Carbon::now(),
        ]);
    }

    public function markAsUnread()
    {
        $this->update([
            'is_read' => false,
            'read_at' => null,
        ]);
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeForUser($query, $userId = null)
    {
        if ($userId) {
            return $query->where(function($q) use ($userId) {
                $q->where('user_id', $userId)
                  ->orWhereNull('user_id');
            });
        }
        return $query->whereNull('user_id');
    }

    public function scopeRecent($query, $hours = 24)
    {
        return $query->where('created_at', '>=', Carbon::now()->subHours($hours));
    }

    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    // Static methods for creating notifications
    public static function createAppointmentReminder($appointment, $minutesBefore)
    {
        $appointmentTime = Carbon::parse($appointment->appointment_time);
        $reminderTime = $appointmentTime->copy()->subMinutes($minutesBefore);
        
        if ($reminderTime->isPast()) {
            return null; // Too late to send reminder
        }

        return self::create([
            'user_id' => null, // All receptionists
            'type' => 'appointment_reminder',
            'title' => "Appointment Reminder - {$minutesBefore} minutes",
            'message' => "{$appointment->visitor_name} has an appointment with {$appointment->user->name} in {$minutesBefore} minutes at {$appointmentTime->format('H:i')}",
            'icon' => 'bi-calendar-event',
            'color' => 'warning',
            'notifiable_type' => Appointments::class,
            'notifiable_id' => $appointment->id,
            'data' => [
                'appointment_id' => $appointment->id,
                'visitor_name' => $appointment->visitor_name,
                'staff_name' => $appointment->user->name,
                'appointment_time' => $appointmentTime->toDateTimeString(),
                'minutes_before' => $minutesBefore,
            ],
            'expires_at' => $appointmentTime->copy()->addMinutes(30), // Expire 30 min after appointment
        ]);
    }

    public static function createVisitorArrival($visitor)
    {
        return self::create([
            'user_id' => null, // All receptionists
            'type' => 'visitor_arrival',
            'title' => 'New Visitor Arrived',
            'message' => "{$visitor->name} has checked in to see {$visitor->user->name}",
            'icon' => 'bi-person-check',
            'color' => 'info',
            'notifiable_type' => Visitors::class,
            'notifiable_id' => $visitor->id,
            'data' => [
                'visitor_id' => $visitor->id,
                'visitor_name' => $visitor->name,
                'staff_name' => $visitor->user->name,
                'check_in_time' => $visitor->check_in_time->toDateTimeString(),
            ],
            'expires_at' => Carbon::now()->addHours(2), // Expire after 2 hours
        ]);
    }

    public static function createVIPVisitor($visitor)
    {
        return self::create([
            'user_id' => null, // All receptionists
            'type' => 'vip_visitor',
            'title' => 'VIP Visitor Arrived',
            'message' => "VIP visitor {$visitor->name} has checked in to see {$visitor->user->name}",
            'icon' => 'bi-star-fill',
            'color' => 'warning',
            'notifiable_type' => Visitors::class,
            'notifiable_id' => $visitor->id,
            'data' => [
                'visitor_id' => $visitor->id,
                'visitor_name' => $visitor->name,
                'staff_name' => $visitor->user->name,
                'check_in_time' => $visitor->check_in_time->toDateTimeString(),
            ],
            'expires_at' => Carbon::now()->addHours(4), // Expire after 4 hours
        ]);
    }

    public static function createStaffStatusChange($staff, $oldStatus, $newStatus)
    {
        $statusLabels = [
            'free' => 'Available',
            'busy' => 'Busy',
            'away' => 'Away',
        ];

        $oldStatusLabel = $statusLabels[$oldStatus] ?? $oldStatus;
        $newStatusLabel = $statusLabels[$newStatus] ?? $newStatus;

        return self::create([
            'user_id' => null, // All receptionists
            'type' => 'staff_status_change',
            'title' => 'Staff Status Changed',
            'message' => "{$staff->name} status changed from {$oldStatusLabel} to {$newStatusLabel}",
            'icon' => $newStatus === 'busy' ? 'bi-calendar-x' : 'bi-check-circle',
            'color' => $newStatus === 'busy' ? 'warning' : 'success',
            'notifiable_type' => User::class,
            'notifiable_id' => $staff->id,
            'data' => [
                'staff_id' => $staff->id,
                'staff_name' => $staff->name,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
            ],
            'expires_at' => Carbon::now()->addHours(1), // Expire after 1 hour
        ]);
    }

    public static function createOverdueCheckout($visitor, $hoursOverdue)
    {
        return self::create([
            'user_id' => null, // All receptionists
            'type' => 'overdue_checkout',
            'title' => 'Overdue Checkout',
            'message' => "{$visitor->name} has been checked in for {$hoursOverdue} hours without checkout",
            'icon' => 'bi-clock-history',
            'color' => 'danger',
            'notifiable_type' => Visitors::class,
            'notifiable_id' => $visitor->id,
            'data' => [
                'visitor_id' => $visitor->id,
                'visitor_name' => $visitor->name,
                'check_in_time' => $visitor->check_in_time->toDateTimeString(),
                'hours_overdue' => $hoursOverdue,
            ],
            'expires_at' => Carbon::now()->addHours(24), // Expire after 24 hours
        ]);
    }
}
