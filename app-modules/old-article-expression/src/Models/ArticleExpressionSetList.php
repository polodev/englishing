<?php

namespace Modules\ArticleExpression\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ArticleExpressionSetList extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'article_expression_set_id',
        'expression',
        'slug',
        'display_order',
    ];

    /**
     * Get the expression set that owns the list.
     */
    public function expressionSet(): BelongsTo
    {
        return $this->belongsTo(ArticleExpressionSet::class, 'article_expression_set_id');
    }

    /**
     * Get the meaning for the expression.
     */
    public function meaning(): HasOne
    {
        return $this->hasOne(ArticleExpressionMeaning::class);
    }

    /**
     * Get the example sentence for the expression.
     */
    public function exampleSentence(): HasOne
    {
        return $this->hasOne(ArticleExpressionExampleSentence::class);
    }
}
