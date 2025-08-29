<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MediaContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'model_id',
        'media_type',
        'media_url',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class, 'model_id');
    }

    public function answer(): BelongsTo
    {
        return $this->belongsTo(Answer::class, 'model_id');
    }
}


