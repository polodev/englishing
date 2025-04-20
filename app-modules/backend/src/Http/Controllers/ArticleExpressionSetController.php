<?php

namespace Modules\Backend\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Article\Models\Article;
use Modules\ArticleExpression\Models\ArticleExpressionSet;
use Yajra\DataTables\Facades\DataTables;

class ArticleExpressionSetController
{
    /**
     * Display a listing of the article expression sets.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {        
        return view('backend::article-expression-set.index');
    }

    /**
     * JSON response for the datatable.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index_json(Request $request)
    {        
        $model = ArticleExpressionSet::with(['article', 'user']);

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
            ->addColumn('title_translation_text', function (ArticleExpressionSet $expressionSet) {
                $translations = [];
                foreach ($expressionSet->getTranslations('title_translation') as $locale => $value) {
                    $translations[] = '<span class="language-label">' . strtoupper($locale) . ':</span> ' . e($value);
                }
                return !empty($translations) ? implode('<br>', $translations) : 'No translations available';
            })
            ->addColumn('content_translation_text', function (ArticleExpressionSet $expressionSet) {
                $translations = [];
                foreach ($expressionSet->getTranslations('content_translation') as $locale => $value) {
                    $translations[] = '<span class="language-label">' . strtoupper($locale) . ':</span> ' . e(substr($value, 0, 100)) . (strlen($value) > 100 ? '...' : '');
                }
                return !empty($translations) ? implode('<br>', $translations) : 'No translations available';
            })
            ->addColumn('created_at_formatted', function (ArticleExpressionSet $expressionSet) {
                return $expressionSet->created_at->format('Y-m-d H:i:s');
            })
            ->addColumn('updated_at_formatted', function (ArticleExpressionSet $expressionSet) {
                return $expressionSet->updated_at->format('Y-m-d H:i:s');
            })
            ->addColumn('id', function (ArticleExpressionSet $expressionSet) {
                return sprintf(
                    '<a href="%s">%s</a>',
                    route('backend::article-expression-sets.show', $expressionSet->id),
                    $expressionSet->id
                );
            })
            ->addColumn('title', function (ArticleExpressionSet $expressionSet) {
                return sprintf(
                    '<a href="%s">%s</a>',
                    route('backend::article-expression-sets.show', $expressionSet->id),
                    $expressionSet->title
                );
            })
            ->addColumn('article_title', function (ArticleExpressionSet $expressionSet) {
                return $expressionSet->article ? sprintf(
                    '<a href="%s">%s</a>',
                    route('backend::articles.show', $expressionSet->article->id),
                    $expressionSet->article->title
                ) : 'N/A';
            })
            ->addColumn('user_name', function (ArticleExpressionSet $expressionSet) {
                return $expressionSet->user ? $expressionSet->user->name : 'N/A';
            })
            ->addColumn('actions', function (ArticleExpressionSet $expressionSet) {
                $actions = '
                <div class="flex space-x-2">
                    <a href="' . route('backend::article-expression-sets.show', $expressionSet->id) . '" class="text-blue-600 hover:text-blue-900">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </a>
                    <a href="' . route('backend::article-expression-sets.edit', $expressionSet->id) . '" class="text-green-600 hover:text-green-900">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </a>';
                
                // Only show delete button for admin users
                $user = Auth::user();
                if (Auth::check() && $user && $user->role === 'admin') {
                    $actions .= '
                    <button type="button" class="text-red-600 hover:text-red-900 delete-expression-set" data-id="' . $expressionSet->id . '" data-title="' . $expressionSet->title . '">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>';
                }
                
                $actions .= '
                </div>';
                
                return $actions;
            })
            ->rawColumns(['id', 'title', 'title_translation_text', 'content_translation_text', 'article_title', 'actions'])
            ->toJson();
    }

    /**
     * Show the form for creating a new article expression set.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {        
        $articles = Article::orderBy('title')->get();
        return view('backend::article-expression-set.create', compact('articles'));
    }

    /**
     * Store a newly created article expression set in storage.
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
            'title_translation' => 'nullable|array',
            'content_translation' => 'nullable|array',
            'column_order' => 'nullable|string',
        ]);

        // Create a new article expression set
        $expressionSet = new ArticleExpressionSet();
        $expressionSet->article_id = $validated['article_id'] ?? null;
        $expressionSet->user_id = Auth::id();
        $expressionSet->title = $validated['title'];
        $expressionSet->content = $validated['content'] ?? null;
        $expressionSet->display_order = $validated['display_order'] ?? 0;
        
        // Set column order
        if (isset($validated['column_order']) && !empty($validated['column_order'])) {
            $expressionSet->column_order = json_decode($validated['column_order'], true);
        }

        // Set translations
        if (isset($validated['title_translation']) && is_array($validated['title_translation'])) {
            foreach ($validated['title_translation'] as $locale => $value) {
                if (!empty($value)) {
                    $expressionSet->setTranslation('title_translation', $locale, $value);
                }
            }
        }

        if (isset($validated['content_translation']) && is_array($validated['content_translation'])) {
            foreach ($validated['content_translation'] as $locale => $value) {
                if (!empty($value)) {
                    $expressionSet->setTranslation('content_translation', $locale, $value);
                }
            }
        }

        $expressionSet->save();

        return redirect()->route('backend::article-expression-sets.edit', $expressionSet)
            ->with('success', 'Article expression set created successfully');
    }

    /**
     * Display the specified article expression set.
     *
     * @param  \Modules\ArticleExpression\Models\ArticleExpressionSet  $articleExpressionSet
     * @return \Illuminate\View\View
     */
    public function show(ArticleExpressionSet $articleExpressionSet)
    {        
        $articleExpressionSet->load(['article', 'user', 'lists']);
        return view('backend::article-expression-set.show', compact('articleExpressionSet'));
    }

    /**
     * Show the form for editing the specified article expression set.
     *
     * @param  \Modules\ArticleExpression\Models\ArticleExpressionSet  $articleExpressionSet
     * @return \Illuminate\View\View
     */
    public function edit(ArticleExpressionSet $articleExpressionSet)
    {        
        $articles = Article::orderBy('title')->get();
        return view('backend::article-expression-set.edit', compact('articleExpressionSet', 'articles'));
    }

    /**
     * Update the specified article expression set in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Modules\ArticleExpression\Models\ArticleExpressionSet  $articleExpressionSet
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, ArticleExpressionSet $articleExpressionSet)
    {        
        $validated = $request->validate([
            'article_id' => 'nullable|exists:articles,id',
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'display_order' => 'nullable|integer',
            'title_translation' => 'nullable|array',
            'content_translation' => 'nullable|array',
            'column_order' => 'nullable|string',
        ]);

        // Update basic fields
        $articleExpressionSet->article_id = $validated['article_id'] ?? null;
        $articleExpressionSet->title = $validated['title'];
        $articleExpressionSet->content = $validated['content'] ?? null;
        $articleExpressionSet->display_order = $validated['display_order'] ?? 0;
        
        // Set column order
        if (isset($validated['column_order']) && !empty($validated['column_order'])) {
            $articleExpressionSet->column_order = json_decode($validated['column_order'], true);
        }

        // Update translations
        if (isset($validated['title_translation']) && is_array($validated['title_translation'])) {
            foreach ($validated['title_translation'] as $locale => $value) {
                if (!empty($value)) {
                    $articleExpressionSet->setTranslation('title_translation', $locale, $value);
                }
            }
        }

        if (isset($validated['content_translation']) && is_array($validated['content_translation'])) {
            foreach ($validated['content_translation'] as $locale => $value) {
                if (!empty($value)) {
                    $articleExpressionSet->setTranslation('content_translation', $locale, $value);
                }
            }
        }

        $articleExpressionSet->save();

        return redirect()->route('backend::article-expression-sets.edit', $articleExpressionSet)
            ->with('success', 'Article expression set updated successfully');
    }

    /**
     * Remove the specified article expression set from storage.
     *
     * @param  \Modules\ArticleExpression\Models\ArticleExpressionSet  $articleExpressionSet
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(ArticleExpressionSet $articleExpressionSet)
    {        
        // Delete the article expression set
        $articleExpressionSet->delete();

        return response()->json(['success' => true]);
    }
    
    /**
     * Search articles for the select2 dropdown.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchArticles(Request $request)
    {
        $query = $request->input('q');
        $page = $request->input('page', 1);
        $perPage = 10;
        $exactId = $request->input('exact_id', false);
        
        $articlesQuery = Article::query();
        
        if ($exactId) {
            // If exact_id is true, search for the exact ID
            $articlesQuery->where('id', $query);
        } else {
            // Otherwise do a fuzzy search on title or ID
            $articlesQuery->where(function($q) use ($query) {
                $q->where('title', 'LIKE', '%' . $query . '%')
                  ->orWhere('id', $query);
            });
        }
        
        $articles = $articlesQuery->orderBy('title')
            ->paginate($perPage, ['id', 'title'], 'page', $page);
        
        return response()->json([
            'items' => $articles->items(),
            'pagination' => [
                'more' => $articles->hasMorePages()
            ]
        ]);
    }
}
