<?php

namespace Modules\Article\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\User;

class Article extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'course_id',
        'type',
        'title',
        'slug',
        'content',
        'display_order',
        'excerpt',
        'is_premium',
        'scratchpad',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_premium' => 'boolean',
    ];

    /**
     * Get the user that owns the article.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the course that owns the article.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class)->withDefault();
    }

    /**
     * Get the translation for the article.
     */
    public function translation(): HasOne
    {
        return $this->hasOne(ArticleTranslation::class);
    }

    /**
     * Get associated articles in the same course.
     * Returns an array of articles with id, title, slug, display_order, translation titles, and is_current flag.
     * Only returns results if the article is under a course.
     *
     * @return array
     */
    public function getAssociatedArticles(): array
    {
        // If this article isn't associated with a course, return empty array
        if (!$this->course_id) {
            return [];
        }

        $query = self::where('id', '!=', $this->id)
            ->where('course_id', $this->course_id);

        $articles = $query->orderBy('display_order')
            ->with(['translation'])
            ->select('id', 'title', 'slug', 'display_order')
            ->get();

        // Format the results to match the expected output
        $formattedArticles = $articles->map(function($article) {

            return [
                'id' => $article->id,
                'title' => $article->title,
                'bn_title' => $article->translation?->bn_title,
                'hi_title' => $article->translation?->hi_title,
                'es_title' => $article->translation?->es_title,
                'slug' => $article->slug,
                'display_order' => $article->display_order,
                'is_current' => false
            ];
        })->toArray();

        return $formattedArticles;
    }
}
