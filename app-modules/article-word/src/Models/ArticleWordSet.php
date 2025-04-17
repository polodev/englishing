<?php

namespace Modules\ArticleWord\Models;

use App\Models\User;
use Modules\Article\Models\Article;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ArticleWordSet extends Model
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

    /**
     * Get the article that owns the word set.
     */
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }
    /**
     * Get the article that owns the word set.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
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
     * Get the lists for the word set.
     */
    public function lists(): HasMany
    {
        return $this->hasMany(ArticleWordSetList::class);
    }
}
