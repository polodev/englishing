<?php

namespace Modules\Backend\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Article\Models\Article;
use Modules\ArticleSentence\Models\ArticleSentenceSet;
use Yajra\DataTables\Facades\DataTables;

class ArticleSentenceSetController
{
    /**
     * Display a listing of the article sentence sets.
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {        
        return view('backend::article-sentence-set.index');
    }

    /**
     * JSON response for the datatable.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index_json(Request $request)
    {        
        $model = ArticleSentenceSet::with(['article', 'user']);

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
            ->addColumn('title_translation_text', function (ArticleSentenceSet $sentenceSet) {
                $translations = [];
                foreach ($sentenceSet->getTranslations('title_translation') as $locale => $value) {
                    $translations[] = '<span class="language-label">' . strtoupper($locale) . ':</span> ' . e($value);
                }
                return !empty($translations) ? implode('<br>', $translations) : 'No translations available';
            })
            ->addColumn('content_translation_text', function (ArticleSentenceSet $sentenceSet) {
                $translations = [];
                foreach ($sentenceSet->getTranslations('content_translation') as $locale => $value) {
                    $translations[] = '<span class="language-label">' . strtoupper($locale) . ':</span> ' . e(substr($value, 0, 100)) . (strlen($value) > 100 ? '...' : '');
                }
                return !empty($translations) ? implode('<br>', $translations) : 'No translations available';
            })
            ->addColumn('created_at_formatted', function (ArticleSentenceSet $sentenceSet) {
                return $sentenceSet->created_at->format('Y-m-d H:i:s');
            })
            ->addColumn('updated_at_formatted', function (ArticleSentenceSet $sentenceSet) {
                return $sentenceSet->updated_at->format('Y-m-d H:i:s');
            })
            ->addColumn('id', function (ArticleSentenceSet $sentenceSet) {
                return sprintf(
                    '<a href="%s">%s</a>',
                    route('backend::article-sentence-sets.show', $sentenceSet->id),
                    $sentenceSet->id
                );
            })
            ->addColumn('title', function (ArticleSentenceSet $sentenceSet) {
                return sprintf(
                    '<a href="%s">%s</a>',
                    route('backend::article-sentence-sets.show', $sentenceSet->id),
                    $sentenceSet->title
                );
            })
            ->addColumn('article_title', function (ArticleSentenceSet $sentenceSet) {
                return $sentenceSet->article ? sprintf(
                    '<a href="%s">%s</a>',
                    route('backend::articles.show', $sentenceSet->article->id),
                    $sentenceSet->article->title
                ) : 'N/A';
            })
            ->addColumn('user_name', function (ArticleSentenceSet $sentenceSet) {
                return $sentenceSet->user ? $sentenceSet->user->name : 'N/A';
            })
            ->addColumn('actions', function (ArticleSentenceSet $sentenceSet) {
                $actions = '
                <div class="flex space-x-2">
                    <a href="' . route('backend::article-sentence-sets.show', $sentenceSet->id) . '" class="text-blue-600 hover:text-blue-900">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </a>
                    <a href="' . route('backend::article-sentence-sets.edit', $sentenceSet->id) . '" class="text-green-600 hover:text-green-900">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </a>';
                
                // Only show delete button for admin users
                $user = Auth::user();
                if (Auth::check() && $user && $user->role === 'admin') {
                    $actions .= '
                    <button type="button" class="text-red-600 hover:text-red-900 delete-sentence-set" data-id="' . $sentenceSet->id . '" data-title="' . $sentenceSet->title . '">
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
     * Show the form for creating a new article sentence set.
     * 
     * @return \Illuminate\View\View
     */
    public function create()
    {        
        $articles = Article::orderBy('title')->get();
        return view('backend::article-sentence-set.create', compact('articles'));
    }

    /**
     * Store a newly created article sentence set in storage.
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

        // Create a new article sentence set
        $sentenceSet = new ArticleSentenceSet();
        $sentenceSet->article_id = $validated['article_id'] ?? null;
        $sentenceSet->user_id = Auth::id();
        $sentenceSet->title = $validated['title'];
        $sentenceSet->content = $validated['content'] ?? null;
        $sentenceSet->display_order = $validated['display_order'] ?? 0;
        
        // Set column order
        if (isset($validated['column_order']) && !empty($validated['column_order'])) {
            $sentenceSet->column_order = json_decode($validated['column_order'], true);
        }

        // Set translations
        if (isset($validated['title_translation']) && is_array($validated['title_translation'])) {
            foreach ($validated['title_translation'] as $locale => $value) {
                if (!empty($value)) {
                    $sentenceSet->setTranslation('title_translation', $locale, $value);
                }
            }
        }

        if (isset($validated['content_translation']) && is_array($validated['content_translation'])) {
            foreach ($validated['content_translation'] as $locale => $value) {
                if (!empty($value)) {
                    $sentenceSet->setTranslation('content_translation', $locale, $value);
                }
            }
        }

        $sentenceSet->save();

        return redirect()->route('backend::article-sentence-sets.edit', $sentenceSet)
            ->with('success', 'Article sentence set created successfully');
    }

    /**
     * Display the specified article sentence set.
     * 
     * @param  \Modules\ArticleSentence\Models\ArticleSentenceSet  $articleSentenceSet
     * @return \Illuminate\View\View
     */
    public function show(ArticleSentenceSet $articleSentenceSet)
    {        
        $articleSentenceSet->load(['article', 'user', 'lists']);
        return view('backend::article-sentence-set.show', compact('articleSentenceSet'));
    }

    /**
     * Show the form for editing the specified article sentence set.
     * 
     * @param  \Modules\ArticleSentence\Models\ArticleSentenceSet  $articleSentenceSet
     * @return \Illuminate\View\View
     */
    public function edit(ArticleSentenceSet $articleSentenceSet)
    {        
        $articles = Article::orderBy('title')->get();
        return view('backend::article-sentence-set.edit', compact('articleSentenceSet', 'articles'));
    }

    /**
     * Update the specified article sentence set in storage.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \Modules\ArticleSentence\Models\ArticleSentenceSet  $articleSentenceSet
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, ArticleSentenceSet $articleSentenceSet)
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
        $articleSentenceSet->article_id = $validated['article_id'] ?? null;
        $articleSentenceSet->title = $validated['title'];
        $articleSentenceSet->content = $validated['content'] ?? null;
        $articleSentenceSet->display_order = $validated['display_order'] ?? 0;
        
        // Set column order
        if (isset($validated['column_order']) && !empty($validated['column_order'])) {
            $articleSentenceSet->column_order = json_decode($validated['column_order'], true);
        }

        // Update translations
        if (isset($validated['title_translation']) && is_array($validated['title_translation'])) {
            foreach ($validated['title_translation'] as $locale => $value) {
                if (!empty($value)) {
                    $articleSentenceSet->setTranslation('title_translation', $locale, $value);
                }
            }
        }

        if (isset($validated['content_translation']) && is_array($validated['content_translation'])) {
            foreach ($validated['content_translation'] as $locale => $value) {
                if (!empty($value)) {
                    $articleSentenceSet->setTranslation('content_translation', $locale, $value);
                }
            }
        }

        $articleSentenceSet->save();

        return redirect()->route('backend::article-sentence-sets.edit', $articleSentenceSet)
            ->with('success', 'Article sentence set updated successfully');
    }

    /**
     * Remove the specified article sentence set from storage.
     * 
     * @param  \Modules\ArticleSentence\Models\ArticleSentenceSet  $articleSentenceSet
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(ArticleSentenceSet $articleSentenceSet)
    {        
        // Delete the article sentence set
        $articleSentenceSet->delete();

        return response()->json(['success' => true]);
    }
    
    
}
