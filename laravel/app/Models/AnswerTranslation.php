<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnswerTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'answer_id',
        'locale',
        'text',
    ];

    public function answer(): BelongsTo
    {
        return $this->belongsTo(Answer::class);
    }
}


