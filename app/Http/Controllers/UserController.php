<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query();

        if ($request->has('sort')) {
            $users->orderBy($request->get('sort'), $request->get('direction', 'asc'));
        }

        $users = $users->paginate(10);
        return view('pages.User.index', compact('users'));
    }

    public function show(User $user)
    {
        return view('pages.User.modal.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')
            ->with('success', 'User berhasil dihapus');
    }
}
