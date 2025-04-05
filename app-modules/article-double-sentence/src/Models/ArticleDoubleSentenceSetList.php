<?php

namespace Modules\ArticleDoubleSentence\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class ArticleDoubleSentenceSetList extends Model
{
    use HasFactory, HasTranslations;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * The attributes that are translatable.
     *
     * @var array<int, string>
     */
    public $translatable = [
        'pronunciation_1',
        'pronunciation_2',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'pronunciation_1' => 'array',
        'pronunciation_2' => 'array',
    ];

    /**
     * Get the double sentence set that owns the list.
     */
    public function doubleSentenceSet(): BelongsTo
    {
        return $this->belongsTo(ArticleDoubleSentenceSet::class, 'article_double_sentence_set_id');
    }

    /**
     * Get the translations for the list.
     */
    public function translations(): HasMany
    {
        return $this->hasMany(ArticleDoubleSentenceTranslation::class, 'article_double_sentence_set_list_id');
    }

    /**
     * Get the translation for a specific locale.
     *
     * @param string $locale
     * @return \Modules\ArticleDoubleSentence\Models\ArticleDoubleSentenceTranslation|null
     */
    public function getTranslation(string $locale)
    {
        return $this->translations()->where('locale', $locale)->first();
    }
}
