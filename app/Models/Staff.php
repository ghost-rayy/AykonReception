<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'position',
    ];

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function visitors()
    {
        return $this->hasMany(App\Models\Visitors::class);
    }
}
