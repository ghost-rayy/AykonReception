<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitorCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'color',
        'description',
        'is_active',
        'priority',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'priority' => 'integer',
    ];

    public function visitors()
    {
        return $this->hasMany(Visitors::class, 'category_id');
    }
}
