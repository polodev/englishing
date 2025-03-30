<?php

namespace Modules\Word\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class WordMeaningTranslation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'word_meaning_id',
        'bn_meaning',
        'hi_meaning',
        'es_meaning',
    ];

    /**
     * Get the word meaning that owns the translation.
     */
    public function wordMeaning(): BelongsTo
    {
        return $this->belongsTo(WordMeaning::class);
    }

    /**
     * Get the transliteration for the word meaning translation.
     */
    public function transliteration(): HasOne
    {
        return $this->hasOne(WordMeaningTransliteration::class);
    }
}
