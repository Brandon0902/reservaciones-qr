<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserStoreRequest;
use App\Http\Requests\Admin\UserUpdateRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::query()->latest()->paginate(15);
        return view('admin.users.index', compact('users'));
    }

    public function create(): View
    {
        $roles = UserRole::cases(); // admin | validator | customer
        return view('admin.users.create', compact('roles'));
    }

    public function store(UserStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        User::create($data);

        return redirect()->route('admin.users.index')->with('success', 'User created.');
    }

    public function edit(User $user): View
    {
        $roles = UserRole::cases();
        return view('admin.users.edit', compact('user','roles'));
    }

    public function update(UserUpdateRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'User updated.');
    }

    public function destroy(User $user): RedirectResponse
    {
        // Evitar que un admin se elimine a sí mismo accidentalmente (opcional)
        // if (auth()->id() === $user->id) return back()->with('warning','Cannot delete yourself.');

        $user->delete();
        return back()->with('success', 'User deleted.');
    }
}
