<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\Student\CourseController;
use App\Http\Controllers\Teacher\CourseStudentController;
use App\Http\Controllers\Student\CourseEnrollmentController;
use App\Http\Controllers\Teacher\AssignmentController;
use App\Http\Controllers\Teacher\AssignmentSubmissionController;
use App\Http\Controllers\Student\AssignmentController as StudentAssignmentController;
use App\Http\Controllers\Student\ResourceController as StudentResourceController;
use App\Http\Controllers\Teacher\ResourceController as TeacherResourceController;

use App\Http\Controllers\ImpersonationController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])
    ->name('auth.google');

Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])
    ->name('auth.google.callback');

Route::post('/logout', function (Request $request) {
    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login');
})
    ->middleware('auth')
    ->name('logout');


/*
|--------------------------------------------------------------------------
| Dashboard Router
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function () {
    $user = auth()->user();

    return match ($user->role) {
        'admin' => redirect('/admin'),
        'teacher' => redirect('/teacher'),
        'student' => redirect('/student'),

        default => abort(
            403,
            'Your account does not have a valid role.'
        ),
    };
})
    ->middleware('auth')
    ->name('dashboard');


/*
|--------------------------------------------------------------------------
| Student Area
|--------------------------------------------------------------------------
*/

// Route::middleware(['auth', 'role:student'])->group(function () {

//     Route::get('/student', [StudentController::class, 'dashboard'])
//         ->name('student.dashboard');

// });

Route::middleware(['auth', 'role:student'])
    ->prefix('student')
    ->name('student.')
    ->group(function () {
        
        Route::get('/', [StudentController::class, 'dashboard'])
            ->name('dashboard');

        Route::get(
            '/courses/join',
            [CourseEnrollmentController::class, 'create']
        )->name('courses.join');

        Route::post(
            '/courses/join',
            [CourseEnrollmentController::class, 'store']
        )->name('courses.join.store');

        Route::get('/courses', [CourseController::class, 'index'])
            ->name('courses.index');

        Route::get('/courses/{course}', [CourseController::class, 'show'])
            ->name('courses.show');

        Route::get('/resources/{resource}', [StudentResourceController::class, 'show'])
            ->name('resources.show');

        /*
        |--------------------------------------------------------------------------
        | Assignments
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/assignments',
            [StudentAssignmentController::class, 'index']
        )->name('assignments.index');

        Route::get(
            '/assignments/{assignment}',
            [StudentAssignmentController::class, 'show']
        )->name('assignments.show');

        Route::post( '/assignments/{assignment}/draft', [StudentAssignmentController::class, 'saveDraft'])
            ->name('assignments.draft'); 
            
        Route::post( '/assignments/{assignment}/submit', [StudentAssignmentController::class, 'submit'] )
            ->name('assignments.submit');

        Route::delete( '/assignment-submission-files/{file}', [StudentAssignmentController::class, 'removeFile'] )
            ->name('assignments.files.remove');

        Route::get( '/assignments/submissions/files/{file}/download', 
            [\App\Http\Controllers\Student\AssignmentController::class, 'downloadFile'])
            ->name('assignments.files.download');

        
        Route::post( '/assignments/{assignment}/resubmit',
           [\App\Http\Controllers\Student\AssignmentController::class, 'resubmit']
           )->name('assignments.resubmit');

        Route::get(
            '/assignments/{assignment}/attachments/{attachment}',
            [\App\Http\Controllers\Student\AssignmentController::class, 'downloadAttachment']
        )->name('assignments.attachments.download');


        
    });

/*
|--------------------------------------------------------------------------
| Teacher Area
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:teacher'])
    ->prefix('teacher')
    ->name('teacher.')
    ->group(function () {

        Route::get('/', [TeacherController::class, 'dashboard'])
            ->name('dashboard');

        Route::get('/courses', [\App\Http\Controllers\Teacher\CourseController::class, 'index'])
            ->name('courses.index');

        Route::get('/courses/create', [\App\Http\Controllers\Teacher\CourseController::class, 'create'])
            ->name('courses.create');

        Route::post('/courses', [\App\Http\Controllers\Teacher\CourseController::class, 'store'])
            ->name('courses.store');

        Route::get('/courses/{course}', [\App\Http\Controllers\Teacher\CourseController::class, 'show'])
            ->name('courses.show');

        Route::get('/courses/{course}/edit', [\App\Http\Controllers\Teacher\CourseController::class, 'edit'])
            ->name('courses.edit');

        Route::put('/courses/{course}', [\App\Http\Controllers\Teacher\CourseController::class, 'update'])
            ->name('courses.update');

        Route::delete('/courses/{course}', [\App\Http\Controllers\Teacher\CourseController::class, 'destroy'])
            ->name('courses.destroy');

        

         /*
        |--------------------------------------------------------------------------
        | Resources
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/courses/{course}/resources',
            [TeacherResourceController::class, 'index']
        )->name('courses.resources.index');

        Route::get(
            '/courses/{course}/resources/create',
            [TeacherResourceController::class, 'create']
        )->name('courses.resources.create');

        Route::post(
            '/courses/{course}/resources',
            [TeacherResourceController::class, 'store']
        )->name('courses.resources.store');

        Route::get(
            '/courses/{course}/resources/{resource}',
            [TeacherResourceController::class, 'show']
        )->name('courses.resources.show');

        Route::get(
            '/courses/{course}/resources/{resource}/edit',
            [TeacherResourceController::class, 'edit']
        )->name('courses.resources.edit');

        Route::put(
            '/courses/{course}/resources/{resource}',
            [TeacherResourceController::class, 'update']
        )->name('courses.resources.update');

        Route::delete(
            '/courses/{course}/resources/{resource}',
            [TeacherResourceController::class, 'destroy']
        )->name('courses.resources.destroy');

         

        Route::get(
            '/courses/{course}/students/pending',
            [CourseStudentController::class, 'pending']
        )->name('courses.students.pending');

        Route::post(
            '/courses/{course}/students/{enrollment}/approve',
            [CourseStudentController::class, 'approve']
        )->name('courses.students.approve');

        Route::post(
            '/courses/{course}/students/{enrollment}/reject',
            [CourseStudentController::class, 'reject']
        )->name('courses.students.reject');


        /*
        |--------------------------------------------------------------------------
        | Editor Image Upload
        |--------------------------------------------------------------------------
        */

        Route::post(
            '/courses/{course}/resources/images',
            [TeacherResourceController::class, 'uploadImage']
        )->name('courses.resources.images.store');
        

        /*
        |--------------------------------------------------------------------------
        | Course Students / Enrollment
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/courses/{course}/students',
            [CourseStudentController::class, 'index']
        )->name('courses.students.index');

        Route::get(
            '/courses/{course}/students/create',
            [CourseStudentController::class, 'create']
        )->name('courses.students.create');

        Route::post(
            '/courses/{course}/students',
            [CourseStudentController::class, 'store']
        )->name('courses.students.store');

        Route::delete(
            '/courses/{course}/students/{student}',
            [CourseStudentController::class, 'destroy']
        )->name('courses.students.destroy');

        

        /*
         * Assignment management
         */
        Route::get(
            '/courses/{course}/assignments',
            [AssignmentController::class, 'index']
        )->name('courses.assignments.index');

        Route::get(
            '/courses/{course}/assignments/create',
            [AssignmentController::class, 'create']
        )->name('courses.assignments.create');

        Route::post(
            '/courses/{course}/assignments',
            [AssignmentController::class, 'store']
        )->name('courses.assignments.store');

        Route::get(
            '/courses/{course}/assignments/{assignment}',
            [AssignmentController::class, 'show']
        )->name('courses.assignments.show');

        Route::get(
            '/courses/{course}/assignments/{assignment}/edit',
            [AssignmentController::class, 'edit']
        )->name('courses.assignments.edit');

        Route::put(
            '/courses/{course}/assignments/{assignment}',
            [AssignmentController::class, 'update']
        )->name('courses.assignments.update');

        Route::delete(
            '/courses/{course}/assignments/{assignment}',
            [AssignmentController::class, 'destroy']
        )->name('courses.assignments.destroy');
        
        
        
        /*
        |--------------------------------------------------------------------------
        | Assignment Submission Review & Grading
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/courses/{course}/assignments/{assignment}/submissions/{submission}',
            [\App\Http\Controllers\Teacher\AssignmentSubmissionController::class, 'review']
        )->name('courses.assignments.submissions.review');

        Route::put(
            '/courses/{course}/assignments/{assignment}/submissions/{submission}/grade',
            [\App\Http\Controllers\Teacher\AssignmentSubmissionController::class, 'grade']
        )->name('courses.assignments.submissions.grade');

        Route::get(
            '/courses/{course}/assignments/{assignment}/submissions/files/{file}/download',
            [AssignmentController::class, 'downloadSubmissionFile']
        )->name('courses.assignments.submissions.files.download');

        Route::get(
            '/courses/{course}/assignments/{assignment}/submissions/files/{file}/download',
            [\App\Http\Controllers\Teacher\AssignmentSubmissionController::class, 'downloadSubmissionFile']
        )->name('courses.assignments.submissions.files.download');
    });






Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get(
            '/impersonate/{user}',
            [ImpersonationController::class, 'start']
        )->name('impersonate.start');

        });

Route::middleware('auth')
        ->post(
        '/impersonation/stop',
        [ImpersonationController::class, 'stop']
        )
        ->name('impersonation.stop');
        











