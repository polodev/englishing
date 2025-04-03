<?php

namespace Modules\Backend\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\Word\Models\Word;
use Modules\Word\Models\WordMeaning;
use Modules\Word\Models\WordPronunciation;
use Modules\Word\Models\WordMeaningTranslation;
use Modules\Word\Models\WordMeaningTransliteration;
use Yajra\DataTables\Facades\DataTables;

class WordController
{
    /**
     * Display a listing of the words.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('backend::word.index');
    }

    /**
     * JSON response for the datatable.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index_json(Request $request)
    {
        $model = Word::with([
            'meanings',
            'meanings.translation',
            'meanings.translation.transliteration',
            'pronunciation',
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
            ->addColumn('meanings_list', function (Word $word) {
                return $word->meanings->pluck('meaning')->implode(', ');
            })
            ->addColumn('synonyms', function (Word $word) {
                return $word->getSynonyms()->pluck('word')->implode(', ');
            })
            ->addColumn('antonyms', function (Word $word) {
                return $word->getAntonyms()->pluck('word')->implode(', ');
            })
            ->addColumn('pronunciation_text', function (Word $word) {
                if ($word->pronunciation) {
                    return sprintf(
                        'BN: %s, HI: %s, ES: %s',
                        $word->pronunciation->bn_pronunciation ?? 'N/A',
                        $word->pronunciation->hi_pronunciation ?? 'N/A',
                        $word->pronunciation->es_pronunciation ?? 'N/A'
                    );
                }
                return 'No pronunciation available';
            })
            ->addColumn('translations', function (Word $word) {
                $html = '<div class="meanings-container">';

                foreach ($word->meanings as $index => $meaning) {
                    $html .= '<div class="meaning-block mb-3">';
                    $html .= '<div class="meaning-text font-weight-bold">Meaning ' . ($index + 1) . ': ' . e($meaning->meaning) . '</div>';

                    if ($meaning->translation) {
                        $html .= '<div class="translations pl-3">';
                        $html .= '<div class="translation-item"><span class="language-label">BN:</span> ' . e($meaning->translation->bn_meaning ?? 'N/A') . '</div>';
                        $html .= '<div class="translation-item"><span class="language-label">HI:</span> ' . e($meaning->translation->hi_meaning ?? 'N/A') . '</div>';
                        $html .= '<div class="translation-item"><span class="language-label">ES:</span> ' . e($meaning->translation->es_meaning ?? 'N/A') . '</div>';

                        // if ($meaning->translation->transliteration) {
                        //     $html .= '<div class="transliterations pl-3 mt-1">';
                        //     $html .= '<div class="transliteration-title font-italic">Transliterations:</div>';
                        //     $html .= '<div class="transliteration-item"><span class="language-label">BN:</span> ' . e($meaning->translation->transliteration->bn_transliteration ?? 'N/A') . '</div>';
                        //     $html .= '<div class="transliteration-item"><span class="language-label">HI:</span> ' . e($meaning->translation->transliteration->hi_transliteration ?? 'N/A') . '</div>';
                        //     $html .= '<div class="transliteration-item"><span class="language-label">ES:</span> ' . e($meaning->translation->transliteration->es_transliteration ?? 'N/A') . '</div>';
                        //     $html .= '</div>'; // close transliterations
                        // }

                        $html .= '</div>'; // close translations
                    } else {
                        $html .= '<div class="translations pl-3">No translations available</div>';
                    }

                    $html .= '</div>'; // close meaning-block

                    // Add separator except for the last item
                    if ($index < count($word->meanings) - 1) {
                        $html .= '<hr class="meaning-separator">';
                    }
                }

                $html .= '</div>'; // close meanings-container

                return $html;
            })
            ->addColumn('created_at_formatted', function (Word $word) {
                return $word->created_at->format('Y-m-d H:i:s');
            })
            ->addColumn('updated_at_formatted', function (Word $word) {
                return $word->updated_at->format('Y-m-d H:i:s');
            })
            ->addColumn('id', function (Word $word) {
                return sprintf(
                    '<a href="%s">%s</a>',
                    route('backend::words.show', $word->id),
                    $word->id
                );
            })
            ->addColumn('word', function (Word $word) {
                return sprintf(
                    '<a href="%s">%s</a>',
                    route('backend::words.show', $word->id),
                    $word->word
                );
            })
            ->rawColumns(['id', 'word', 'translations'])
            ->toJson();
    }




    public function show(Word $word)
    {
        $word->load([
            'meanings',
            'meanings.translation',
            'meanings.translation.transliteration',
            'pronunciation',
        ]);

        $synonyms = $word->getSynonyms();
        $antonyms = $word->getAntonyms();

        return view('backend::word.show', compact('word', 'synonyms', 'antonyms'));
    }


    /**
     * Remove the specified word from storage.
     *
     * @param  \Modules\Word\Models\Word  $word
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Word $word)
    {
        // Delete all connections
        $word->connections()->detach();
        $word->connectionsInverse()->detach();

        // Delete meanings and related models
        foreach ($word->meanings as $meaning) {
            if ($meaning->translation) {
                if ($meaning->translation->transliteration) {
                    $meaning->translation->transliteration->delete();
                }
                $meaning->translation->delete();
            }
            $meaning->delete();
        }

        // Delete pronunciation
        if ($word->pronunciation) {
            $word->pronunciation->delete();
        }

        // Delete the word
        $word->delete();

        return response()->json(['success' => true]);
    }
}
