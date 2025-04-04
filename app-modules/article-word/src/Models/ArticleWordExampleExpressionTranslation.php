<?php

namespace Modules\ArticleWord\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArticleWordExampleExpressionTranslation extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * Get the word set list that owns the translation.
     */
    public function wordSetList(): BelongsTo
    {
        return $this->belongsTo(ArticleWordSetList::class, 'article_word_set_list_id');
    }
}
