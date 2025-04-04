<?php

namespace Modules\Expression\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExpressionTranslation extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * Get the expression that owns the translation.
     */
    public function expression(): BelongsTo
    {
        return $this->belongsTo(Expression::class);
    }

    /**
     * Get the meaning that owns the translation.
     */
    public function meaning(): BelongsTo
    {
        return $this->belongsTo(ExpressionMeaning::class, 'expression_meaning_id');
    }
}
