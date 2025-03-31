<?php

namespace Modules\Article\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArticleTranslation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'article_id',
        'bn_title',
        'hi_title',
        'es_title',
        'bn_content',
        'hi_content',
        'es_content',
        'bn_excerpt',
        'hi_excerpt',
        'es_excerpt',
    ];

    /**
     * Get the article that owns the translation.
     */
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }
}
