<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use App\Services\ActivityLogService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    use VerifiesEmails;

    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct(private ActivityLogService $activityLog)
    {
        $this->middleware('auth')->except('verify');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    public function verify(Request $request)
    {
        $user = User::findOrFail($request->route('id'));

        if (!hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
            throw new AuthorizationException;
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('login')->with('success', 'Email already verified. Please log in.');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));

            $this->activityLog->record(
                'user.email_verified',
                'user',
                $user->id,
                'User "' . $user->name . '" verified their email address.'
            );
        }

        $message = $user->mustChangePassword()
            ? 'Email verified. Check your email for a link to set your password.'
            : 'Email verified successfully. Please log in.';

        return redirect()->route('login')->with('success', $message);
    }

    protected function verified(Request $request)
    {
        return redirect($this->redirectPath())->with('verified', true);
    }
}
