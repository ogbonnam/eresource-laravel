<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Resource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ResourceController extends Controller
{
    /**
     * Display resource creation form.
     */
    public function create(
        Request $request,
        Course $course
    ): View {
        $this->authorizeCourse($request, $course);

        return view('teacher.resources.create', [
            'course' => $course,
        ]);
    }


    /**
     * Store a new resource.
     */
    public function store(
        Request $request,
        Course $course
    ): RedirectResponse {
        $this->authorizeCourse($request, $course);


        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'content' => [
                'nullable',
                'string',
            ],

            'type' => [
                'required',
                'string',
                'in:lesson_note,assignment,pdf,document,video,zip',
            ],

            'file' => [
                'nullable',
                'file',
                'max:307200',
            ],

            'url' => [
                'nullable',
                'url',
                'max:2000',
            ],

            'thumbnail' => [
                'nullable',
                'image',
                'max:5120',
            ],

            'is_published' => [
                'nullable',
                'boolean',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Lesson Note / Assignment
        |--------------------------------------------------------------------------
        */

        if (
            in_array(
                $validated['type'],
                ['lesson_note', 'assignment'],
                true
            )
        ) {

            if (
                empty(trim(strip_tags(
                    $validated['content'] ?? ''
                )))
            ) {

                return back()
                    ->withErrors([
                        'content' =>
                            'Please enter some content.',
                    ])
                    ->withInput();
            }
        }


        /*
        |--------------------------------------------------------------------------
        | File-based resources
        |--------------------------------------------------------------------------
        */

        $filePath = null;

        if ($request->hasFile('file')) {

            $filePath = $request
                ->file('file')
                ->store(
                    'resources/' . $course->id,
                    'public'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Thumbnail
        |--------------------------------------------------------------------------
        */

        $thumbnailPath = null;

        if ($request->hasFile('thumbnail')) {

            $thumbnailPath = $request
                ->file('thumbnail')
                ->store(
                    'resources/thumbnails/' . $course->id,
                    'public'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Sort order
        |--------------------------------------------------------------------------
        */

        $sortOrder =
            ((int) $course->resources()->max('sort_order')) + 1;


        /*
        |--------------------------------------------------------------------------
        | Create resource
        |--------------------------------------------------------------------------
        */

        $resource = $course->resources()->create([
            'title' => $validated['title'],

            'description' =>
                $validated['description'] ?? null,

            'content' =>
                $validated['content'] ?? null,

            'type' =>
                $validated['type'],

            'file_path' =>
                $filePath,

            'url' =>
                $validated['url'] ?? null,

            'thumbnail' =>
                $thumbnailPath,

            'is_published' =>
                $request->boolean('is_published'),

            'sort_order' =>
                $sortOrder,

            'published_at' =>
                $request->boolean('is_published')
                    ? now()
                    : null,
        ]);


        return redirect()
            ->route(
                'teacher.courses.show',
                $course
            )
            ->with(
                'success',
                'Resource created successfully.'
            );
    }


    /**
     * Upload an image from the WYSIWYG editor.
     */
    public function uploadImage(
        Request $request
    ): JsonResponse {

        /*
        |--------------------------------------------------------------------------
        | Only teachers can use the editor upload endpoint.
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $request->user()?->isTeacher(),
            403
        );


        $validated = $request->validate([
            'image' => [
                'required',
                'image',
                'mimes:jpeg,jpg,png,gif,webp',
                'max:5120',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Store image
        |--------------------------------------------------------------------------
        */

        $path = $request
            ->file('image')
            ->store(
                'resources/editor',
                'public'
            );


        /*
        |--------------------------------------------------------------------------
        | Return public URL
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'url' => Storage::disk('public')
                ->url($path),

            'path' => $path,
        ]);
    }


    /**
     * Show a resource.
     */
    public function show(
        Request $request,
        Course $course,
        Resource $resource
    ): View {

        $this->authorizeCourse(
            $request,
            $course
        );

        $this->ensureResourceBelongsToCourse(
            $course,
            $resource
        );

        return view('teacher.resources.show', [
            'course' => $course,
            'resource' => $resource,
        ]);
    }


    /**
     * Show edit form.
     */
    public function edit(
        Request $request,
        Course $course,
        Resource $resource
    ): View {

        $this->authorizeCourse(
            $request,
            $course
        );

        $this->ensureResourceBelongsToCourse(
            $course,
            $resource
        );

        return view('teacher.resources.edit', [
            'course' => $course,
            'resource' => $resource,
        ]);
    }


    /**
     * Update resource.
     */
    public function update(
        Request $request,
        Course $course,
        Resource $resource
    ): RedirectResponse {

        $this->authorizeCourse(
            $request,
            $course
        );

        $this->ensureResourceBelongsToCourse(
            $course,
            $resource
        );


        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'content' => [
                'nullable',
                'string',
            ],

            'type' => [
                'required',
                'string',
                'in:lesson_note,assignment,pdf,document,video,zip',
            ],

            'file' => [
                'nullable',
                'file',
                'max:307200',
            ],

            'url' => [
                'nullable',
                'url',
                'max:2000',
            ],

            'thumbnail' => [
                'nullable',
                'image',
                'max:5120',
            ],

            'is_published' => [
                'nullable',
                'boolean',
            ],
        ]);


        $wasPublished =
            $resource->is_published;


        /*
        |--------------------------------------------------------------------------
        | Replace file
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('file')) {

            if ($resource->file_path) {

                Storage::disk('public')->delete(
                    $resource->file_path
                );
            }


            $resource->file_path =
                $request
                    ->file('file')
                    ->store(
                        'resources/' . $course->id,
                        'public'
                    );
        }


        /*
        |--------------------------------------------------------------------------
        | Replace thumbnail
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('thumbnail')) {

            if ($resource->thumbnail) {

                Storage::disk('public')->delete(
                    $resource->thumbnail
                );
            }


            $resource->thumbnail =
                $request
                    ->file('thumbnail')
                    ->store(
                        'resources/thumbnails/' .
                        $course->id,
                        'public'
                    );
        }


        /*
        |--------------------------------------------------------------------------
        | Update fields
        |--------------------------------------------------------------------------
        */

        $resource->title =
            $validated['title'];

        $resource->description =
            $validated['description'] ?? null;

        $resource->content =
            $validated['content'] ?? null;

        $resource->type =
            $validated['type'];

        $resource->url =
            $validated['url'] ?? null;

        $resource->is_published =
            $request->boolean('is_published');


        /*
        |--------------------------------------------------------------------------
        | Publication date
        |--------------------------------------------------------------------------
        */

        if (
            !$wasPublished &&
            $resource->is_published
        ) {

            $resource->published_at = now();
        }


        if (!$resource->is_published) {

            $resource->published_at = null;
        }


        $resource->save();


        return redirect()
            ->route(
                'teacher.courses.show',
                $course
            )
            ->with(
                'success',
                'Resource updated successfully.'
            );
    }


    /**
     * Delete resource.
     */
    public function destroy(
        Request $request,
        Course $course,
        Resource $resource
    ): RedirectResponse {

        $this->authorizeCourse(
            $request,
            $course
        );

        $this->ensureResourceBelongsToCourse(
            $course,
            $resource
        );


        if ($resource->file_path) {

            Storage::disk('public')->delete(
                $resource->file_path
            );
        }


        if ($resource->thumbnail) {

            Storage::disk('public')->delete(
                $resource->thumbnail
            );
        }


        $resource->delete();


        return redirect()
            ->route(
                'teacher.courses.show',
                $course
            )
            ->with(
                'success',
                'Resource deleted successfully.'
            );
    }


    /**
     * Verify teacher owns course.
     */
    protected function authorizeCourse(
        Request $request,
        Course $course
    ): void {

        abort_unless(
            $request->user()->isTeacher()
            &&
            $course->teacher_id ===
                $request->user()->id,
            403,
            'You are not authorized to manage this course.'
        );
    }


    /**
     * Verify resource belongs to course.
     */
    protected function ensureResourceBelongsToCourse(
        Course $course,
        Resource $resource
    ): void {

        abort_unless(
            $resource->course_id ===
                $course->id,
            404
        );
    }
}