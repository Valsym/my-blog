<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    public function index()
    {
        $users = User::withCount('posts')
            ->latest()
            ->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($user->id),
            ],
            'is_admin' => 'boolean',
            'is_blocked' => 'boolean',
        ]);

        $user->update($validated);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Пользователь успешно обновлен');
    }

    public function destroy(User $user)
    {
        // Не позволяем удалить самого себя
        if ($user->id === Auth::id()) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'Вы не можете удалить свой собственный аккаунт');
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Пользователь успешно удален');
    }

    public function toggleBlock(User $user)
    {
        // Не позволяем заблокировать самого себя
        if ($user->id === Auth::id()) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'Вы не можете заблокировать свой собственный аккаунт');
        }

        $user->update([
            'is_blocked' => ! $user->is_blocked,
        ]);

        $status = $user->is_blocked ? 'заблокирован' : 'разблокирован';

        return redirect()
            ->route('admin.users.index')
            ->with('success', "Пользователь {$user->name} {$status}");
    }
}
