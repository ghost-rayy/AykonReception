<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visitors extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'purpose',
        'user_id',
        'check_in_time',
        'check_out_time',
        'status',
        'notes',
        'photo_path',
    ];

    protected $casts = [
        'check_in_time' => 'datetime',
        'check_out_time' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
