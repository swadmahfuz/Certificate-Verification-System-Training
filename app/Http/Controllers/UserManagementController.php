<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\User;
use App\Services\ActivityLogService;
use App\Services\PermissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class UserManagementController extends Controller
{
    public function __construct(
        private ActivityLogService $activityLog,
        private PermissionService $permissions
    ) {
    }

    public function index()
    {
        $users = User::with('departmentRelation')
            ->withCount([
                'certificatesCreated',
                'certificatesReviewed',
                'certificatesApproved',
            ])
            ->orderBy('name')
            ->get();

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $departments = Department::active()->orderBy('name')->get();
        $apps = config('cvs.apps', []);

        return view('admin.users.create', compact('departments', 'apps'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'department_id' => 'required|exists:departments,id',
            'designation' => 'required|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'nullable|in:view,full',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'department_id' => $validated['department_id'],
            'designation' => $validated['designation'],
            'password' => Hash::make(Str::random(64)),
            'is_super_admin' => false,
            'is_active' => true,
            'password_must_change' => true,
        ]);

        $this->permissions->syncPermissions($user, $request->input('permissions', []));
        $user->sendEmailVerificationNotification();

        $this->activityLog->record(
            'user.created',
            'user',
            $user->id,
            'User "' . $user->name . '" was created by ' . Auth::user()->name . '.'
        );

        return redirect()->route('admin.users.index')
            ->with('success', 'User created. A verification email was sent.');
    }

    public function edit(User $user)
    {
        $departments = Department::active()->orderBy('name')->get();
        $apps = config('cvs.apps', []);
        $current = $user->appPermissions()->pluck('access_level', 'app_key')->all();

        return view('admin.users.edit', compact('user', 'departments', 'apps', 'current'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'department_id' => 'required|exists:departments,id',
            'designation' => 'required|string|max:255',
            'password' => 'nullable|string|min:8|confirmed',
            'permissions' => 'nullable|array',
            'permissions.*' => 'nullable|in:view,full',
            'is_super_admin' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $wantsActive = $request->boolean('is_active');
        $wantsSuperAdmin = $request->boolean('is_super_admin');
        $editingOtherSuperAdmin = $user->isSuperAdmin() && $user->id !== Auth::id();

        if (!$wantsActive && $user->id === Auth::id()) {
            return back()->with('error', 'You cannot deactivate your own account.');
        }

        if (!$wantsActive && $this->wouldRemoveLastActiveSuperAdmin($user)) {
            return back()->with('error', 'Cannot deactivate the last active Super Admin.');
        }

        if (!$wantsSuperAdmin && $user->isSuperAdmin() && $this->superAdminCount() <= 1) {
            return back()->with('error', 'Cannot remove the last Super Admin.');
        }

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'department_id' => $validated['department_id'],
            'designation' => $validated['designation'],
            'is_active' => $wantsActive,
        ]);

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
            $user->password_must_change = false;
        }

        if (!$editingOtherSuperAdmin) {
            if ($user->id === Auth::id() || !$user->isSuperAdmin()) {
                $user->is_super_admin = $wantsSuperAdmin;
            }
        }

        $user->save();

        if (!$editingOtherSuperAdmin && !$user->is_super_admin) {
            $this->permissions->syncPermissions($user, $validated['permissions'] ?? []);
        }

        $this->activityLog->record(
            'user.updated',
            'user',
            $user->id,
            'User "' . $user->name . '" was updated by ' . Auth::user()->name . '.'
        );

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function sendPasswordReset(User $user)
    {
        $status = Password::sendResetLink(['email' => $user->email]);

        if ($status !== Password::RESET_LINK_SENT) {
            return back()->with('error', __($status));
        }

        $this->activityLog->record(
            'user.password_reset_sent',
            'user',
            $user->id,
            'Password reset email sent to "' . $user->name . '" by ' . Auth::user()->name . '.'
        );

        return back()->with('success', 'Password reset email sent.');
    }

    public function editPermissions(User $user)
    {
        return redirect()->route('admin.users.edit', $user);
    }

    public function updatePermissions(Request $request, User $user)
    {
        return redirect()->route('admin.users.edit', $user);
    }

    private function superAdminCount(): int
    {
        return User::where('is_super_admin', true)->count();
    }

    private function wouldRemoveLastActiveSuperAdmin(User $user): bool
    {
        if (!$user->isSuperAdmin() || !$user->isActive()) {
            return false;
        }

        return User::where('is_super_admin', true)
            ->where('is_active', true)
            ->where('id', '!=', $user->id)
            ->count() === 0;
    }
}
