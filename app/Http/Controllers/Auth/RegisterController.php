<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use App\Services\PermissionService;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    use RegistersUsers;

    public function __construct(private PermissionService $permissions)
    {
        $this->middleware('guest');
    }

    public function showRegistrationForm()
    {
        $departments = Department::active()->orderBy('name')->get();

        return view('auth.register', compact('departments'));
    }

    protected $redirectTo = RouteServiceProvider::HOME;

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'department_id' => ['required', 'exists:departments,id'],
            'designation' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    protected function create(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'department_id' => $data['department_id'],
            'designation' => $data['designation'],
            'password' => Hash::make($data['password']),
            'is_active' => true,
            'password_must_change' => false,
        ]);

        $this->permissions->grantDefaultRegistrationAccess($user);
        $user->sendEmailVerificationNotification();

        return $user;
    }

    protected function registered(Request $request, $user)
    {
        return redirect()->route('verification.notice');
    }
}
