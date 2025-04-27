<?php

namespace Modules\ArticleTripleWord\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ArticleTripleWordSetList extends Model
{
    protected $guarded = [];

    /**
     * Get the triple word set that this list item belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function set(): BelongsTo
    {
        return $this->belongsTo(ArticleTripleWordSet::class, 'article_triple_word_set_id');
    }
    
    /**
     * Get the translations for this triple word set list item.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function translations(): HasMany
    {
        return $this->hasMany(ArticleTripleWordTranslation::class, 'article_triple_word_set_list_id');
    }
}
