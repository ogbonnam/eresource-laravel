<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class GoogleAuthController extends Controller
{
    /**
     * Redirect the user to Google's OAuth consent screen.
     */
    public function redirect(Request $request): RedirectResponse
    {
        $state = Str::random(40);

        // Store OAuth state in the Laravel session.
        // This protects against CSRF attacks.
        $request->session()->put('google_oauth_state', $state);

        $query = http_build_query([
            'client_id' => config('services.google.client_id'),
            'redirect_uri' => config('services.google.redirect'),
            'response_type' => 'code',
            'scope' => 'openid email profile',
            'state' => $state,
            'access_type' => 'online',
            'prompt' => 'select_account',
        ]);

        return redirect()->away(
            'https://accounts.google.com/o/oauth2/v2/auth?' . $query
        );
    }

    /**
     * Handle Google's OAuth callback.
     */
    public function callback(Request $request): RedirectResponse
    {
        /*
        |--------------------------------------------------------------------------
        | 1. Validate OAuth state
        |--------------------------------------------------------------------------
        */

        $expectedState = $request->session()->pull('google_oauth_state');

        if (
            !$expectedState ||
            !hash_equals(
                $expectedState,
                (string) $request->input('state')
            )
        ) {
            abort(419, 'Invalid OAuth state.');
        }

        /*
        |--------------------------------------------------------------------------
        | 2. Validate authorization code
        |--------------------------------------------------------------------------
        */

        $code = $request->input('code');

        if (!$code) {
            abort(400, 'Google authorization code is missing.');
        }

        /*
        |--------------------------------------------------------------------------
        | 3. Exchange authorization code for Google access token
        |--------------------------------------------------------------------------
        */

        $tokenResponse = Http::asForm()->post(
            'https://oauth2.googleapis.com/token',
            [
                'code' => $code,
                'client_id' => config('services.google.client_id'),
                'client_secret' => config('services.google.client_secret'),
                'redirect_uri' => config('services.google.redirect'),
                'grant_type' => 'authorization_code',
            ]
        );

        if ($tokenResponse->failed()) {
            throw new RuntimeException(
                'Unable to exchange Google authorization code.'
            );
        }

        $accessToken = $tokenResponse->json('access_token');

        if (!$accessToken) {
            throw new RuntimeException(
                'Google did not return an access token.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 4. Retrieve Google account information
        |--------------------------------------------------------------------------
        */

        $googleResponse = Http::withToken($accessToken)
            ->get('https://www.googleapis.com/oauth2/v3/userinfo');

        if ($googleResponse->failed()) {
            throw new RuntimeException(
                'Unable to retrieve Google account information.'
            );
        }

        $googleUser = $googleResponse->json();

        $googleId = $googleUser['sub'] ?? null;
        $email = $googleUser['email'] ?? null;
        $name = $googleUser['name'] ?? null;
        $avatar = $googleUser['picture'] ?? null;
        $emailVerified = $googleUser['email_verified'] ?? false;

        /*
        |--------------------------------------------------------------------------
        | 5. Validate Google account information
        |--------------------------------------------------------------------------
        */

        if (!$googleId || !$email) {
            throw new RuntimeException(
                'Google account information is incomplete.'
            );
        }

        if (!$emailVerified) {
            abort(
                403,
                'Your Google email address must be verified.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 6. Find existing Google account
        |--------------------------------------------------------------------------
        */

        $socialAccount = SocialAccount::where('provider', 'google')
            ->where('provider_id', $googleId)
            ->first();

        if ($socialAccount) {

            /*
            |--------------------------------------------------------------------------
            | Existing Google account
            |--------------------------------------------------------------------------
            */

            $user = $socialAccount->user;

            if (!$user) {
                throw new RuntimeException(
                    'The Google account is not associated with a valid user.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Update Google account information
            |--------------------------------------------------------------------------
            */

            $socialAccount->update([
                'provider_email' => $email,
                'avatar' => $avatar,
            ]);

            /*
            |--------------------------------------------------------------------------
            | Keep the user's verified email status current
            |--------------------------------------------------------------------------
            */

            if (!$user->email_verified_at) {
                $user->update([
                    'email_verified_at' => now(),
                ]);
            }
        } else {

            /*
            |--------------------------------------------------------------------------
            | 7. Google account does not exist
            |--------------------------------------------------------------------------
            |
            | First check whether the email already belongs to an eResource
            | user. This prevents creating duplicate users.
            |
            */

            $user = User::where('email', $email)->first();

            if ($user) {

                /*
                |--------------------------------------------------------------------------
                | Existing eResource user
                |--------------------------------------------------------------------------
                |
                | IMPORTANT:
                | We do NOT change the user's role.
                |
                | If the administrator previously made this user a teacher,
                | they remain a teacher.
                |
                */

                if (!$user->email_verified_at) {
                    $user->update([
                        'email_verified_at' => now(),
                    ]);
                }

                $socialAccount = $user->socialAccounts()->create([
                    'provider' => 'google',
                    'provider_id' => $googleId,
                    'provider_email' => $email,
                    'avatar' => $avatar,
                ]);
            } else {

                /*
                |--------------------------------------------------------------------------
                | 8. Completely new eResource user
                |--------------------------------------------------------------------------
                |
                | A new Google user is ALWAYS created as a student.
                |
                | Google authentication proves who the person is.
                | It does NOT determine their school role.
                |
                */

                $user = User::create([
                    'name' => $name ?: $email,
                    'email' => $email,
                    'password' => Str::random(40),
                    'role' => 'student',
                    'email_verified_at' => now(),
                ]);

                $socialAccount = $user->socialAccounts()->create([
                    'provider' => 'google',
                    'provider_id' => $googleId,
                    'provider_email' => $email,
                    'avatar' => $avatar,
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 9. Log the user into Laravel
        |--------------------------------------------------------------------------
        */

        Auth::login($user, true);

        /*
        |--------------------------------------------------------------------------
        | 10. Regenerate session after authentication
        |--------------------------------------------------------------------------
        |
        | This prevents session fixation attacks.
        |
        */

        $request->session()->regenerate();

        /*
        |--------------------------------------------------------------------------
        | 11. Send authenticated user to dashboard
        |--------------------------------------------------------------------------
        |
        | The dashboard decides where the user belongs based on role.
        |
        */

        return redirect()->route('dashboard');
    }
}