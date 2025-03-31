<?php

namespace Modules\Article\Libraries\Helpers;

trait HelperFunctionsArticleTrait
{
    /**
     * @param int $seriesId
     * @return array
     */
    public static function getAssociatedArticlesForSeries(int $seriesId): array
    {
        return self::where('series_id', $seriesId)
            ->orderBy('display_order')
            ->with(['section:id,title', 'series:id,title'])
            ->select('id', 'title', 'slug', 'display_order', 'section_id', 'series_id')
            ->get()
            ->map(function ($article) {
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
            })
            ->toArray();
    }

}
