<?php

namespace Modules\ArticleDoubleWord\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArticleDoubleWordTranslation extends Model
{
    protected $guarded = [];

    /**
     * Get the double word set list item that this translation belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function setList(): BelongsTo
    {
        return $this->belongsTo(ArticleDoubleWordSetList::class, 'article_double_word_set_list_id');
    }
}
