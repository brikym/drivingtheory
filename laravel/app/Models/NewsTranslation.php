<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NewsTranslation extends Model
{
    protected $fillable = [
        'news_id',
        'locale',
        'title',
        'content',
        'slug',
    ];

    public function news(): BelongsTo
    {
        return $this->belongsTo(News::class);
    }
}
