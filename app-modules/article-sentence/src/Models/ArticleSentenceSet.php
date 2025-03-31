<?php

namespace Modules\ArticleSentence\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\Article\Models\Article;

class ArticleSentenceSet extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'article_id',
        'display_order',
        'title',
        'content',
    ];

    /**
     * Get the article that owns the sentence set.
     */
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    /**
     * Get the translation for the sentence set.
     */
    public function translation(): HasOne
    {
        return $this->hasOne(ArticleSentenceSetTranslation::class);
    }

    /**
     * Get the lists for the sentence set.
     */
    public function lists(): HasMany
    {
        return $this->hasMany(ArticleSentenceSetList::class);
    }
}
