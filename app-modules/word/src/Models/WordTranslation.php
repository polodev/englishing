<?php

namespace Modules\Word\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WordTranslation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * Get the word that owns the translation.
     */
    public function word(): BelongsTo
    {
        return $this->belongsTo(Word::class);
    }

    /**
     * Get the meaning that owns the translation.
     */
    public function meaning(): BelongsTo
    {
        return $this->belongsTo(WordMeaning::class, 'meaning_id');
    }
}
