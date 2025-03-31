<?php

namespace Modules\ArticleDoubleSentence\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArticleDoubleSentenceSetTranslation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'article_double_sentence_set_id',
        'bn_title',
        'hi_title',
        'es_title',
        'bn_content',
        'hi_content',
        'es_content',
    ];

    /**
     * Get the double sentence set that owns the translation.
     */
    public function doubleSentenceSet(): BelongsTo
    {
        return $this->belongsTo(ArticleDoubleSentenceSet::class, 'article_double_sentence_set_id');
    }
}
