<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $users = User::query()
            ->when(request('q'), fn ($q, $v) => $q->where(
                fn ($w) => $w->where('name', 'like', "%$v%")->orWhere('email', 'like', "%$v%")
            ))
            ->when(request('role'), fn ($q, $v) => $q->where('role', $v))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('user.index', compact('users'));
    }

    public function create()
    {
        return view('user.form', ['user' => new User(['role' => 'penyewa', 'is_active' => true])]);
    }

    public function store(UserRequest $request)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');
        User::create($data);

        return redirect()->route('user.index')->with('success', 'Pengguna ditambahkan.');
    }

    public function edit(User $user)
    {
        return view('user.form', compact('user'));
    }

    public function update(UserRequest $request, User $user)
    {
        $data = $request->validated();

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        $data['is_active'] = $request->boolean('is_active');

        // Admin tidak boleh mengunci dirinya sendiri.
        if ($user->id === $request->user()->id) {
            $data['is_active'] = true;
            $data['role'] = $user->role;
        }

        $user->update($data);

        return redirect()->route('user.index')->with('success', 'Pengguna diperbarui.');
    }

    public function destroy(User $user)
    {
        if ($user->id === request()->user()->id) {
            return back()->withErrors(['user' => 'Tidak bisa menonaktifkan akun sendiri.']);
        }

        $user->update(['is_active' => false]);

        return back()->with('success', 'Akun dinonaktifkan.');
    }
}