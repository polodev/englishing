<?php

namespace Modules\Backend\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Article\Models\Article;
use Modules\ArticleWord\Models\ArticleWordSet;
use Yajra\DataTables\Facades\DataTables;

class ArticleWordSetController
{
    /**
     * Display a listing of the article word sets.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {        
        return view('backend::article-word-set.index');
    }

    /**
     * JSON response for the datatable.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index_json(Request $request)
    {        
        $model = ArticleWordSet::with(['article', 'user']);

        return DataTables::eloquent($model)
            ->filter(function ($query) use ($request) {
                // Filter by created_at date range
                if ($request->has('created_from') && $request->created_from) {
                    $query->whereDate('created_at', '>=', $request->created_from);
                }

                if ($request->has('created_to') && $request->created_to) {
                    $query->whereDate('created_at', '<=', $request->created_to);
                }

                // Filter by article
                if ($request->has('article_id') && $request->article_id) {
                    $query->where('article_id', $request->article_id);
                }
            }, true)
            ->addColumn('title_translation_text', function (ArticleWordSet $wordSet) {
                $translations = [];
                foreach ($wordSet->getTranslations('title_translation') as $locale => $value) {
                    $translations[] = '<span class="language-label">' . strtoupper($locale) . ':</span> ' . e($value);
                }
                return !empty($translations) ? implode('<br>', $translations) : 'No translations available';
            })
            ->addColumn('content_translation_text', function (ArticleWordSet $wordSet) {
                $translations = [];
                foreach ($wordSet->getTranslations('content_translation') as $locale => $value) {
                    $translations[] = '<span class="language-label">' . strtoupper($locale) . ':</span> ' . e(substr($value, 0, 100)) . (strlen($value) > 100 ? '...' : '');
                }
                return !empty($translations) ? implode('<br>', $translations) : 'No translations available';
            })
            ->addColumn('created_at_formatted', function (ArticleWordSet $wordSet) {
                return $wordSet->created_at->format('Y-m-d H:i:s');
            })
            ->addColumn('updated_at_formatted', function (ArticleWordSet $wordSet) {
                return $wordSet->updated_at->format('Y-m-d H:i:s');
            })
            ->addColumn('id', function (ArticleWordSet $wordSet) {
                return sprintf(
                    '<a href="%s">%s</a>',
                    route('backend::article-word-sets.show', $wordSet->id),
                    $wordSet->id
                );
            })
            ->addColumn('title', function (ArticleWordSet $wordSet) {
                return sprintf(
                    '<a href="%s">%s</a>',
                    route('backend::article-word-sets.show', $wordSet->id),
                    $wordSet->title
                );
            })
            ->addColumn('article_title', function (ArticleWordSet $wordSet) {
                return $wordSet->article ? sprintf(
                    '<a href="%s">%s</a>',
                    route('backend::articles.show', $wordSet->article->id),
                    $wordSet->article->title
                ) : 'N/A';
            })
            ->addColumn('user_name', function (ArticleWordSet $wordSet) {
                return $wordSet->user ? $wordSet->user->name : 'N/A';
            })
            ->addColumn('actions', function (ArticleWordSet $wordSet) {
                return '
                <div class="flex space-x-2">
                    <a href="' . route('backend::article-word-sets.show', $wordSet->id) . '" class="text-blue-600 hover:text-blue-900">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                        </svg>
                    </a>
                    <a href="' . route('backend::article-word-sets.edit', $wordSet->id) . '" class="text-yellow-600 hover:text-yellow-900">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                        </svg>
                    </a>
                </div>';
            })
            ->rawColumns(['id', 'title', 'article_title', 'title_translation_text', 'content_translation_text', 'actions'])
            ->make(true);
    }

    /**
     * Show the form for creating a new article word set.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {        
        $articles = Article::orderBy('title')->get();
        return view('backend::article-word-set.create', compact('articles'));
    }

    /**
     * Store a newly created article word set in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {        
        $validated = $request->validate([
            'article_id' => 'nullable|exists:articles,id',
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'display_order' => 'nullable|integer',
            'column_order' => 'nullable|string',
            'title_translation' => 'nullable|array',
            'content_translation' => 'nullable|array',
        ]);

        // Create the article word set
        $wordSet = new ArticleWordSet();
        $wordSet->article_id = $validated['article_id'] ?? null;
        $wordSet->title = $validated['title'];
        $wordSet->content = $validated['content'] ?? null;
        $wordSet->display_order = $validated['display_order'] ?? 0;
        $wordSet->column_order = $validated['column_order'] ?? null;
        $wordSet->user_id = request()->user()->id;

        // Save the word set first to get an ID
        $wordSet->save();

        // Handle translations
        if (isset($validated['title_translation']) && is_array($validated['title_translation'])) {
            foreach ($validated['title_translation'] as $locale => $value) {
                if (!empty($value)) {
                    $wordSet->setTranslation('title_translation', $locale, $value);
                }
            }
        }

        if (isset($validated['content_translation']) && is_array($validated['content_translation'])) {
            foreach ($validated['content_translation'] as $locale => $value) {
                if (!empty($value)) {
                    $wordSet->setTranslation('content_translation', $locale, $value);
                }
            }
        }

        // Save the word set again with translations
        $wordSet->save();

        return redirect()->route('backend::article-word-sets.show', $wordSet)
            ->with('success', 'Article word set created successfully');
    }

    /**
     * Display the specified article word set.
     *
     * @param  \Modules\ArticleWord\Models\ArticleWordSet  $articleWordSet
     * @return \Illuminate\View\View
     */
    public function show(ArticleWordSet $articleWordSet)
    {        
        $articleWordSet->load(['article', 'user']);
        
        return view('backend::article-word-set.show', compact('articleWordSet'));
    }

    /**
     * Show the form for editing the specified article word set.
     *
     * @param  \Modules\ArticleWord\Models\ArticleWordSet  $articleWordSet
     * @return \Illuminate\View\View
     */
    public function edit(ArticleWordSet $articleWordSet)
    {        
        $articles = Article::orderBy('title')->get();
        return view('backend::article-word-set.edit', compact('articleWordSet', 'articles'));
    }

    /**
     * Update the specified article word set in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Modules\ArticleWord\Models\ArticleWordSet  $articleWordSet
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, ArticleWordSet $articleWordSet)
    {        
        $validated = $request->validate([
            'article_id' => 'nullable|exists:articles,id',
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'display_order' => 'nullable|integer',
            'column_order' => 'nullable|string',
            'title_translation' => 'nullable|array',
            'content_translation' => 'nullable|array',
        ]);

        // Update basic fields
        $articleWordSet->article_id = $validated['article_id'] ?? null;
        $articleWordSet->title = $validated['title'];
        $articleWordSet->content = $validated['content'] ?? null;
        $articleWordSet->display_order = $validated['display_order'] ?? 0;
        $articleWordSet->column_order = $validated['column_order'] ?? null;

        // Update translations
        if (isset($validated['title_translation']) && is_array($validated['title_translation'])) {
            foreach ($validated['title_translation'] as $locale => $value) {
                if (!empty($value)) {
                    $articleWordSet->setTranslation('title_translation', $locale, $value);
                }
            }
        }

        if (isset($validated['content_translation']) && is_array($validated['content_translation'])) {
            foreach ($validated['content_translation'] as $locale => $value) {
                if (!empty($value)) {
                    $articleWordSet->setTranslation('content_translation', $locale, $value);
                }
            }
        }

        $articleWordSet->save();

        return redirect()->route('backend::article-word-sets.edit', $articleWordSet)
            ->with('success', 'Article word set updated successfully');
    }

    /**
     * Remove the specified article word set from storage.
     *
     * @param  \Modules\ArticleWord\Models\ArticleWordSet  $articleWordSet
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(ArticleWordSet $articleWordSet)
    {        
        // Delete the article word set
        $articleWordSet->delete();

        return response()->json(['success' => true]);
    }
    
}
