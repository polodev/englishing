<?php

namespace Modules\Backend\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\Sentence\Models\Sentence;
use Modules\Sentence\Models\SentenceTranslation;
use Yajra\DataTables\Facades\DataTables;

class SentenceController
{
    /**
     * Display a listing of the sentences.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('backend::sentence.index');
    }

    /**
     * JSON response for the datatable.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index_json(Request $request)
    {
        $model = Sentence::with([
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
            ->addColumn('translations_html', function (Sentence $sentence) {
                $html = '<div class="translations-container">';
                
                if ($sentence->translations->isEmpty()) {
                    $html .= '<div class="no-translations text-gray-500 dark:text-gray-400 italic">No translations available</div>';
                } else {
                    foreach ($sentence->translations as $translation) {
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
            ->addColumn('pronunciation_text', function (Sentence $sentence) {
                $pronunciations = $sentence->getTranslations('pronunciation');
                if (empty($pronunciations)) {
                    return '<span class="no-data">No pronunciation available</span>';
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
            ->addColumn('created_at_formatted', function (Sentence $sentence) {
                return $sentence->created_at->format('Y-m-d H:i:s');
            })
            ->addColumn('updated_at_formatted', function (Sentence $sentence) {
                return $sentence->updated_at->format('Y-m-d H:i:s');
            })
            ->addColumn('id', function (Sentence $sentence) {
                return sprintf(
                    '<a href="%s">%s</a>',
                    route('backend::sentences.show', $sentence->id),
                    $sentence->id
                );
            })
            ->addColumn('sentence', function (Sentence $sentence) {
                return sprintf(
                    '<a href="%s">%s</a>',
                    route('backend::sentences.show', $sentence->id),
                    Str::limit($sentence->sentence, 50)
                );
            })
            ->rawColumns(['id', 'sentence', 'translations_html', 'pronunciation_text'])
            ->toJson();
    }

    /**
     * Display the specified sentence.
     *
     * @param  \Modules\Sentence\Models\Sentence  $sentence
     * @return \Illuminate\View\View
     */
    public function show(Sentence $sentence)
    {
        $sentence->load([
            'translations',
        ]);

        return view('backend::sentence.show', compact('sentence'));
    }

    /**
     * Remove the specified sentence from storage.
     *
     * @param  \Modules\Sentence\Models\Sentence  $sentence
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Sentence $sentence)
    {
        // Delete translations
        foreach ($sentence->translations as $translation) {
            $translation->delete();
        }

        // Delete the sentence
        $sentence->delete();

        return response()->json(['success' => true]);
    }
}
