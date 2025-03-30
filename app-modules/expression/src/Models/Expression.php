<?php

namespace Modules\Expression\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Expression extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'expression',
        'type',
        'slug',
    ];

    /**
     * Get the meanings for the expression.
     */
    public function meanings(): HasMany
    {
        return $this->hasMany(ExpressionMeaning::class);
    }

    /**
     * Get the pronunciation for the expression.
     */
    public function pronunciation(): HasOne
    {
        return $this->hasOne(ExpressionPronunciation::class);
    }

    /**
     * Get the connections where this expression is the first expression.
     */
    public function connectionsAsFirst(): HasMany
    {
        return $this->hasMany(ExpressionConnection::class, 'expression_id_1');
    }

    /**
     * Get the connections where this expression is the second expression.
     */
    public function connectionsAsSecond(): HasMany
    {
        return $this->hasMany(ExpressionConnection::class, 'expression_id_2');
    }
}
