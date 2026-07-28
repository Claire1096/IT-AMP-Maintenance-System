<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    private array $roles = ['it', 'executive', 'facility_manager'];
    private array $employmentTypes = ['intern', 'employee'];

    public function create()
    {
        abort_unless(auth()->user()->is_admin, 403);

        return view('users.create', [
            'roles' => $this->roles,
            'employmentTypes' => $this->employmentTypes,
        ]);
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->is_admin, 403);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|in:' . implode(',', $this->roles),
            'employment_type' => 'required|in:' . implode(',', $this->employmentTypes),
            'grant_admin' => 'nullable|boolean',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role' => $validated['role'],
            'employment_type' => $validated['employment_type'],
            'is_admin' => $request->boolean('grant_admin'),
        ]);

        return redirect()->route('profile.edit')->with('success', 'User account created.');
    }
}