<?php

namespace Modules\ArticleWord\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ArticleWordExampleSentence extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'article_word_set_list_id',
        'display_order',
        'sentence',
        'slug',
    ];

    /**
     * Get the list that owns the example sentence.
     */
    public function list(): BelongsTo
    {
        return $this->belongsTo(ArticleWordSetList::class, 'article_word_set_list_id');
    }

    /**
     * Get the translation for the example sentence.
     */
    public function translation(): HasOne
    {
        return $this->hasOne(ArticleWordExampleSentenceTranslation::class);
    }
}
