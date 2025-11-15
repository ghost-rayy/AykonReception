<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointments extends Model
{
    protected $fillable = [
        'visitor_name',
        'visitor_phone',
        'user_id',
        'appointment_time',
        'status',
    ];

    protected $casts = [
        'appointment_time' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function visitor()
    {
        return $this->belongsTo(Visitors::class, 'visitor_id');
    }
}
