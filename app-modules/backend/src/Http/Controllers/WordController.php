<?php

namespace Modules\Backend\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\Word\Models\Word;
use Modules\Word\Models\WordMeaning;
use Modules\Word\Models\WordTranslation;
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
            'meanings.translations',
            'translations',
            'connections',
            'connectionsInverse',
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
                // Get synonyms directly from the eager loaded relationships
                $synonyms1 = $word->connections->where('pivot.type', 'synonyms');
                $synonyms2 = $word->connectionsInverse->where('pivot.type', 'synonyms');
                
                // Merge the collections and extract word values
                return $synonyms1->merge($synonyms2)->pluck('word')->implode(', ');
            })
            ->addColumn('antonyms', function (Word $word) {
                // Get antonyms directly from the eager loaded relationships
                $antonyms1 = $word->connections->where('pivot.type', 'antonyms');
                $antonyms2 = $word->connectionsInverse->where('pivot.type', 'antonyms');
                
                // Merge the collections and extract word values
                return $antonyms1->merge($antonyms2)->pluck('word')->implode(', ');
            })
            ->addColumn('pronunciation_text', function (Word $word) {
                if ($word->pronunciation) {
                    $pronunciations = [];
                    foreach ($word->getTranslations('pronunciation') as $locale => $value) {
                        $pronunciations[] = strtoupper($locale) . ': ' . $value;
                    }
                    return implode(', ', $pronunciations);
                }
                return 'No pronunciation available';
            })
            ->addColumn('translations', function (Word $word) {
                $html = '<div class="meanings-container">';

                foreach ($word->meanings as $index => $meaning) {
                    $html .= '<div class="meaning-block mb-3">';
                    $html .= '<div class="meaning-text font-weight-bold">Meaning ' . ($index + 1) . ': ' . e($meaning->meaning) . '</div>';

                    if ($meaning->translations->count() > 0) {
                        $html .= '<div class="translations pl-3">';
                        
                        foreach ($meaning->translations as $translation) {
                            $html .= '<div class="translation-item">';
                            $html .= '<span class="language-label">' . strtoupper($translation->locale) . ':</span> ';
                            $html .= e($translation->translation);
                            
                            if ($translation->transliteration) {
                                $html .= '<span class="transliteration-block">(' . e($translation->transliteration) . ')</span>';
                            }
                            
                            $html .= '</div>';
                        }
                        
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
            'meanings.translations',
            'translations',
            'connections',
            'connectionsInverse',
        ]);

        // Get synonyms directly from the eager loaded relationships
        $synonyms1 = $word->connections->where('pivot.type', 'synonyms');
        $synonyms2 = $word->connectionsInverse->where('pivot.type', 'synonyms');
        $synonyms = $synonyms1->merge($synonyms2);
        
        // Get antonyms directly from the eager loaded relationships
        $antonyms1 = $word->connections->where('pivot.type', 'antonyms');
        $antonyms2 = $word->connectionsInverse->where('pivot.type', 'antonyms');
        $antonyms = $antonyms1->merge($antonyms2);

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

        // Delete translations
        foreach ($word->translations as $translation) {
            $translation->delete();
        }
        
        // Delete meanings and their translations
        foreach ($word->meanings as $meaning) {
            foreach ($meaning->translations as $translation) {
                $translation->delete();
            }
            $meaning->delete();
        }

        // Delete the word
        $word->delete();

        return response()->json(['success' => true]);
    }
}
