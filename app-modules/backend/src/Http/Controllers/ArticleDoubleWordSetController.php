<?php

namespace Modules\Backend\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Article\Models\Article;
use Modules\ArticleDoubleWord\Models\ArticleDoubleWordSet;
use Yajra\DataTables\Facades\DataTables;

class ArticleDoubleWordSetController
{
    /**
     * Display a listing of the article double word sets.
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {        
        return view('backend::article-double-word-set.index');
    }

    /**
     * JSON response for the datatable.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index_json(Request $request)
    {        
        $model = ArticleDoubleWordSet::with(['article', 'user']);

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
            ->addColumn('title_translation_text', function (ArticleDoubleWordSet $doubleWordSet) {
                $translations = [];
                foreach ($doubleWordSet->getTranslations('title_translation') as $locale => $value) {
                    $translations[] = '<span class="language-label">' . strtoupper($locale) . ':</span> ' . e($value);
                }
                return !empty($translations) ? implode('<br>', $translations) : 'No translations available';
            })
            ->addColumn('content_translation_text', function (ArticleDoubleWordSet $doubleWordSet) {
                $translations = [];
                foreach ($doubleWordSet->getTranslations('content_translation') as $locale => $value) {
                    $translations[] = '<span class="language-label">' . strtoupper($locale) . ':</span> ' . e(substr($value, 0, 100)) . (strlen($value) > 100 ? '...' : '');
                }
                return !empty($translations) ? implode('<br>', $translations) : 'No translations available';
            })
            ->addColumn('created_at_formatted', function (ArticleDoubleWordSet $doubleWordSet) {
                return $doubleWordSet->created_at->format('Y-m-d H:i:s');
            })
            ->addColumn('updated_at_formatted', function (ArticleDoubleWordSet $doubleWordSet) {
                return $doubleWordSet->updated_at->format('Y-m-d H:i:s');
            })
            ->addColumn('id', function (ArticleDoubleWordSet $doubleWordSet) {
                return sprintf(
                    '<a href="%s">%s</a>',
                    route('backend::article-double-word-sets.show', $doubleWordSet->id),
                    $doubleWordSet->id
                );
            })
            ->addColumn('title', function (ArticleDoubleWordSet $doubleWordSet) {
                return sprintf(
                    '<a href="%s">%s</a>',
                    route('backend::article-double-word-sets.show', $doubleWordSet->id),
                    $doubleWordSet->title
                );
            })
            ->addColumn('article_title', function (ArticleDoubleWordSet $doubleWordSet) {
                return $doubleWordSet->article ? sprintf(
                    '<a href="%s">%s</a>',
                    route('backend::articles.show', $doubleWordSet->article->id),
                    $doubleWordSet->article->title
                ) : 'N/A';
            })
            ->addColumn('user_name', function (ArticleDoubleWordSet $doubleWordSet) {
                return $doubleWordSet->user ? $doubleWordSet->user->name : 'N/A';
            })
            ->addColumn('actions', function (ArticleDoubleWordSet $doubleWordSet) {
                $showUrl = route('backend::article-double-word-sets.show', $doubleWordSet->id);
                $editUrl = route('backend::article-double-word-sets.edit', $doubleWordSet->id);
                
                return sprintf(
                    '<div class="btn-group" role="group">
                        <a href="%s" class="btn btn-sm btn-info">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="%s" class="btn btn-sm btn-primary">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button type="button" class="btn btn-sm btn-danger delete-item" data-id="%s">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>',
                    $showUrl,
                    $editUrl,
                    $doubleWordSet->id
                );
            })
            ->rawColumns(['id', 'title', 'article_title', 'title_translation_text', 'content_translation_text', 'actions'])
            ->toJson();
    }

    /**
     * Show the form for creating a new article double word set.
     * 
     * @return \Illuminate\View\View
     */
    public function create()
    {        
        $articles = Article::orderBy('title')->get();
        return view('backend::article-double-word-set.create', compact('articles'));
    }

    /**
     * Store a newly created article double word set in storage.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {        
        $validated = $request->validate([
            'article_id' => 'required|exists:articles,id',
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'display_order' => 'nullable|integer',
            'title_translation' => 'nullable|array',
            'content_translation' => 'nullable|array',
        ]);

        // Create new double word set
        $doubleWordSet = new ArticleDoubleWordSet();
        $doubleWordSet->article_id = $validated['article_id'];
        $doubleWordSet->title = $validated['title'];
        $doubleWordSet->content = $validated['content'] ?? null;
        $doubleWordSet->display_order = $validated['display_order'] ?? 0;
        $doubleWordSet->user_id = Auth::id();
        $doubleWordSet->save();

        // Handle translations
        if (isset($validated['title_translation']) && is_array($validated['title_translation'])) {
            foreach ($validated['title_translation'] as $locale => $value) {
                if (!empty($value)) {
                    $doubleWordSet->setTranslation('title_translation', $locale, $value);
                }
            }
        }

        if (isset($validated['content_translation']) && is_array($validated['content_translation'])) {
            foreach ($validated['content_translation'] as $locale => $value) {
                if (!empty($value)) {
                    $doubleWordSet->setTranslation('content_translation', $locale, $value);
                }
            }
        }

        $doubleWordSet->save();

        return redirect()->route('backend::article-double-word-sets.edit', $doubleWordSet)
            ->with('success', 'Article double word set created successfully');
    }

    /**
     * Display the specified article double word set.
     * 
     * @param  \Modules\ArticleDoubleWord\Models\ArticleDoubleWordSet  $articleDoubleWordSet
     * @return \Illuminate\View\View
     */
    public function show(ArticleDoubleWordSet $articleDoubleWordSet)
    {        
        $articleDoubleWordSet->load(['article', 'user', 'lists']);
        return view('backend::article-double-word-set.show', compact('articleDoubleWordSet'));
    }

    /**
     * Show the form for editing the specified article double word set.
     * 
     * @param  \Modules\ArticleDoubleWord\Models\ArticleDoubleWordSet  $articleDoubleWordSet
     * @return \Illuminate\View\View
     */
    public function edit(ArticleDoubleWordSet $articleDoubleWordSet)
    {        
        $articles = Article::orderBy('title')->get();
        return view('backend::article-double-word-set.edit', compact('articleDoubleWordSet', 'articles'));
    }

    /**
     * Update the specified article double word set in storage.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \Modules\ArticleDoubleWord\Models\ArticleDoubleWordSet  $articleDoubleWordSet
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, ArticleDoubleWordSet $articleDoubleWordSet)
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
        $articleDoubleWordSet->article_id = $validated['article_id'] ?? null;
        $articleDoubleWordSet->title = $validated['title'];
        $articleDoubleWordSet->content = $validated['content'] ?? null;
        $articleDoubleWordSet->display_order = $validated['display_order'] ?? 0;
        
        // Set column order
        if (isset($validated['column_order']) && !empty($validated['column_order'])) {
            $articleDoubleWordSet->column_order = json_decode($validated['column_order'], true);
        }

        // Update translations
        if (isset($validated['title_translation']) && is_array($validated['title_translation'])) {
            foreach ($validated['title_translation'] as $locale => $value) {
                if (!empty($value)) {
                    $articleDoubleWordSet->setTranslation('title_translation', $locale, $value);
                }
            }
        }

        if (isset($validated['content_translation']) && is_array($validated['content_translation'])) {
            foreach ($validated['content_translation'] as $locale => $value) {
                if (!empty($value)) {
                    $articleDoubleWordSet->setTranslation('content_translation', $locale, $value);
                }
            }
        }

        $articleDoubleWordSet->save();

        return redirect()->route('backend::article-double-word-sets.edit', $articleDoubleWordSet)
            ->with('success', 'Article double word set updated successfully');
    }

    /**
     * Remove the specified article double word set from storage.
     * 
     * @param  \Modules\ArticleDoubleWord\Models\ArticleDoubleWordSet  $articleDoubleWordSet
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(ArticleDoubleWordSet $articleDoubleWordSet)
    {        
        // Delete the article double word set
        $articleDoubleWordSet->delete();

        return response()->json(['success' => true]);
    }
}
