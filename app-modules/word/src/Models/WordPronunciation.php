<?php

namespace Modules\Word\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WordPronunciation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'word_id',
        'bn_pronunciation',
        'hi_pronunciation',
        'es_pronunciation',
    ];

    /**
     * Get the word that owns the pronunciation.
     */
    public function word(): BelongsTo
    {
        return $this->belongsTo(Word::class);
    }
}
