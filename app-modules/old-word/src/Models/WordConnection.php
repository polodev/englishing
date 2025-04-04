<?php

namespace Modules\Word\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WordConnection extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'word_id_1',
        'word_id_2',
        'type', // 'synonyms', 'antonyms'
    ];

    /**
     * Get the first word in the connection.
     */
    public function firstWord(): BelongsTo
    {
        return $this->belongsTo(Word::class, 'word_id_1');
    }

    /**
     * Get the second word in the connection.
     */
    public function secondWord(): BelongsTo
    {
        return $this->belongsTo(Word::class, 'word_id_2');
    }
}
