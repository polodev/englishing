<?php

namespace Modules\Backend\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Article\Models\Article;
use Modules\ArticleDoubleSentence\Models\ArticleDoubleSentenceSet;
use Yajra\DataTables\Facades\DataTables;

class ArticleDoubleSentenceSetController
{
    /**
     * Display a listing of the article double sentence sets.
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {        
        return view('backend::article-double-sentence-set.index');
    }

    /**
     * JSON response for the datatable.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index_json(Request $request)
    {        
        $model = ArticleDoubleSentenceSet::with(['article', 'user']);

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
            ->addColumn('title_translation_text', function (ArticleDoubleSentenceSet $doubleSentenceSet) {
                $translations = [];
                foreach ($doubleSentenceSet->getTranslations('title_translation') as $locale => $value) {
                    $translations[] = '<span class="language-label">' . strtoupper($locale) . ':</span> ' . e($value);
                }
                return !empty($translations) ? implode('<br>', $translations) : 'No translations available';
            })
            ->addColumn('content_translation_text', function (ArticleDoubleSentenceSet $doubleSentenceSet) {
                $translations = [];
                foreach ($doubleSentenceSet->getTranslations('content_translation') as $locale => $value) {
                    $translations[] = '<span class="language-label">' . strtoupper($locale) . ':</span> ' . e(substr($value, 0, 100)) . (strlen($value) > 100 ? '...' : '');
                }
                return !empty($translations) ? implode('<br>', $translations) : 'No translations available';
            })
            ->addColumn('created_at_formatted', function (ArticleDoubleSentenceSet $doubleSentenceSet) {
                return $doubleSentenceSet->created_at->format('Y-m-d H:i:s');
            })
            ->addColumn('updated_at_formatted', function (ArticleDoubleSentenceSet $doubleSentenceSet) {
                return $doubleSentenceSet->updated_at->format('Y-m-d H:i:s');
            })
            ->addColumn('id', function (ArticleDoubleSentenceSet $doubleSentenceSet) {
                return sprintf(
                    '<a href="%s">%s</a>',
                    route('backend::article-double-sentence-sets.show', $doubleSentenceSet->id),
                    $doubleSentenceSet->id
                );
            })
            ->addColumn('title', function (ArticleDoubleSentenceSet $doubleSentenceSet) {
                return sprintf(
                    '<a href="%s">%s</a>',
                    route('backend::article-double-sentence-sets.show', $doubleSentenceSet->id),
                    $doubleSentenceSet->title
                );
            })
            ->addColumn('article_title', function (ArticleDoubleSentenceSet $doubleSentenceSet) {
                return $doubleSentenceSet->article ? sprintf(
                    '<a href="%s">%s</a>',
                    route('backend::articles.show', $doubleSentenceSet->article->id),
                    $doubleSentenceSet->article->title
                ) : 'N/A';
            })
            ->addColumn('user_name', function (ArticleDoubleSentenceSet $doubleSentenceSet) {
                return $doubleSentenceSet->user ? $doubleSentenceSet->user->name : 'N/A';
            })
            ->addColumn('actions', function (ArticleDoubleSentenceSet $doubleSentenceSet) {
                $showUrl = route('backend::article-double-sentence-sets.show', $doubleSentenceSet->id);
                $editUrl = route('backend::article-double-sentence-sets.edit', $doubleSentenceSet->id);
                
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
                    $doubleSentenceSet->id
                );
            })
            ->rawColumns(['id', 'title', 'article_title', 'title_translation_text', 'content_translation_text', 'actions'])
            ->toJson();
    }

    /**
     * Show the form for creating a new article double sentence set.
     * 
     * @return \Illuminate\View\View
     */
    public function create()
    {        
        $articles = Article::orderBy('title')->get();
        return view('backend::article-double-sentence-set.create', compact('articles'));
    }

    /**
     * Store a newly created article double sentence set in storage.
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

        // Create new double sentence set
        $doubleSentenceSet = new ArticleDoubleSentenceSet();
        $doubleSentenceSet->article_id = $validated['article_id'];
        $doubleSentenceSet->title = $validated['title'];
        $doubleSentenceSet->content = $validated['content'] ?? null;
        $doubleSentenceSet->display_order = $validated['display_order'] ?? 0;
        $doubleSentenceSet->user_id = Auth::id();
        $doubleSentenceSet->save();

        // Handle translations
        if (isset($validated['title_translation']) && is_array($validated['title_translation'])) {
            foreach ($validated['title_translation'] as $locale => $value) {
                if (!empty($value)) {
                    $doubleSentenceSet->setTranslation('title_translation', $locale, $value);
                }
            }
        }

        if (isset($validated['content_translation']) && is_array($validated['content_translation'])) {
            foreach ($validated['content_translation'] as $locale => $value) {
                if (!empty($value)) {
                    $doubleSentenceSet->setTranslation('content_translation', $locale, $value);
                }
            }
        }

        $doubleSentenceSet->save();

        return redirect()->route('backend::article-double-sentence-sets.edit', $doubleSentenceSet)
            ->with('success', 'Article double sentence set created successfully');
    }

    /**
     * Display the specified article double sentence set.
     * 
     * @param  \Modules\ArticleDoubleSentence\Models\ArticleDoubleSentenceSet  $articleDoubleSentenceSet
     * @return \Illuminate\View\View
     */
    public function show(ArticleDoubleSentenceSet $articleDoubleSentenceSet)
    {        
        $articleDoubleSentenceSet->load(['article', 'user', 'lists']);
        return view('backend::article-double-sentence-set.show', compact('articleDoubleSentenceSet'));
    }

    /**
     * Show the form for editing the specified article double sentence set.
     * 
     * @param  \Modules\ArticleDoubleSentence\Models\ArticleDoubleSentenceSet  $articleDoubleSentenceSet
     * @return \Illuminate\View\View
     */
    public function edit(ArticleDoubleSentenceSet $articleDoubleSentenceSet)
    {        
        $articles = Article::orderBy('title')->get();
        return view('backend::article-double-sentence-set.edit', compact('articleDoubleSentenceSet', 'articles'));
    }

    /**
     * Update the specified article double sentence set in storage.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \Modules\ArticleDoubleSentence\Models\ArticleDoubleSentenceSet  $articleDoubleSentenceSet
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, ArticleDoubleSentenceSet $articleDoubleSentenceSet)
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
        $articleDoubleSentenceSet->article_id = $validated['article_id'] ?? null;
        $articleDoubleSentenceSet->title = $validated['title'];
        $articleDoubleSentenceSet->content = $validated['content'] ?? null;
        $articleDoubleSentenceSet->display_order = $validated['display_order'] ?? 0;
        
        // Set column order
        if (isset($validated['column_order']) && !empty($validated['column_order'])) {
            $articleDoubleSentenceSet->column_order = json_decode($validated['column_order'], true);
        }

        // Update translations
        if (isset($validated['title_translation']) && is_array($validated['title_translation'])) {
            foreach ($validated['title_translation'] as $locale => $value) {
                if (!empty($value)) {
                    $articleDoubleSentenceSet->setTranslation('title_translation', $locale, $value);
                }
            }
        }

        if (isset($validated['content_translation']) && is_array($validated['content_translation'])) {
            foreach ($validated['content_translation'] as $locale => $value) {
                if (!empty($value)) {
                    $articleDoubleSentenceSet->setTranslation('content_translation', $locale, $value);
                }
            }
        }

        $articleDoubleSentenceSet->save();

        return redirect()->route('backend::article-double-sentence-sets.edit', $articleDoubleSentenceSet)
            ->with('success', 'Article double sentence set updated successfully');
    }

    /**
     * Remove the specified article double sentence set from storage.
     * 
     * @param  \Modules\ArticleDoubleSentence\Models\ArticleDoubleSentenceSet  $articleDoubleSentenceSet
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(ArticleDoubleSentenceSet $articleDoubleSentenceSet)
    {        
        // Delete the article double sentence set
        $articleDoubleSentenceSet->delete();

        return response()->json(['success' => true]);
    }
}
