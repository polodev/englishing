<?php

namespace Modules\Backend\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\Tag\Models\Tag;
use Yajra\DataTables\Facades\DataTables;

class TagController
{
    /**
     * Display a listing of the tags.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('backend::tag.index');
    }

    /**
     * JSON response for the datatable.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index_json(Request $request)
    {
        // Get current authenticated user
        $user = $request->user();
        $model = Tag::query();

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
            ->addColumn('title_translation_text', function (Tag $tag) {
                $translations = [];
                $locales = ['bn', 'hi'];
                
                foreach ($locales as $locale) {
                    $translation = $tag->getTranslation('title_translation', $locale, false);
                    if ($translation) {
                        $translations[] = '<span class="language-label">' . strtoupper($locale) . ':</span> ' . e($translation);
                    }
                }
                
                return !empty($translations) ? implode('<br>', $translations) : 'No translations available';
            })
            ->addColumn('created_at_formatted', function (Tag $tag) {
                return $tag->created_at->format('Y-m-d H:i:s');
            })
            ->addColumn('updated_at_formatted', function (Tag $tag) {
                return $tag->updated_at->format('Y-m-d H:i:s');
            })
            ->addColumn('id', function (Tag $tag) {
                return sprintf(
                    '<a href="%s">%s</a>',
                    route('backend::tags.show', $tag->id),
                    $tag->id
                );
            })
            ->addColumn('title', function (Tag $tag) {
                return sprintf(
                    '<a href="%s">%s</a>',
                    route('backend::tags.show', $tag->id),
                    $tag->title
                );
            })
            ->addColumn('article_count', function (Tag $tag) {
                return $tag->articles()->count();
            })
            ->addColumn('actions', function (Tag $tag) use ($user) {
                $actions = '
                <div class="flex space-x-2">
                    <a href="' . route('backend::tags.show', $tag->id) . '" class="text-blue-600 hover:text-blue-900">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </a>
                    <a href="' . route('backend::tags.edit', $tag->id) . '" class="text-green-600 hover:text-green-900">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </a>';
                
                // Only show delete button for admin users
                if ($user && $user->hasAnyRole(['admin'])) {
                    $actions .= '
                    <button type="button" class="text-red-600 hover:text-red-900 delete-tag" data-id="' . $tag->id . '" data-title="' . $tag->title . '">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>';
                }
                
                $actions .= '
                </div>';
                
                return $actions;
            })
            ->rawColumns(['id', 'title', 'title_translation_text', 'actions'])
            ->toJson();
    }

    /**
     * Show the form for creating a new tag.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('backend::tag.create');
    }

    /**
     * Store a newly created tag in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:tags,slug',
            'title_translation' => 'nullable|array',
        ]);

        // Create new tag
        $tag = new Tag();
        $tag->title = $validated['title'];
        $tag->slug = $validated['slug'];

        // Set translations using setTranslation
        if (isset($validated['title_translation']) && is_array($validated['title_translation'])) {
            foreach ($validated['title_translation'] as $locale => $value) {
                if (!empty($value)) {
                    $tag->setTranslation('title_translation', $locale, $value);
                }
            }
        }

        $tag->save();

        return redirect()->route('backend::tags.show', $tag)
            ->with('success', 'Tag created successfully');
    }

    /**
     * Display the specified tag.
     *
     * @param  \Modules\Tag\Models\Tag  $tag
     * @return \Illuminate\View\View
     */
    public function show(Tag $tag)
    {
        // Load associated articles
        $tag->load('articles');
        
        return view('backend::tag.show', compact('tag'));
    }

    /**
     * Show the form for editing the specified tag.
     *
     * @param  \Modules\Tag\Models\Tag  $tag
     * @return \Illuminate\View\View
     */
    public function edit(Tag $tag)
    {
        return view('backend::tag.edit', compact('tag'));
    }

    /**
     * Update the specified tag in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Modules\Tag\Models\Tag  $tag
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Tag $tag)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:tags,slug,' . $tag->id,
            'title_translation' => 'nullable|array',
        ]);

        // Update tag fields
        $tag->title = $validated['title'];
        $tag->slug = $validated['slug'];

        // Update translations using setTranslation
        if (isset($validated['title_translation']) && is_array($validated['title_translation'])) {
            foreach ($validated['title_translation'] as $locale => $value) {
                if (!empty($value)) {
                    $tag->setTranslation('title_translation', $locale, $value);
                }
            }
        }

        $tag->save();

        return redirect()->route('backend::tags.edit', $tag)
            ->with('success', 'Tag updated successfully');
    }

    /**
     * Remove the specified tag from storage.
     *
     * @param  \Modules\Tag\Models\Tag  $tag
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Tag $tag)
    {
        // Delete the tag
        $tag->delete();

        return response()->json(['success' => true]);
    }
}
