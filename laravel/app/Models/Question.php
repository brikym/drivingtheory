<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_code',
        'category_id',
        'is_active',
        'external_id',
        'template_id',
        'points_count',
        'valid_from',
        'valid_to',
        'media_content_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'valid_from' => 'datetime',
        'valid_to' => 'datetime',
    ];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'question_categories');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(QuestionTranslation::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
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


