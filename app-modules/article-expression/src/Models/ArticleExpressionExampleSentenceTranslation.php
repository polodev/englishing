<?php

namespace Modules\ArticleExpression\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ArticleExpressionExampleSentenceTranslation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'article_expression_example_sentence_id',
        'bn_sentence',
        'hi_sentence',
        'es_sentence',
    ];

    /**
     * Get the example sentence that owns the translation.
     */
    public function exampleSentence(): BelongsTo
    {
        return $this->belongsTo(ArticleExpressionExampleSentence::class, 'article_expression_example_sentence_id');
    }

    /**
     * Get the transliteration for the translation.
     */
    public function transliteration(): HasOne
    {
        return $this->hasOne(ArticleExpressionExampleSentenceTransliteration::class);
    }
}
