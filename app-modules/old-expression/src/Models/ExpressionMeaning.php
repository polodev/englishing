<?php

namespace Modules\Expression\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ExpressionMeaning extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'expression_id',
        'meaning',
    ];

    /**
     * Get the expression that owns the meaning.
     */
    public function expression(): BelongsTo
    {
        return $this->belongsTo(Expression::class);
    }

    /**
     * Get the translation for the expression meaning.
     */
    public function translation(): HasOne
    {
        return $this->hasOne(ExpressionMeaningTranslation::class);
    }
}
