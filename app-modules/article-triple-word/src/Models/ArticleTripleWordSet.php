<?php

namespace Modules\ArticleTripleWord\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class ArticleTripleWordSet extends Model
{
    use HasTranslations;

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
        'column_order' => 'json',
    ];

    /**
     * Get the triple word set lists for this set.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function setLists(): HasMany
    {
        return $this->hasMany(ArticleTripleWordSetList::class, 'article_triple_word_set_id');
    }

    /**
     * Get the column names available for ordering
     *
     * @return array
     */
    public static function getColumnsForColumnOrder(): array
    {
        return [
            'word_1',
            'word_2',
            'word_3',
            'word_1_meaning',
            'word_2_meaning',
            'word_3_meaning',
            'word_1_example_sentence',
            'word_2_example_sentence',
            'word_3_example_sentence',
            'word_1_translation',
            'word_2_translation',
            'word_3_translation',
        ];
    }
}
