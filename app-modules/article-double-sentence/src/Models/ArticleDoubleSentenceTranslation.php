<?php

namespace Modules\ArticleDoubleSentence\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArticleDoubleSentenceTranslation extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * Get the double sentence set list that owns the translation.
     */
    public function doubleSentenceSetList(): BelongsTo
    {
        return $this->belongsTo(ArticleDoubleSentenceSetList::class, 'article_double_sentence_set_list_id');
    }
}
