<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Answer extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id',
        'is_correct',
        'external_id',
        'media_content_id',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function translations(): HasMany
    {
        return $this->hasMany(AnswerTranslation::class);
    }

    public function mediaContents(): HasMany
    {
        return $this->hasMany(MediaContent::class, 'model_id');
    }

    public function mediaContent(): BelongsTo
    {
        return $this->belongsTo(MediaContent::class, 'media_content_id');
    }
}


