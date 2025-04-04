<?php

namespace Modules\ArticleWord\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\Article\Models\Article;

class ArticleWordSet extends Model
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
        'column_order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'column_order' => 'array',
    ];

    /**
     * Get the article that owns the word set.
     */
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    /**
     * Get the translation for the word set.
     */
    public function translation(): HasOne
    {
        return $this->hasOne(ArticleWordSetTranslation::class);
    }

    /**
     * Get the lists for the word set.
     */
    public function lists(): HasMany
    {
        return $this->hasMany(ArticleWordSetList::class);
    }
}
