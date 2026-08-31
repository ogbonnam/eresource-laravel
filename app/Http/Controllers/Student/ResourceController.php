<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Resource;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ResourceController extends Controller
{
    /**
     * Display a published resource belonging to a course
     * the student is actively enrolled in.
     */
    public function show(Request $request, Resource $resource): View
    {
        $student = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Load course information
        |--------------------------------------------------------------------------
        */

        $resource->load([
            'course.subject',
            'course.schoolClass',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Resource must belong to a course
        |--------------------------------------------------------------------------
        */

        if (! $resource->course) {
            abort(404, 'Course not found.');
        }

        $course = $resource->course;

        /*
        |--------------------------------------------------------------------------
        | Student must be actively enrolled
        |--------------------------------------------------------------------------
        */

        $isEnrolled = $student
            ->activeCourses()
            ->where('courses.id', $course->id)
            ->exists();

        if (! $isEnrolled) {
            abort(403, 'You are not enrolled in this course.');
        }

        /*
        |--------------------------------------------------------------------------
        | Resource must be published
        |--------------------------------------------------------------------------
        */

        if (! $resource->is_published) {
            abort(404, 'This resource is not available.');
        }

        /*
        |--------------------------------------------------------------------------
        | Generate the public file URL
        |--------------------------------------------------------------------------
        */

        $fileUrl = null;

        if ($resource->file_path) {
            $fileUrl = \Illuminate\Support\Facades\Storage::disk('public')
                ->url($resource->file_path);
        }

        /*
        |--------------------------------------------------------------------------
        | File extension
        |--------------------------------------------------------------------------
        */

        $extension = null;

        if ($resource->file_path) {
            $extension = strtolower(
                pathinfo($resource->file_path, PATHINFO_EXTENSION)
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Return resource page
        |--------------------------------------------------------------------------
        */

        return view('student.resources.show', [
            'resource' => $resource,
            'course' => $course,
            'fileUrl' => $fileUrl,
            'extension' => $extension,
        ]);
    }
}