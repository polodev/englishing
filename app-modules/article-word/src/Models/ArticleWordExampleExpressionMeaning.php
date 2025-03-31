<?php

namespace Modules\ArticleWord\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ArticleWordExampleExpressionMeaning extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'article_word_example_expression_id',
        'meaning',
    ];

    /**
     * Get the example expression that owns the meaning.
     */
    public function exampleExpression(): BelongsTo
    {
        return $this->belongsTo(ArticleWordExampleExpression::class, 'article_word_example_expression_id');
    }

    /**
     * Get the translation for the meaning.
     */
    public function translation(): HasOne
    {
        return $this->hasOne(ArticleWordExampleExpressionMeaningTranslation::class);
    }
}
