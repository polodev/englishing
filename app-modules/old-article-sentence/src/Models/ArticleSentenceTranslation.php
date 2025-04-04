<?php

namespace Modules\ArticleSentence\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ArticleSentenceTranslation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'article_sentence_set_list_id',
        'bn_meaning',
        'hi_meaning',
        'es_meaning',
    ];

    /**
     * Get the sentence list that owns the translation.
     */
    public function sentenceList(): BelongsTo
    {
        return $this->belongsTo(ArticleSentenceSetList::class, 'article_sentence_set_list_id');
    }

    /**
     * Get the transliteration for the translation.
     */
    public function transliteration(): HasOne
    {
        return $this->hasOne(ArticleSentenceTransliteration::class);
    }
}
