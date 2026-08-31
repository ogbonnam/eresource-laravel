<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class ImpersonationController extends Controller
{
    /**
     * Start impersonating a user.
     */
    public function start(User $user): RedirectResponse
    {
        /*
        |--------------------------------------------------------------------------
        | Only authenticated administrators can impersonate.
        |--------------------------------------------------------------------------
        */

        $admin = Auth::user();

        abort_unless(
            $admin && $admin->role === 'admin',
            403
        );


        /*
        |--------------------------------------------------------------------------
        | Prevent administrators from impersonating another administrator.
        |--------------------------------------------------------------------------
        */

        abort_if(
            $user->role === 'admin',
            403,
            'Administrators cannot impersonate another administrator.'
        );


        /*
        |--------------------------------------------------------------------------
        | Prevent impersonation of the currently authenticated user.
        |--------------------------------------------------------------------------
        */

        abort_if(
            $admin->id === $user->id,
            403
        );


        /*
        |--------------------------------------------------------------------------
        | Store the original administrator.
        |--------------------------------------------------------------------------
        |
        | We only store the administrator's ID.
        |
        | We do NOT modify the administrator's password or database record.
        |
        */

        session([
            'impersonating' => true,
            'impersonator_id' => $admin->id,
        ]);


        /*
        |--------------------------------------------------------------------------
        | Log in as the selected user.
        |--------------------------------------------------------------------------
        */

        Auth::login($user);


        /*
        |--------------------------------------------------------------------------
        | Regenerate the session ID.
        |--------------------------------------------------------------------------
        |
        | This prevents session fixation when switching identities.
        |
        */

        request()->session()->regenerate();


        /*
        |--------------------------------------------------------------------------
        | Redirect based on the impersonated user's role.
        |--------------------------------------------------------------------------
        */

        return match ($user->role) {

            'teacher' => redirect()
                ->route('teacher.dashboard')
                ->with(
                    'success',
                    "You are now impersonating {$user->name}."
                ),

            'student' => redirect()
                ->route('student.dashboard')
                ->with(
                    'success',
                    "You are now impersonating {$user->name}."
                ),

            default => redirect('/')
                ->with(
                    'success',
                    "You are now impersonating {$user->name}."
                ),
        };
    }


    /**
     * Stop impersonating and return to the original administrator.
     */
    public function stop(): RedirectResponse
    {
        /*
        |--------------------------------------------------------------------------
        | Make sure an impersonation session exists.
        |--------------------------------------------------------------------------
        */

        abort_unless(
            session()->has('impersonator_id'),
            403,
            'You are not currently impersonating another user.'
        );


        /*
        |--------------------------------------------------------------------------
        | Retrieve the original administrator.
        |--------------------------------------------------------------------------
        */

        $adminId = session('impersonator_id');

        $admin = User::query()
            ->where('id', $adminId)
            ->where('role', 'admin')
            ->firstOrFail();


        /*
        |--------------------------------------------------------------------------
        | Log back in as the administrator.
        |--------------------------------------------------------------------------
        */

        Auth::login($admin);


        /*
        |--------------------------------------------------------------------------
        | Remove impersonation information.
        |--------------------------------------------------------------------------
        */

        session()->forget([
            'impersonating',
            'impersonator_id',
        ]);


        /*
        |--------------------------------------------------------------------------
        | Regenerate the session.
        |--------------------------------------------------------------------------
        */

        request()->session()->regenerate();


        /*
        |--------------------------------------------------------------------------
        | Return to Filament administration.
        |--------------------------------------------------------------------------
        */

        return redirect('/admin')
            ->with(
                'success',
                'You have returned to your administrator account.'
            );
    }
}
