<?php

namespace Modules\ArticleExpression\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArticleExpressionTranslation extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * Get the expression set list that owns the translation.
     */
    public function expressionSetList(): BelongsTo
    {
        return $this->belongsTo(ArticleExpressionSetList::class, 'article_expression_set_list_id');
    }
}
