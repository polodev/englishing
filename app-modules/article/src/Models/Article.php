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
        'series_id',
        'section_id',
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
     * Get the series that owns the article.
     */
    public function series(): BelongsTo
    {
        return $this->belongsTo(Series::class)->withDefault();
    }

    /**
     * Get the section that owns the article.
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class)->withDefault();
    }

    /**
     * Get the translation for the article.
     */
    public function translation(): HasOne
    {
        return $this->hasOne(ArticleTranslation::class);
    }

    /**
     * Get associated articles in the same series and section.
     * Returns an array of articles with id, title, slug, display_order, section, series, and is_current flag.
     * Only returns results if the article is under a series.
     *
     * @return array
     */
    public function getAssociatedArticles(): array
    {
        // If this article isn't associated with a series, return empty array
        if (!$this->series_id) {
            return [];
        }
        
        $query = self::where('id', '!=', $this->id)
            ->where('series_id', $this->series_id);
        
        // Only filter by section_id if it exists
        if ($this->section_id) {
            $query->where('section_id', $this->section_id);
        }
        
        $articles = $query->orderBy('display_order')
            ->with(['section:id,title', 'series:id,title'])
            ->select('id', 'title', 'slug', 'display_order', 'section_id', 'series_id')
            ->get();
        
        // Format the results to match the expected output
        $formattedArticles = $articles->map(function($article) {
            $sectionTitle = '';
            $seriesTitle = '';
            
            // Get section title if section_id exists
            if ($article->section_id && $article->section) {
                $sectionTitle = $article->section->title;
            }
            
            // Get series title if series_id exists
            if ($article->series_id && $article->series) {
                $seriesTitle = $article->series->title;
            }
            
            return [
                'id' => $article->id,
                'title' => $article->title,
                'slug' => $article->slug,
                'display_order' => $article->display_order,
                'section' => $sectionTitle,
                'series' => $seriesTitle,
                'is_current' => false
            ];
        })->toArray();

        return $formattedArticles;
    }
}
