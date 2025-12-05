<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class VisitorPreregistration extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'company',
        'purpose',
        'user_id',
        'category_id',
        'expected_arrival_time',
        'notes',
        'status',
        'qr_code',
        'checked_in_at',
        'checked_in_by',
    ];

    protected $casts = [
        'expected_arrival_time' => 'datetime',
        'checked_in_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(VisitorCategory::class);
    }

    public function checkedInBy()
    {
        return $this->belongsTo(User::class, 'checked_in_by');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($preregistration) {
            if (!$preregistration->qr_code) {
                $preregistration->qr_code = Str::random(32);
            }
        });
    }

    public function generateQRCode()
    {
        if (!$this->qr_code) {
            $this->qr_code = Str::random(32);
            $this->save();
        }
        return $this->qr_code;
    }
}
