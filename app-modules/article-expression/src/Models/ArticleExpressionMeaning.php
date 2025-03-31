<?php

namespace Modules\ArticleExpression\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ArticleExpressionMeaning extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'article_expression_set_list_id',
        'meaning',
    ];

    /**
     * Get the expression list that owns the meaning.
     */
    public function expressionList(): BelongsTo
    {
        return $this->belongsTo(ArticleExpressionSetList::class, 'article_expression_set_list_id');
    }

    /**
     * Get the translation for the meaning.
     */
    public function translation(): HasOne
    {
        return $this->hasOne(ArticleExpressionMeaningTranslation::class);
    }
}
