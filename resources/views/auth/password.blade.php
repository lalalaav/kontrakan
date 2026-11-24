@extends('layouts.app')

@section('title', 'Ubah Sandi')

@section('content')
<h1 class="mb-4 text-2xl font-bold">Ubah Kata Sandi</h1>
<form method="POST" action="{{ route('password.update') }}" class="max-w-md space-y-3 rounded bg-white p-5">
    @csrf
    @method('PUT')
    <div>
        <label class="mb-1 block text-sm">Kata sandi saat ini</label>
        <input type="password" name="current_password" required class="w-full rounded border px-3 py-2">
    </div>
    <div>
        <label class="mb-1 block text-sm">Kata sandi baru</label>
        <input type="password" name="password" required class="w-full rounded border px-3 py-2">
    </div>
    <div>
        <label class="mb-1 block text-sm">Ulangi kata sandi baru</label>
        <input type="password" name="password_confirmation" required class="w-full rounded border px-3 py-2">
    </div>
    <button class="rounded bg-slate-900 px-4 py-2 text-white">Simpan</button>
</form>
@endsection
