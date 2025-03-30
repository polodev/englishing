<?php

namespace Modules\Sentence\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SentenceTranslation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'sentence_id',
        'bn_sentence',
        'hi_sentence',
        'es_sentence',
    ];

    /**
     * Get the sentence that owns the translation.
     */
    public function sentence(): BelongsTo
    {
        return $this->belongsTo(Sentence::class);
    }

    /**
     * Get the transliteration for the sentence translation.
     */
    public function transliteration(): HasOne
    {
        return $this->hasOne(SentenceTransliteration::class);
    }
}
