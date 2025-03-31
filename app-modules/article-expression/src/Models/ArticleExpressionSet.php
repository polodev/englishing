<?php

namespace Modules\ArticleExpression\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\Article\Models\Article;

class ArticleExpressionSet extends Model
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
    ];

    /**
     * Get the article that owns the expression set.
     */
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    /**
     * Get the translation for the expression set.
     */
    public function translation(): HasOne
    {
        return $this->hasOne(ArticleExpressionSetTranslation::class);
    }

    /**
     * Get the lists for the expression set.
     */
    public function lists(): HasMany
    {
        return $this->hasMany(ArticleExpressionSetList::class);
    }
}
