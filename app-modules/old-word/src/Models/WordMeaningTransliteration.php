<?php

namespace Modules\Word\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WordMeaningTransliteration extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'word_meaning_translation_id',
        'bn_transliteration',
        'hi_transliteration',
        'es_transliteration',
    ];

    /**
     * Get the word meaning translation that owns the transliteration.
     */
    public function wordMeaningTranslation(): BelongsTo
    {
        return $this->belongsTo(WordMeaningTranslation::class);
    }
}
