<?php

namespace Modules\Backend\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\Article\Models\Course;
use Modules\Article\Models\Article;
use Yajra\DataTables\Facades\DataTables;

class CourseController
{
    /**
     * Display a listing of the courses.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('backend::course.index');
    }

    /**
     * JSON response for the datatable.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index_json(Request $request)
    {
        $model = Course::with(['articles', 'user']);

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
            ->addColumn('article_count', function (Course $course) {
                return $course->articles->count();
            })
            ->addColumn('title_translation_text', function (Course $course) {
                $translations = [];
                foreach ($course->getTranslations('title_translation') as $locale => $value) {
                    $translations[] = '<span class="language-label">' . strtoupper($locale) . ':</span> ' . e($value);
                }
                return !empty($translations) ? implode('<br>', $translations) : 'No translations available';
            })
            ->addColumn('content_translation_text', function (Course $course) {
                $translations = [];
                foreach ($course->getTranslations('content_translation') as $locale => $value) {
                    $translations[] = '<span class="language-label">' . strtoupper($locale) . ':</span> ' . Str::limit(strip_tags($value), 100);
                }
                return !empty($translations) ? implode('<br>', $translations) : 'No translations available';
            })
            ->addColumn('created_at_formatted', function (Course $course) {
                return $course->created_at->format('Y-m-d H:i:s');
            })
            ->addColumn('updated_at_formatted', function (Course $course) {
                return $course->updated_at->format('Y-m-d H:i:s');
            })
            ->addColumn('id', function (Course $course) {
                return sprintf(
                    '<a href="%s">%s</a>',
                    route('backend::courses.show', $course->id),
                    $course->id
                );
            })
            ->addColumn('title', function (Course $course) {
                return sprintf(
                    '<a href="%s">%s</a>',
                    route('backend::courses.show', $course->id),
                    $course->title
                );
            })
            ->addColumn('user_name', function (Course $course) {
                return $course->user ? $course->user->name : 'N/A';
            })
            ->addColumn('actions', function (Course $course) {
                return '
                <div class="flex space-x-2">
                    <a href="' . route('backend::courses.show', $course->id) . '" class="text-blue-600 hover:text-blue-900">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </a>
                    <a href="' . route('backend::courses.edit', $course->id) . '" class="text-green-600 hover:text-green-900">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </a>
                    <button type="button" data-id="' . $course->id . '" class="delete-course text-red-600 hover:text-red-900">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
                ';
            })
            ->rawColumns(['id', 'title', 'title_translation_text', 'content_translation_text', 'actions'])
            ->toJson();
    }

    /**
     * Display the specified course.
     *
     * @param  \Modules\Article\Models\Course  $course
     * @return \Illuminate\View\View
     */
    public function show(Course $course)
    {
        $course->load(['articles', 'user']);
        
        // Sort articles by display_order
        $articles = $course->articles->sortBy('display_order');
        
        return view('backend::course.show', compact('course', 'articles'));
    }

    /**
     * Show the form for editing the specified course.
     *
     * @param  \Modules\Article\Models\Course  $course
     * @return \Illuminate\View\View
     */
    public function edit(Course $course)
    {
        return view('backend::course.edit', compact('course'));
    }

    /**
     * Update the specified course in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Modules\Article\Models\Course  $course
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:courses,slug,' . $course->id,
            'content' => 'nullable|string',
            'title_translation' => 'nullable|array',
            'content_translation' => 'nullable|array',
        ]);

        // Update basic fields
        $course->title = $validated['title'];
        $course->slug = $validated['slug'];
        $course->content = $validated['content'] ?? null;

        // Update translations
        if (isset($validated['title_translation']) && is_array($validated['title_translation'])) {
            foreach ($validated['title_translation'] as $locale => $value) {
                if (!empty($value)) {
                    $course->setTranslation('title_translation', $locale, $value);
                }
            }
        }

        if (isset($validated['content_translation']) && is_array($validated['content_translation'])) {
            foreach ($validated['content_translation'] as $locale => $value) {
                if (!empty($value)) {
                    $course->setTranslation('content_translation', $locale, $value);
                }
            }
        }

        $course->save();

        return redirect()->route('backend::courses.edit', $course)
            ->with('success', 'Course updated successfully');
    }

    /**
     * Remove the specified course from storage.
     *
     * @param  \Modules\Article\Models\Course  $course
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Course $course)
    {
        // Check if course has articles
        if ($course->articles->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete course with articles. Please delete the articles first.'
            ]);
        }

        // Delete the course
        $course->delete();

        return response()->json(['success' => true]);
    }
}
