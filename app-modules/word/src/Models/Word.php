<?php

namespace Modules\Word\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Word extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'word',
        'slug',
    ];

    /**
     * Get the meanings for the word.
     */
    public function meanings(): HasMany
    {
        return $this->hasMany(WordMeaning::class);
    }

    /**
     * Get the pronunciation for the word.
     */
    public function pronunciation()
    {
        return $this->hasOne(WordPronunciation::class);
    }

    /**
     * Get the connections where this word is the first word.
     */
    public function connectionsAsFirst(): HasMany
    {
        return $this->hasMany(WordConnection::class, 'word_id_1');
    }

    /**
     * Get the connections where this word is the second word.
     */
    public function connectionsAsSecond(): HasMany
    {
        return $this->hasMany(WordConnection::class, 'word_id_2');
    }
}
