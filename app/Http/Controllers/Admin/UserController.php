<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.users.index', [
            'users' => User::with('role')
                ->when($request->filled('search'), function ($query) use ($request): void {
                    $search = '%'.$request->string('search').'%';
                    $query->where(fn ($q) => $q
                        ->where('first_name', 'like', $search)
                        ->orWhere('last_name', 'like', $search)
                        ->orWhere('email', 'like', $search)
                        ->orWhere('username', 'like', $search));
                })
                ->orderBy('id')
                ->paginate(20)
                ->withQueryString(),
        ]);
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', [
            'user' => $user,
            'roles' => Role::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['required', 'string', 'max:30'],
            'role_id' => ['required', Rule::exists('roles', 'id')],
        ]);

        // Admins cannot demote or edit away their own admin role by accident.
        if ($user->id === $request->user()->id && (int) $validated['role_id'] !== $user->role_id) {
            return back()->withErrors(['role_id' => __('You cannot change your own role.')]);
        }

        // role_id and is_blocked are deliberately not mass-assignable.
        $user->forceFill(['role_id' => (int) $validated['role_id']])
            ->fill(collect($validated)->except('role_id')->all())
            ->save();

        return redirect()->route('admin.users.index')->with('status', __('User updated.'));
    }

    public function toggleBlock(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return back()->withErrors(['user' => __('You cannot block yourself.')]);
        }

        $user->forceFill(['is_blocked' => ! $user->is_blocked])->save();

        return back()->with('status', $user->is_blocked ? __('User blocked.') : __('User unblocked.'));
    }
}
