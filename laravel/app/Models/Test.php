<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Test extends Model
{
    protected $fillable = [
        'user_id',
        'vehicle_type',
        'status',
        'started_at',
        'completed_at',
        'time_limit_minutes',
        'total_questions',
        'total_points',
        'earned_points',
        'percentage',
        'passed',
        'time_expired',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'passed' => 'boolean',
        'time_expired' => 'boolean',
        'percentage' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function testAnswers(): HasMany
    {
        return $this->hasMany(TestAnswer::class);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    public function isExpired(): bool
    {
        return $this->status === 'expired';
    }

    public function getCurrentQuestionIndex(): int
    {
        return $this->testAnswers()->count();
    }

    public function getRemainingTime(): int
    {
        if ($this->isCompleted() || $this->isExpired()) {
            return 0;
        }

        // Počítat čas od started_at - 30 minut od začátku testu
        $elapsed = now()->timestamp - $this->started_at->timestamp;
        $totalTimeLimit = $this->time_limit_minutes * 60;
        $remaining = $totalTimeLimit - $elapsed;
        
        return max(0, $remaining);
    }



    public function isTimeExpired(): bool
    {
        return $this->getRemainingTime() <= 0;
    }
}
