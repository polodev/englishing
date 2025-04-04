<?php

namespace Modules\Expression\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ExpressionMeaningTranslation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'expression_meaning_id',
        'bn_meaning',
        'hi_meaning',
        'es_meaning',
    ];

    /**
     * Get the expression meaning that owns the translation.
     */
    public function expressionMeaning(): BelongsTo
    {
        return $this->belongsTo(ExpressionMeaning::class);
    }

    /**
     * Get the transliteration for the expression meaning translation.
     */
    public function transliteration(): HasOne
    {
        return $this->hasOne(ExpressionMeaningTransliteration::class);
    }
}
