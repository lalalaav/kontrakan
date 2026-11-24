@extends('layouts.app')

@section('title', 'Masuk')

@section('content')
<div class="mx-auto mt-16 max-w-md rounded bg-white p-6 shadow">
    <h1 class="mb-4 text-2xl font-bold">Masuk</h1>
    <form method="POST" action="{{ route('login.store') }}" class="space-y-4">
        @csrf
        <div>
            <label class="mb-1 block text-sm">Email atau nomor telepon</label>
            <input name="login" value="{{ old('login') }}" required autofocus
                   class="w-full rounded border px-3 py-2">
        </div>
        <div>
            <label class="mb-1 block text-sm">Kata sandi</label>
            <input type="password" name="password" required class="w-full rounded border px-3 py-2">
        </div>
        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="remember" value="1"> Ingat saya
        </label>
        <button class="w-full rounded bg-slate-900 py-2 text-white">Masuk</button>
    </form>
</div>
@endsection
