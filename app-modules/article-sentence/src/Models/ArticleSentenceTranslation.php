<?php

namespace Modules\ArticleSentence\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArticleSentenceTranslation extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * Get the sentence set list that owns the translation.
     */
    public function sentenceSetList(): BelongsTo
    {
        return $this->belongsTo(ArticleSentenceSetList::class, 'article_sentence_set_list_id');
    }
}
