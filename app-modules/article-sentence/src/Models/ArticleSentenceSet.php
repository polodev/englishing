<?php

namespace Modules\ArticleSentence\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Article\Models\Article;
use Spatie\Translatable\HasTranslations;

class ArticleSentenceSet extends Model
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
        'title_translation',
        'content_translation',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'title_translation' => 'array',
        'content_translation' => 'array',
        'column_order' => 'array',
    ];
    public static function getColumnsForColumnOrder()
    {
        return [
            'word',
            'phonetic',
            'pronunciation',
            'parts_of_speech',
            'static_content_1',
            'static_content_2',
            'meaning',
            'example_sentence',
            'example_expression',
            'example_expression_meaning',
            'word_translation',
            'example_sentence_translation',
            'example_expression_translation',
        ];
    }

    /**
     * Get the article that owns the sentence set.
     */
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    /**
     * Get the lists for the sentence set.
     */
    public function lists(): HasMany
    {
        return $this->hasMany(ArticleSentenceSetList::class);
    }
}
