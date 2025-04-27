<?php

namespace Modules\ArticleDoubleWord\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class ArticleDoubleWordSet extends Model
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
     * Get the double word set lists for this set.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function setLists(): HasMany
    {
        return $this->hasMany(ArticleDoubleWordSetList::class, 'article_double_word_set_id');
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
            'word_1_meaning',
            'word_2_meaning',
            'word_1_example_sentence',
            'word_2_example_sentence',
            'word_1_translation',
            'word_2_translation',
        ];
    }
}
