<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AppointmentReminder extends Model
{
    protected $fillable = [
        'appointment_id',
        'type',
        'remind_before_hours',
        'scheduled_at',
        'sent_at',
        'status',
        'message',
        'error_message',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    public function appointment()
    {
        return $this->belongsTo(Appointments::class);
    }

    public function markAsSent()
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => Carbon::now(),
        ]);
    }

    public function markAsFailed($errorMessage = null)
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $errorMessage,
        ]);
    }

    public function isDue()
    {
        return $this->status === 'pending' 
            && $this->scheduled_at 
            && $this->scheduled_at->isPast();
    }
}
