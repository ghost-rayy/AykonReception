<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class VisitorBlacklist extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'reason',
        'notes',
        'created_by',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isExpired()
    {
        if (!$this->expires_at) {
            return false; // Permanent blacklist
        }
        return $this->expires_at->isPast();
    }

    public function isActive()
    {
        return $this->is_active && !$this->isExpired();
    }

    public static function isBlacklisted($phone = null, $email = null)
    {
        $query = static::where('is_active', true);
        
        if ($phone) {
            $query->where('phone', $phone);
        }
        
        if ($email) {
            $query->orWhere('email', $email);
        }

        return $query->get()->filter(function($item) {
            return $item->isActive();
        })->isNotEmpty();
    }
}
