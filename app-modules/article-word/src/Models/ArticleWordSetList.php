<?php

namespace Modules\ArticleWord\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ArticleWordSetList extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * Get the word set that owns the list.
     */
    public function wordSet(): BelongsTo
    {
        return $this->belongsTo(ArticleWordSet::class, 'article_word_set_id');
    }

    /**
     * Get the word translations for the list.
     */
    public function wordTranslations(): HasMany
    {
        return $this->hasMany(ArticleWordTranslation::class);
    }

    /**
     * Get the example sentence translations for the list.
     */
    public function exampleSentenceTranslations(): HasMany
    {
        return $this->hasMany(ArticleWordExampleSentenceTranslation::class);
    }

    /**
     * Get the example expression translations for the list.
     */
    public function exampleExpressionTranslations(): HasMany
    {
        return $this->hasMany(ArticleWordExampleExpressionTranslation::class);
    }

    /**
     * Get the word translation for a specific locale.
     *
     * @param string $locale
     * @return \Modules\ArticleWord\Models\ArticleWordTranslation|null
     */
    public function getWordTranslation(string $locale)
    {
        return $this->wordTranslations()->where('locale', $locale)->first();
    }

    /**
     * Get the example sentence translation for a specific locale.
     *
     * @param string $locale
     * @return \Modules\ArticleWord\Models\ArticleWordExampleSentenceTranslation|null
     */
    public function getExampleSentenceTranslation(string $locale)
    {
        return $this->exampleSentenceTranslations()->where('locale', $locale)->first();
    }

    /**
     * Get the example expression translation for a specific locale.
     *
     * @param string $locale
     * @return \Modules\ArticleWord\Models\ArticleWordExampleExpressionTranslation|null
     */
    public function getExampleExpressionTranslation(string $locale)
    {
        return $this->exampleExpressionTranslations()->where('locale', $locale)->first();
    }
}
