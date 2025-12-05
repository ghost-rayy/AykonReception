<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Visitors extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'company',
        'purpose',
        'user_id',
        'category_id',
        'check_in_time',
        'check_out_time',
        'wait_start_time',
        'wait_duration_minutes',
        'status',
        'notes',
        'internal_notes',
        'photo_path',
        'is_vip',
        'visitor_type',
    ];

    protected $casts = [
        'check_in_time' => 'datetime',
        'check_out_time' => 'datetime',
        'wait_start_time' => 'datetime',
        'is_vip' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(VisitorCategory::class);
    }

    public function feedback()
    {
        return $this->hasMany(VisitorFeedback::class);
    }

    public function startWaitTime()
    {
        if (!$this->wait_start_time) {
            $this->update(['wait_start_time' => Carbon::now()]);
        }
    }

    public function endWaitTime()
    {
        if ($this->wait_start_time) {
            $waitDuration = $this->wait_start_time->diffInMinutes(Carbon::now());
            $this->update([
                'wait_duration_minutes' => $waitDuration,
                'wait_start_time' => null,
            ]);
        }
    }

    public function getVisitDurationAttribute()
    {
        if ($this->check_in_time && $this->check_out_time) {
            return $this->check_in_time->diffInMinutes($this->check_out_time);
        }
        return null;
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeVip($query)
    {
        return $query->where('is_vip', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('visitor_type', $type);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('company', 'like', "%{$search}%")
              ->orWhere('purpose', 'like', "%{$search}%");
        });
    }
}
