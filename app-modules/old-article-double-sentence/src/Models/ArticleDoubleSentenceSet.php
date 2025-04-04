<?php

namespace Modules\ArticleDoubleSentence\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\Article\Models\Article;

class ArticleDoubleSentenceSet extends Model
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
     * Get the article that owns the double sentence set.
     */
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    /**
     * Get the translation for the double sentence set.
     */
    public function translation(): HasOne
    {
        return $this->hasOne(ArticleDoubleSentenceSetTranslation::class);
    }

    /**
     * Get the lists for the double sentence set.
     */
    public function lists(): HasMany
    {
        return $this->hasMany(ArticleDoubleSentenceSetList::class);
    }
}
