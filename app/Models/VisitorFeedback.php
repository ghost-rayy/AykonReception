<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitorFeedback extends Model
{
    protected $table = 'visitor_feedback';

    protected $fillable = [
        'visitor_id',
        'rating',
        'comments',
        'survey_responses',
        'feedback_type',
        'is_anonymous',
        'visitor_email',
        'is_public',
    ];

    protected $casts = [
        'survey_responses' => 'array',
        'is_anonymous' => 'boolean',
        'is_public' => 'boolean',
    ];

    public function visitor()
    {
        return $this->belongsTo(Visitors::class);
    }
}
