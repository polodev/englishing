<?php

namespace Modules\Sentence\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SentenceTranslation extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * Get the sentence that owns the translation.
     */
    public function sentence(): BelongsTo
    {
        return $this->belongsTo(Sentence::class);
    }
}
