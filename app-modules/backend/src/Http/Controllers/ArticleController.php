<?php

namespace Modules\Backend\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\Article\Models\Article;
use Modules\Article\Models\Course;
use Yajra\DataTables\Facades\DataTables;

class ArticleController
{
    /**
     * Display a listing of the articles.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('backend::article.index');
    }

    /**
     * JSON response for the datatable.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index_json(Request $request)
    {
        $model = Article::with(['course', 'user']);

        return DataTables::eloquent($model)
            ->filter(function ($query) use ($request) {
                // Filter by created_at date range
                if ($request->has('created_from') && $request->created_from) {
                    $query->whereDate('created_at', '>=', $request->created_from);
                }

                if ($request->has('created_to') && $request->created_to) {
                    $query->whereDate('created_at', '<=', $request->created_to);
                }

                // Filter by course
                if ($request->has('course_id') && $request->course_id) {
                    $query->where('course_id', $request->course_id);
                }
            }, true)
            ->addColumn('title_translation_text', function (Article $article) {
                $translations = [];
                foreach ($article->getTranslations('title_translation') as $locale => $value) {
                    $translations[] = '<span class="language-label">' . strtoupper($locale) . ':</span> ' . e($value);
                }
                return !empty($translations) ? implode('<br>', $translations) : 'No translations available';
            })
            ->addColumn('excerpt_translation_text', function (Article $article) {
                $translations = [];
                foreach ($article->getTranslations('excerpt_translation') as $locale => $value) {
                    $translations[] = '<span class="language-label">' . strtoupper($locale) . ':</span> ' . Str::limit(strip_tags($value), 100);
                }
                return !empty($translations) ? implode('<br>', $translations) : 'No translations available';
            })
            ->addColumn('created_at_formatted', function (Article $article) {
                return $article->created_at->format('Y-m-d H:i:s');
            })
            ->addColumn('updated_at_formatted', function (Article $article) {
                return $article->updated_at->format('Y-m-d H:i:s');
            })
            ->addColumn('id', function (Article $article) {
                return sprintf(
                    '<a href="%s">%s</a>',
                    route('backend::articles.show', $article->id),
                    $article->id
                );
            })
            ->addColumn('title', function (Article $article) {
                return sprintf(
                    '<a href="%s">%s</a>',
                    route('backend::articles.show', $article->id),
                    $article->title
                );
            })
            ->addColumn('course_title', function (Article $article) {
                return $article->course ? sprintf(
                    '<a href="%s">%s</a>',
                    route('backend::courses.show', $article->course->id),
                    $article->course->title
                ) : 'N/A';
            })
            ->addColumn('user_name', function (Article $article) {
                return $article->user ? $article->user->name : 'N/A';
            })
            ->addColumn('actions', function (Article $article) {
                return '
                <div class="flex space-x-2">
                    <a href="' . route('backend::articles.show', $article->id) . '" class="text-blue-600 hover:text-blue-900">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </a>
                    <a href="' . route('backend::articles.edit', $article->id) . '" class="text-green-600 hover:text-green-900">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </a>
                </div>
                ';
            })
            ->rawColumns(['id', 'title', 'course_title', 'title_translation_text', 'excerpt_translation_text', 'actions'])
            ->toJson();
    }

    /**
     * Show the form for creating a new article.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $courses = Course::orderBy('title')->get();
        return view('backend::article.create', compact('courses'));
    }

    /**
     * Store a newly created article in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:articles,slug',
            'course_id' => 'nullable|exists:courses,id',
            'content' => 'nullable|string',
            'excerpt' => 'nullable|string',
            'display_order' => 'nullable|integer',
            'is_premium' => 'boolean',
            'title_translation' => 'nullable|array',
            'content_translation' => 'nullable|array',
            'excerpt_translation' => 'nullable|array',
        ]);

        // Create the article with basic fields
        $article = new Article();
        $article->title = $validated['title'];
        $article->slug = $validated['slug'];
        $article->course_id = $validated['course_id'] ?? null;
        $article->content = $validated['content'] ?? null;
        $article->excerpt = $validated['excerpt'] ?? null;
        $article->display_order = $validated['display_order'] ?? 0;
        $article->is_premium = $validated['is_premium'] ?? false;
        $article->user_id = request()->user() ? request()->user()->id : null;

        // Save the article first to get an ID
        $article->save();

        // Handle translations
        if (isset($validated['title_translation']) && is_array($validated['title_translation'])) {
            foreach ($validated['title_translation'] as $locale => $value) {
                if (!empty($value)) {
                    $article->setTranslation('title_translation', $locale, $value);
                }
            }
        }

        if (isset($validated['content_translation']) && is_array($validated['content_translation'])) {
            foreach ($validated['content_translation'] as $locale => $value) {
                if (!empty($value)) {
                    $article->setTranslation('content_translation', $locale, $value);
                }
            }
        }

        if (isset($validated['excerpt_translation']) && is_array($validated['excerpt_translation'])) {
            foreach ($validated['excerpt_translation'] as $locale => $value) {
                if (!empty($value)) {
                    $article->setTranslation('excerpt_translation', $locale, $value);
                }
            }
        }

        // Save the article again with translations
        $article->save();

        return redirect()->route('backend::articles.show', $article)
            ->with('success', 'Article created successfully');
    }

    /**
     * Display the specified article.
     *
     * @param  \Modules\Article\Models\Article  $article
     * @return \Illuminate\View\View
     */
    public function show(Article $article)
    {
        $article->load(['course', 'user']);
        
        // Get associated articles if this article belongs to a course
        $associatedArticles = $article->getAssociatedArticles();
        
        // Get article word set using the relationship
        $articleWordSet = $article->wordSet;
        
        // Get article expression set using the relationship
        $articleExpressionSet = $article->expressionSet;
        
        return view('backend::article.show', compact('article', 'associatedArticles', 'articleWordSet', 'articleExpressionSet'));
    }

    /**
     * Show the form for editing the specified article.
     *
     * @param  \Modules\Article\Models\Article  $article
     * @return \Illuminate\View\View
     */
    public function edit(Article $article)
    {
        $courses = Course::orderBy('title')->get();
        return view('backend::article.edit', compact('article', 'courses'));
    }

    /**
     * Update the specified article in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Modules\Article\Models\Article  $article
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Article $article)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:articles,slug,' . $article->id,
            'course_id' => 'nullable|exists:courses,id',
            'content' => 'nullable|string',
            'excerpt' => 'nullable|string',
            'display_order' => 'nullable|integer',
            'is_premium' => 'boolean',
            'title_translation' => 'nullable|array',
            'content_translation' => 'nullable|array',
            'excerpt_translation' => 'nullable|array',
        ]);

        // Update basic fields
        $article->title = $validated['title'];
        $article->slug = $validated['slug'];
        $article->course_id = $validated['course_id'] ?? null;
        $article->content = $validated['content'] ?? null;
        $article->excerpt = $validated['excerpt'] ?? null;
        $article->display_order = $validated['display_order'] ?? 0;
        $article->is_premium = $validated['is_premium'] ?? false;

        // Update translations
        if (isset($validated['title_translation']) && is_array($validated['title_translation'])) {
            foreach ($validated['title_translation'] as $locale => $value) {
                if (!empty($value)) {
                    $article->setTranslation('title_translation', $locale, $value);
                }
            }
        }

        if (isset($validated['content_translation']) && is_array($validated['content_translation'])) {
            foreach ($validated['content_translation'] as $locale => $value) {
                if (!empty($value)) {
                    $article->setTranslation('content_translation', $locale, $value);
                }
            }
        }

        if (isset($validated['excerpt_translation']) && is_array($validated['excerpt_translation'])) {
            foreach ($validated['excerpt_translation'] as $locale => $value) {
                if (!empty($value)) {
                    $article->setTranslation('excerpt_translation', $locale, $value);
                }
            }
        }

        $article->save();

        return redirect()->route('backend::articles.edit', $article)
            ->with('success', 'Article updated successfully');
    }

    /**
     * Remove the specified article from storage.
     *
     * @param  \Modules\Article\Models\Article  $article
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Article $article)
    {
        // Delete the article
        $article->delete();

        return response()->json(['success' => true]);
    }
}
