<?php

namespace Modules\Backend\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Article\Models\Article;
use Modules\ArticleTripleWord\Models\ArticleTripleWordSet;
use Yajra\DataTables\Facades\DataTables;

class ArticleTrippleWordSetController
{
    /**
     * Display a listing of the article triple word sets.
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {        
        return view('backend::article-triple-word-set.index');
    }

    /**
     * JSON response for the datatable.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index_json(Request $request)
    {        
        $model = ArticleTripleWordSet::with(['article', 'user']);

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
            ->addColumn('title_translation_text', function (ArticleTripleWordSet $tripleWordSet) {
                $translations = [];
                foreach ($tripleWordSet->getTranslations('title_translation') as $locale => $value) {
                    $translations[] = '<span class="language-label">' . strtoupper($locale) . ':</span> ' . e($value);
                }
                return !empty($translations) ? implode('<br>', $translations) : 'No translations available';
            })
            ->addColumn('content_translation_text', function (ArticleTripleWordSet $tripleWordSet) {
                $translations = [];
                foreach ($tripleWordSet->getTranslations('content_translation') as $locale => $value) {
                    $translations[] = '<span class="language-label">' . strtoupper($locale) . ':</span> ' . e(substr($value, 0, 100)) . (strlen($value) > 100 ? '...' : '');
                }
                return !empty($translations) ? implode('<br>', $translations) : 'No translations available';
            })
            ->addColumn('created_at_formatted', function (ArticleTripleWordSet $tripleWordSet) {
                return $tripleWordSet->created_at->format('Y-m-d H:i:s');
            })
            ->addColumn('updated_at_formatted', function (ArticleTripleWordSet $tripleWordSet) {
                return $tripleWordSet->updated_at->format('Y-m-d H:i:s');
            })
            ->addColumn('id', function (ArticleTripleWordSet $tripleWordSet) {
                return sprintf(
                    '<a href="%s">%s</a>',
                    route('backend::article-triple-word-sets.show', $tripleWordSet->id),
                    $tripleWordSet->id
                );
            })
            ->addColumn('title', function (ArticleTripleWordSet $tripleWordSet) {
                return sprintf(
                    '<a href="%s">%s</a>',
                    route('backend::article-triple-word-sets.show', $tripleWordSet->id),
                    $tripleWordSet->title
                );
            })
            ->addColumn('article_title', function (ArticleTripleWordSet $tripleWordSet) {
                return $tripleWordSet->article ? sprintf(
                    '<a href="%s">%s</a>',
                    route('backend::articles.show', $tripleWordSet->article->id),
                    $tripleWordSet->article->title
                ) : 'N/A';
            })
            ->addColumn('user_name', function (ArticleTripleWordSet $tripleWordSet) {
                return $tripleWordSet->user ? $tripleWordSet->user->name : 'N/A';
            })
            ->addColumn('actions', function (ArticleTripleWordSet $tripleWordSet) {
                $showUrl = route('backend::article-triple-word-sets.show', $tripleWordSet->id);
                $editUrl = route('backend::article-triple-word-sets.edit', $tripleWordSet->id);
                
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
                    $tripleWordSet->id
                );
            })
            ->rawColumns(['id', 'title', 'article_title', 'title_translation_text', 'content_translation_text', 'actions'])
            ->toJson();
    }

    /**
     * Show the form for creating a new article triple word set.
     * 
     * @return \Illuminate\View\View
     */
    public function create()
    {        
        $articles = Article::orderBy('title')->get();
        return view('backend::article-triple-word-set.create', compact('articles'));
    }

    /**
     * Store a newly created article triple word set in storage.
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

        // Create new triple word set
        $tripleWordSet = new ArticleTripleWordSet();
        $tripleWordSet->article_id = $validated['article_id'];
        $tripleWordSet->title = $validated['title'];
        $tripleWordSet->content = $validated['content'] ?? null;
        $tripleWordSet->display_order = $validated['display_order'] ?? 0;
        $tripleWordSet->user_id = Auth::id();
        $tripleWordSet->save();

        // Handle translations
        if (isset($validated['title_translation']) && is_array($validated['title_translation'])) {
            foreach ($validated['title_translation'] as $locale => $value) {
                if (!empty($value)) {
                    $tripleWordSet->setTranslation('title_translation', $locale, $value);
                }
            }
        }

        if (isset($validated['content_translation']) && is_array($validated['content_translation'])) {
            foreach ($validated['content_translation'] as $locale => $value) {
                if (!empty($value)) {
                    $tripleWordSet->setTranslation('content_translation', $locale, $value);
                }
            }
        }

        $tripleWordSet->save();

        return redirect()->route('backend::article-triple-word-sets.edit', $tripleWordSet)
            ->with('success', 'Article triple word set created successfully');
    }

    /**
     * Display the specified article triple word set.
     * 
     * @param  \Modules\ArticleTripleWord\Models\ArticleTripleWordSet  $articleTripleWordSet
     * @return \Illuminate\View\View
     */
    public function show(ArticleTripleWordSet $articleTripleWordSet)
    {        
        $articleTripleWordSet->load(['article', 'user', 'lists']);
        return view('backend::article-triple-word-set.show', compact('articleTripleWordSet'));
    }

    /**
     * Show the form for editing the specified article triple word set.
     * 
     * @param  \Modules\ArticleTripleWord\Models\ArticleTripleWordSet  $articleTripleWordSet
     * @return \Illuminate\View\View
     */
    public function edit(ArticleTripleWordSet $articleTripleWordSet)
    {        
        $articles = Article::orderBy('title')->get();
        return view('backend::article-triple-word-set.edit', compact('articleTripleWordSet', 'articles'));
    }

    /**
     * Update the specified article triple word set in storage.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \Modules\ArticleTripleWord\Models\ArticleTripleWordSet  $articleTripleWordSet
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, ArticleTripleWordSet $articleTripleWordSet)
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
        $articleTripleWordSet->article_id = $validated['article_id'] ?? null;
        $articleTripleWordSet->title = $validated['title'];
        $articleTripleWordSet->content = $validated['content'] ?? null;
        $articleTripleWordSet->display_order = $validated['display_order'] ?? 0;
        
        // Set column order
        if (isset($validated['column_order']) && !empty($validated['column_order'])) {
            $articleTripleWordSet->column_order = json_decode($validated['column_order'], true);
        }

        // Update translations
        if (isset($validated['title_translation']) && is_array($validated['title_translation'])) {
            foreach ($validated['title_translation'] as $locale => $value) {
                if (!empty($value)) {
                    $articleTripleWordSet->setTranslation('title_translation', $locale, $value);
                }
            }
        }

        if (isset($validated['content_translation']) && is_array($validated['content_translation'])) {
            foreach ($validated['content_translation'] as $locale => $value) {
                if (!empty($value)) {
                    $articleTripleWordSet->setTranslation('content_translation', $locale, $value);
                }
            }
        }

        $articleTripleWordSet->save();

        return redirect()->route('backend::article-triple-word-sets.edit', $articleTripleWordSet)
            ->with('success', 'Article triple word set updated successfully');
    }

    /**
     * Remove the specified article triple word set from storage.
     * 
     * @param  \Modules\ArticleTripleWord\Models\ArticleTripleWordSet  $articleTripleWordSet
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(ArticleTripleWordSet $articleTripleWordSet)
    {        
        // Delete the article triple word set
        $articleTripleWordSet->delete();

        return response()->json(['success' => true]);
    }
}
