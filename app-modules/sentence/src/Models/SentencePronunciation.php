<?php

namespace Modules\Sentence\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SentencePronunciation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'sentence_id',
        'bn_pronunciation',
        'hi_pronunciation',
        'es_pronunciation',
    ];

    /**
     * Get the sentence that owns the pronunciation.
     */
    public function sentence(): BelongsTo
    {
        return $this->belongsTo(Sentence::class);
    }
}
