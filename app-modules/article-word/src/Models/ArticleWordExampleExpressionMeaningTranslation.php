<?php

namespace Modules\ArticleWord\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ArticleWordExampleExpressionMeaningTranslation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'article_word_example_expression_meaning_id',
        'bn_meaning',
        'hi_meaning',
        'es_meaning',
    ];

    /**
     * Get the meaning that owns the translation.
     */
    public function meaning(): BelongsTo
    {
        return $this->belongsTo(ArticleWordExampleExpressionMeaning::class, 'article_word_example_expression_meaning_id');
    }

    /**
     * Get the transliteration for the translation.
     */
    public function transliteration(): HasOne
    {
        return $this->hasOne(ArticleWordExampleExpressionMeaningTransliteration::class);
    }
}
