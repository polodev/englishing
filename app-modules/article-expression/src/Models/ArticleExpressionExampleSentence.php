<?php

namespace Modules\ArticleExpression\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ArticleExpressionExampleSentence extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'article_expression_set_list_id',
        'sentence',
        'slug',
    ];

    /**
     * Get the expression list that owns the example sentence.
     */
    public function expressionList(): BelongsTo
    {
        return $this->belongsTo(ArticleExpressionSetList::class, 'article_expression_set_list_id');
    }

    /**
     * Get the translation for the example sentence.
     */
    public function translation(): HasOne
    {
        return $this->hasOne(ArticleExpressionExampleSentenceTranslation::class);
    }
}
