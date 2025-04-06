<?php

namespace Modules\Expression\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class ExpressionMeaning extends Model
{
    use HasFactory, HasTranslations;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * The attributes that are translatable.
     *
     * @var array<int, string>
     */
    public $translatable = [
        'pronunciation', # pronunciation should be for non english locale.
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'pronunciation' => 'array',
    ];

    /**
     * Get the expression that owns the meaning.
     */
    public function expression(): BelongsTo
    {
        return $this->belongsTo(Expression::class);
    }

    /**
     * Get the translations for this meaning.
     */
    public function translations(): HasMany
    {
        return $this->hasMany(ExpressionTranslation::class, 'expression_meaning_id');
    }
}
