<?php

namespace Modules\ArticleTripleWord\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArticleTripleWordTranslation extends Model
{
    protected $guarded = [];

    /**
     * Get the triple word set list item that this translation belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function setList(): BelongsTo
    {
        return $this->belongsTo(ArticleTripleWordSetList::class, 'article_triple_word_set_list_id');
    }
}
