<?php

namespace Modules\Expression\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExpressionConnection extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'expression_id_1',
        'expression_id_2',
        'type', // 'synonyms', 'antonyms'
    ];

    /**
     * Get the first expression in the connection.
     */
    public function firstExpression(): BelongsTo
    {
        return $this->belongsTo(Expression::class, 'expression_id_1');
    }

    /**
     * Get the second expression in the connection.
     */
    public function secondExpression(): BelongsTo
    {
        return $this->belongsTo(Expression::class, 'expression_id_2');
    }
}
