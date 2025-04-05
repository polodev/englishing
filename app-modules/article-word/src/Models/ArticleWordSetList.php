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
     * Get the translations for the list.
     */
    public function translations(): HasMany
    {
        return $this->hasMany(ArticleWordTranslation::class, 'article_word_set_list_id');
    }

    /**
     * Get the translation for a specific locale.
     *
     * @param string $locale
     * @return \Modules\ArticleWord\Models\ArticleWordTranslation|null
     */
    public function getTranslation(string $locale)
    {
        return $this->translations()->where('locale', $locale)->first();
    }


}
