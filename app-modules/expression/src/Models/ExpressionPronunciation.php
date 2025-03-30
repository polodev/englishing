<?php

namespace Modules\Expression\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExpressionPronunciation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'expression_id',
        'bn_pronunciation',
        'hi_pronunciation',
        'es_pronunciation',
    ];

    /**
     * Get the expression that owns the pronunciation.
     */
    public function expression(): BelongsTo
    {
        return $this->belongsTo(Expression::class);
    }
}
