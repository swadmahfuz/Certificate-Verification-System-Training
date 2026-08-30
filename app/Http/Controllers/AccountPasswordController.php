<?php

namespace App\Http\Controllers;

use App\Services\ActivityLogService;
use App\Support\PasswordRules;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AccountPasswordController extends Controller
{
    public function __construct(private ActivityLogService $activityLog)
    {
    }

    public function edit()
    {
        return view('account.password');
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'password' => PasswordRules::requiredConfirmed(),
        ]);

        if ($user->mustChangePassword()) {
            // Admin-created users set password via this form without knowing the old one.
        } else {
            $request->validate([
                'current_password' => 'required|string',
            ]);

            if (!Hash::check($request->input('current_password'), $user->password)) {
                return back()->withErrors(['current_password' => 'The current password is incorrect.']);
            }
        }

        $user->password = Hash::make($validated['password']);
        $user->password_must_change = false;
        $user->save();

        $this->activityLog->record(
            'user.password_changed',
            'user',
            $user->id,
            'User "' . $user->name . '" changed their password.'
        );

        return redirect()->route('dashboard')->with('success', 'Password updated successfully.');
    }
}
