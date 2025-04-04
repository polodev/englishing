<?php

namespace Modules\ArticleSentence\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ArticleSentenceSetList extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'article_sentence_set_id',
        'sentence',
        'slug',
        'display_order',
    ];

    /**
     * Get the sentence set that owns the list.
     */
    public function sentenceSet(): BelongsTo
    {
        return $this->belongsTo(ArticleSentenceSet::class, 'article_sentence_set_id');
    }

    /**
     * Get the translation for the sentence.
     */
    public function translation(): HasOne
    {
        return $this->hasOne(ArticleSentenceTranslation::class);
    }
}
