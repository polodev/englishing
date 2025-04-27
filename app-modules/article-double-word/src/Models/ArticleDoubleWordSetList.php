<?php

namespace Modules\ArticleDoubleWord\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ArticleDoubleWordSetList extends Model
{
    protected $guarded = [];

    /**
     * Get the double word set that this list item belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function set(): BelongsTo
    {
        return $this->belongsTo(ArticleDoubleWordSet::class, 'article_double_word_set_id');
    }

    /**
     * Get the translations for this double word set list item.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function translations(): HasMany
    {
        return $this->hasMany(ArticleDoubleWordTranslation::class, 'article_double_word_set_list_id');
    }
}
