<?php

namespace Modules\ArticleDoubleSentence\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ArticleDoubleSentenceSetList extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'article_double_sentence_set_id',
        'sentence_1',
        'sentence_1_slug',
        'sentence_2',
        'sentence_2_slug',
        'display_order',
    ];

    /**
     * Get the double sentence set that owns the list.
     */
    public function doubleSentenceSet(): BelongsTo
    {
        return $this->belongsTo(ArticleDoubleSentenceSet::class, 'article_double_sentence_set_id');
    }

    /**
     * Get the translation for the double sentence.
     */
    public function translation(): HasOne
    {
        return $this->hasOne(ArticleDoubleSentenceTranslation::class);
    }
}
