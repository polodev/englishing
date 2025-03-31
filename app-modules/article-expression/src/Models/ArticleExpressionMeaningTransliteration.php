<?php

namespace Modules\ArticleExpression\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArticleExpressionMeaningTransliteration extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'article_expression_meaning_translation_id',
        'bn_transliteration',
        'hi_transliteration',
        'es_transliteration',
    ];

    /**
     * Get the translation that owns the transliteration.
     */
    public function translation(): BelongsTo
    {
        return $this->belongsTo(ArticleExpressionMeaningTranslation::class, 'article_expression_meaning_translation_id');
    }
}
