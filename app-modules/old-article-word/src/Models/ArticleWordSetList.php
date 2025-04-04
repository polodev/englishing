<?php

namespace Modules\ArticleWord\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ArticleWordSetList extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'article_word_set_id',
        'display_order',
        'word',
        'slug',
        'position',
        'phonetic',
        'parts_of_speech',
    ];

    /**
     * Get the word set that owns the list.
     */
    public function wordSet(): BelongsTo
    {
        return $this->belongsTo(ArticleWordSet::class, 'article_word_set_id');
    }

    /**
     * Get the meanings for the list.
     */
    public function meanings(): HasMany
    {
        return $this->hasMany(ArticleWordMeaning::class);
    }

    /**
     * Get the example sentences for the list.
     */
    public function exampleSentences(): HasMany
    {
        return $this->hasMany(ArticleWordExampleSentence::class);
    }
    
    /**
     * Get the example expressions for the list.
     */
    public function exampleExpressions(): HasMany
    {
        return $this->hasMany(ArticleWordExampleExpression::class);
    }
}
