<?php

namespace Modules\Backend\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Article\Models\Article;
use Modules\ArticleConversation\Models\ArticleConversationSet;
use Yajra\DataTables\Facades\DataTables;

class ArticleConversationController
{
    /**
     * Display a listing of the article conversation sets.
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {        
        return view('backend::article-conversation.index');
    }

    /**
     * JSON response for the datatable.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index_json(Request $request)
    {        
        $model = ArticleConversationSet::with(['article', 'user']);

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
            ->addColumn('title_translation_text', function (ArticleConversationSet $conversationSet) {
                $translations = [];
                foreach ($conversationSet->getTranslations('title_translation') as $locale => $value) {
                    $translations[] = '<span class="language-label">' . strtoupper($locale) . ':</span> ' . e($value);
                }
                return !empty($translations) ? implode('<br>', $translations) : 'No translations available';
            })
            ->addColumn('content_translation_text', function (ArticleConversationSet $conversationSet) {
                $translations = [];
                foreach ($conversationSet->getTranslations('content_translation') as $locale => $value) {
                    $translations[] = '<span class="language-label">' . strtoupper($locale) . ':</span> ' . e(substr($value, 0, 100)) . (strlen($value) > 100 ? '...' : '');
                }
                return !empty($translations) ? implode('<br>', $translations) : 'No translations available';
            })
            ->addColumn('created_at_formatted', function (ArticleConversationSet $conversationSet) {
                return $conversationSet->created_at->format('Y-m-d H:i:s');
            })
            ->addColumn('updated_at_formatted', function (ArticleConversationSet $conversationSet) {
                return $conversationSet->updated_at->format('Y-m-d H:i:s');
            })
            ->addColumn('id', function (ArticleConversationSet $conversationSet) {
                return sprintf(
                    '<a href="%s">%s</a>',
                    route('backend::article-conversation-sets.show', $conversationSet->id),
                    $conversationSet->id
                );
            })
            ->addColumn('title', function (ArticleConversationSet $conversationSet) {
                return sprintf(
                    '<a href="%s">%s</a>',
                    route('backend::article-conversation-sets.show', $conversationSet->id),
                    $conversationSet->title
                );
            })
            ->addColumn('article_title', function (ArticleConversationSet $conversationSet) {
                return $conversationSet->article ? sprintf(
                    '<a href="%s">%s</a>',
                    route('backend::articles.show', $conversationSet->article->id),
                    $conversationSet->article->title
                ) : 'N/A';
            })
            ->addColumn('user_name', function (ArticleConversationSet $conversationSet) {
                return $conversationSet->user ? $conversationSet->user->name : 'N/A';
            })
            ->addColumn('actions', function (ArticleConversationSet $conversationSet) {
                $showUrl = route('backend::article-conversation-sets.show', $conversationSet->id);
                $editUrl = route('backend::article-conversation-sets.edit', $conversationSet->id);
                
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
                    $conversationSet->id
                );
            })
            ->rawColumns(['id', 'title', 'article_title', 'title_translation_text', 'content_translation_text', 'actions'])
            ->toJson();
    }

    /**
     * Show the form for creating a new article conversation set.
     * 
     * @return \Illuminate\View\View
     */
    public function create()
    {        
        $articles = Article::orderBy('title')->get();
        return view('backend::article-conversation.create', compact('articles'));
    }

    /**
     * Store a newly created article conversation set in storage.
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

        // Create new conversation set
        $conversationSet = new ArticleConversationSet();
        $conversationSet->article_id = $validated['article_id'];
        $conversationSet->title = $validated['title'];
        $conversationSet->content = $validated['content'] ?? null;
        $conversationSet->display_order = $validated['display_order'] ?? 0;
        $conversationSet->user_id = Auth::id();
        $conversationSet->save();

        // Handle translations
        if (isset($validated['title_translation']) && is_array($validated['title_translation'])) {
            foreach ($validated['title_translation'] as $locale => $value) {
                if (!empty($value)) {
                    $conversationSet->setTranslation('title_translation', $locale, $value);
                }
            }
        }

        if (isset($validated['content_translation']) && is_array($validated['content_translation'])) {
            foreach ($validated['content_translation'] as $locale => $value) {
                if (!empty($value)) {
                    $conversationSet->setTranslation('content_translation', $locale, $value);
                }
            }
        }

        $conversationSet->save();

        return redirect()->route('backend::article-conversation-sets.edit', $conversationSet)
            ->with('success', 'Article conversation set created successfully');
    }

    /**
     * Display the specified article conversation set.
     * 
     * @param  \Modules\ArticleConversation\Models\ArticleConversationSet  $articleConversationSet
     * @return \Illuminate\View\View
     */
    public function show(ArticleConversationSet $articleConversationSet)
    {        
        $articleConversationSet->load(['article', 'user']);
        return view('backend::article-conversation.show', compact('articleConversationSet'));
    }

    /**
     * Show the form for editing the specified article conversation set.
     * 
     * @param  \Modules\ArticleConversation\Models\ArticleConversationSet  $articleConversationSet
     * @return \Illuminate\View\View
     */
    public function edit(ArticleConversationSet $articleConversationSet)
    {        
        $articles = Article::orderBy('title')->get();
        return view('backend::article-conversation.edit', compact('articleConversationSet', 'articles'));
    }

    /**
     * Update the specified article conversation set in storage.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \Modules\ArticleConversation\Models\ArticleConversationSet  $articleConversationSet
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, ArticleConversationSet $articleConversationSet)
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
        $articleConversationSet->article_id = $validated['article_id'] ?? null;
        $articleConversationSet->title = $validated['title'];
        $articleConversationSet->content = $validated['content'] ?? null;
        $articleConversationSet->display_order = $validated['display_order'] ?? 0;
        
        // Set column order
        if (isset($validated['column_order']) && !empty($validated['column_order'])) {
            $articleConversationSet->column_order = json_decode($validated['column_order'], true);
        }

        // Update translations
        if (isset($validated['title_translation']) && is_array($validated['title_translation'])) {
            foreach ($validated['title_translation'] as $locale => $value) {
                if (!empty($value)) {
                    $articleConversationSet->setTranslation('title_translation', $locale, $value);
                }
            }
        }

        if (isset($validated['content_translation']) && is_array($validated['content_translation'])) {
            foreach ($validated['content_translation'] as $locale => $value) {
                if (!empty($value)) {
                    $articleConversationSet->setTranslation('content_translation', $locale, $value);
                }
            }
        }

        $articleConversationSet->save();

        return redirect()->route('backend::article-conversation-sets.edit', $articleConversationSet)
            ->with('success', 'Article conversation set updated successfully');
    }

    /**
     * Remove the specified article conversation set from storage.
     * 
     * @param  \Modules\ArticleConversation\Models\ArticleConversationSet  $articleConversationSet
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(ArticleConversationSet $articleConversationSet)
    {        
        // Delete the article conversation set
        $articleConversationSet->delete();

        return response()->json(['success' => true]);
    }
}
