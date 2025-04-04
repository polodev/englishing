<?php

namespace Modules\ArticleWord\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ArticleWordExampleSentenceTranslation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'article_word_example_sentence_id',
        'bn_sentence',
        'hi_sentence',
        'es_sentence',
    ];

    /**
     * Get the example sentence that owns the translation.
     */
    public function exampleSentence(): BelongsTo
    {
        return $this->belongsTo(ArticleWordExampleSentence::class, 'article_word_example_sentence_id');
    }

    /**
     * Get the transliteration for the translation.
     */
    public function transliteration(): HasOne
    {
        return $this->hasOne(ArticleWordExampleSentenceTransliteration::class);
    }
}
