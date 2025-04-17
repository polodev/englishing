<?php

namespace Modules\Article\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\ArticleWord\Models\ArticleWordSet;
use Spatie\Translatable\HasTranslations;

class Article extends Model
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
        'excerpt_translation',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'title_translation' => 'array',
        'content_translation' => 'array',
        'excerpt_translation' => 'array',
        'is_premium' => 'boolean',
    ];

    /**
     * Get the user that owns the article.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'));
    }

    /**
     * Get the course that owns the article.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }



    /**
     * Get associated articles for the same course.
     * 
     * @return array
     */
    public function getAssociatedArticles()
    {        
        // If this article belongs to a course, get all articles in the same course
        if ($this->course_id) {
            return Article::where('course_id', $this->course_id)
                ->where('id', '!=', $this->id) // Exclude the current article
                ->orderBy('display_order')
                ->get()
                ->map(function ($article) {
                    return [
                        'id' => $article->id,
                        'title' => $article->title,
                        'slug' => $article->slug,
                        'title_translation' => $article->title_translation,
                    ];
                })
                ->toArray();
        }
        
        // If no related articles, return empty array
        return [];
    }

    /**
     * Get the word set for this article.
     */
    public function wordSet(): HasOne
    {
        return $this->hasOne(ArticleWordSet::class);
    }
}
