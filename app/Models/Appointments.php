<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Appointments extends Model
{
    protected $fillable = [
        'visitor_name',
        'visitor_phone',
        'visitor_email',
        'user_id',
        'appointment_time',
        'duration_minutes',
        'status',
        'purpose',
        'notes',
        'recurring_appointment_id',
        'original_appointment_time',
        'rescheduled_at',
        'rescheduled_by',
    ];

    protected $casts = [
        'appointment_time' => 'datetime',
        'original_appointment_time' => 'datetime',
        'rescheduled_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function visitor()
    {
        return $this->belongsTo(Visitors::class, 'visitor_id');
    }

    public function recurringAppointment()
    {
        return $this->belongsTo(RecurringAppointment::class);
    }

    public function reminders()
    {
        return $this->hasMany(AppointmentReminder::class);
    }

    public function rescheduledBy()
    {
        return $this->belongsTo(User::class, 'rescheduled_by');
    }

    public function getEndTimeAttribute()
    {
        if ($this->appointment_time && $this->duration_minutes) {
            return $this->appointment_time->copy()->addMinutes($this->duration_minutes);
        }
        return null;
    }

    public function isRecurring()
    {
        return !is_null($this->recurring_appointment_id);
    }

    public function isRescheduled()
    {
        return !is_null($this->rescheduled_at);
    }

    public function hasConflict($excludeId = null)
    {
        $query = static::where('user_id', $this->user_id)
            ->where('status', '!=', 'canceled')
            ->where(function($q) {
                $q->whereBetween('appointment_time', [
                    $this->appointment_time,
                    $this->end_time
                ])
                ->orWhereBetween('appointment_time', [
                    $this->appointment_time->copy()->subMinutes($this->duration_minutes ?? 60),
                    $this->appointment_time
                ]);
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    public function scopeUpcoming($query)
    {
        return $query->where('appointment_time', '>', Carbon::now())
            ->where('status', '!=', 'canceled');
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('appointment_time', $date);
    }

    public function scopeForDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('appointment_time', [$startDate, $endDate]);
    }
}
