<?php

namespace Modules\ArticleSentence\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArticleSentenceSetTranslation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'article_sentence_set_id',
        'bn_title',
        'hi_title',
        'es_title',
        'bn_content',
        'hi_content',
        'es_content',
    ];

    /**
     * Get the sentence set that owns the translation.
     */
    public function sentenceSet(): BelongsTo
    {
        return $this->belongsTo(ArticleSentenceSet::class, 'article_sentence_set_id');
    }
}
