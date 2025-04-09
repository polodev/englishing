<?php

namespace Modules\Backend\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\Expression\Models\Expression;
use Modules\Expression\Models\ExpressionMeaning;
use Modules\Expression\Models\ExpressionTranslation;
use Yajra\DataTables\Facades\DataTables;

class ExpressionController
{
    /**
     * Display a listing of the expressions.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('backend::expression.index');
    }

    /**
     * JSON response for the datatable.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index_json(Request $request)
    {
        $model = Expression::with([
            'meanings',
            'meanings.translations',
            'translations',
        ]);

        return DataTables::eloquent($model)
            ->filter(function ($query) use ($request) {
                // Filter by created_at date range
                if ($request->has('created_from') && $request->created_from) {
                    $query->whereDate('created_at', '>=', $request->created_from);
                }

                if ($request->has('created_to') && $request->created_to) {
                    $query->whereDate('created_at', '<=', $request->created_to);
                }
            }, true)
            ->addColumn('meanings_html', function (Expression $expression) {
                $html = '<div class="meanings-container">';
                
                if ($expression->meanings->isEmpty()) {
                    $html .= '<div class="no-meanings text-gray-500 dark:text-gray-400 italic">No meanings available</div>';
                } else {
                    foreach ($expression->meanings as $meaning) {
                        $html .= '<div class="meaning-item mb-2">';
                        $html .= '<div class="meaning-text text-gray-800 dark:text-gray-200">' . e($meaning->meaning) . '</div>';
                        
                        // Add translations if available
                        if ($meaning->translations->isNotEmpty()) {
                            $html .= '<div class="ml-4">';
                            foreach ($meaning->translations as $translation) {
                                $html .= '<div class="translation-item">';
                                $html .= '<span class="language-label">' . strtoupper($translation->locale) . ':</span> ';
                                $html .= '<span class="text-gray-800 dark:text-gray-200">' . e($translation->translation) . '</span>';
                                
                                if (!empty($translation->transliteration)) {
                                    $html .= '<span class="transliteration-block">(' . e($translation->transliteration) . ')</span>';
                                }
                                
                                $html .= '</div>';
                            }
                            $html .= '</div>';
                        }
                        
                        $html .= '</div>';
                    }
                }
                
                $html .= '</div>'; // close meanings-container
                
                return $html;
            })
            ->addColumn('translations_html', function (Expression $expression) {
                $html = '<div class="translations-container">';
                
                if ($expression->translations->isEmpty()) {
                    $html .= '<div class="no-translations text-gray-500 dark:text-gray-400 italic">No translations available</div>';
                } else {
                    foreach ($expression->translations as $translation) {
                        $html .= '<div class="translation-item">';
                        $html .= '<span class="language-label">' . strtoupper($translation->locale) . ':</span> ';
                        $html .= '<span class="text-gray-800 dark:text-gray-200">' . e($translation->translation) . '</span>';
                        
                        if (!empty($translation->transliteration)) {
                            $html .= '<span class="transliteration-block">(' . e($translation->transliteration) . ')</span>';
                        }
                        
                        $html .= '</div>';
                    }
                }
                
                $html .= '</div>'; // close translations-container
                
                return $html;
            })
            ->addColumn('pronunciation_text', function (Expression $expression) {
                $pronunciations = [];
                
                try {
                    $pronunciations = $expression->getTranslations('pronunciation');
                } catch (\Exception $e) {
                    // Handle the case when pronunciation is not set or there's an error
                }
                
                if (empty($pronunciations)) {
                    return '<span class="no-data text-gray-500 dark:text-gray-400 italic">No pronunciation available</span>';
                }
                
                $html = '<div class="pronunciations-container">';
                foreach ($pronunciations as $locale => $pronunciation) {
                    $html .= '<div class="pronunciation-item">';
                    $html .= '<span class="language-label">' . strtoupper($locale) . ':</span> ';
                    $html .= '<span class="text-gray-800 dark:text-gray-200">' . e($pronunciation) . '</span>';
                    $html .= '</div>';
                }
                $html .= '</div>';
                
                return $html;
            })
            ->addColumn('created_at_formatted', function (Expression $expression) {
                return $expression->created_at->format('Y-m-d H:i:s');
            })
            ->addColumn('updated_at_formatted', function (Expression $expression) {
                return $expression->updated_at->format('Y-m-d H:i:s');
            })
            ->addColumn('id', function (Expression $expression) {
                return sprintf(
                    '<a href="%s">%s</a>',
                    route('backend::expressions.show', $expression->id),
                    $expression->id
                );
            })
            ->addColumn('expression', function (Expression $expression) {
                return sprintf(
                    '<a href="%s">%s</a>',
                    route('backend::expressions.show', $expression->id),
                    Str::limit($expression->expression, 50)
                );
            })
            ->addColumn('type', function (Expression $expression) {
                return $expression->type ?? '<span class="text-gray-500 dark:text-gray-400 italic">N/A</span>';
            })
            ->rawColumns(['id', 'expression', 'meanings_html', 'translations_html', 'pronunciation_text', 'type'])
            ->toJson();
    }

    /**
     * Display the specified expression.
     *
     * @param  \Modules\Expression\Models\Expression  $expression
     * @return \Illuminate\View\View
     */
    public function show(Expression $expression)
    {
        $expression->load([
            'meanings',
            'meanings.translations',
            'translations',
            'connections',
            'connectionsInverse',
        ]);

        return view('backend::expression.show', compact('expression'));
    }

    /**
     * Remove the specified expression from storage.
     *
     * @param  \Modules\Expression\Models\Expression  $expression
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Expression $expression)
    {
        // Delete all connections
        $expression->connections()->detach();
        $expression->connectionsInverse()->detach();

        // Delete translations
        foreach ($expression->translations as $translation) {
            $translation->delete();
        }

        // Delete meanings and their translations
        foreach ($expression->meanings as $meaning) {
            foreach ($meaning->translations as $translation) {
                $translation->delete();
            }
            $meaning->delete();
        }

        // Delete the expression
        $expression->delete();

        return response()->json(['success' => true]);
    }
}
