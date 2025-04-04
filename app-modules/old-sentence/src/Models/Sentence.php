<?php

namespace Modules\Sentence\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Sentence extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'sentence',
        'slug',
        'source',
    ];

    /**
     * Get the pronunciation for the sentence.
     */
    public function pronunciation(): HasOne
    {
        return $this->hasOne(SentencePronunciation::class);
    }

    /**
     * Get the translation for the sentence.
     */
    public function translation(): HasOne
    {
        return $this->hasOne(SentenceTranslation::class);
    }
}
