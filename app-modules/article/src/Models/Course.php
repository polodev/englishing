<?php

namespace Modules\Article\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Course extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'content',
    ];

    /**
     * Get the user that owns the course.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the translation for the course.
     */
    public function translation(): HasOne
    {
        return $this->hasOne(CourseTranslation::class);
    }

    /**
     * Get the articles for the course.
     */
    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }
    
    /**
     * Get associated articles for this course.
     * Returns an array of articles with id, title, slug, display_order, translation titles, and is_current flag.
     *
     * @return array
     */
    public function getAssociatedArticles(): array
    {
        $articles = $this->articles()
            ->orderBy('display_order')
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
