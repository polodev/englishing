<?php

namespace Modules\ArticleWord\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ArticleWordExampleExpression extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'article_word_set_list_id',
        'expression',
        'slug',
    ];

    /**
     * Get the list that owns the example expression.
     */
    public function list(): BelongsTo
    {
        return $this->belongsTo(ArticleWordSetList::class, 'article_word_set_list_id');
    }

    /**
     * Get the meaning for the example expression.
     */
    public function meaning(): HasOne
    {
        return $this->hasOne(ArticleWordExampleExpressionMeaning::class);
    }
}
