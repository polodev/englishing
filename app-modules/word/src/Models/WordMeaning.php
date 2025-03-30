<?php

namespace Modules\Word\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class WordMeaning extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'word_id',
        'meaning',
        'slug',
    ];

    /**
     * Get the word that owns the meaning.
     */
    public function word(): BelongsTo
    {
        return $this->belongsTo(Word::class);
    }

    /**
     * Get the translation for the word meaning.
     */
    public function translation(): HasOne
    {
        return $this->hasOne(WordMeaningTranslation::class);
    }
}
