@extends('layouts.app')
@section('title', 'Pengguna')
@section('content')
<div class="mb-4 flex items-center justify-between">
    <h1 class="text-2xl font-bold">Pengguna</h1>
    <a href="{{ route('user.create') }}" class="rounded bg-slate-900 px-3 py-2 text-white">Tambah</a>
</div>
<form class="mb-4 flex flex-wrap gap-2">
    <input name="q" value="{{ request('q') }}" placeholder="Nama atau email" class="rounded border px-3 py-2">
    <select name="role" class="rounded border px-3 py-2">
        <option value="">Semua peran</option>
        @foreach (['admin','petugas','penyewa'] as $r)
            <option value="{{ $r }}" @selected(request('role') === $r)>{{ ucfirst($r) }}</option>
        @endforeach
    </select>
    <button class="rounded bg-slate-700 px-4 py-2 text-white">Filter</button>
</form>
<div class="overflow-hidden rounded bg-white shadow">
<table class="w-full text-left text-sm">
    <thead class="bg-slate-100">
        <tr><th class="p-3">Nama</th><th class="p-3">Email</th><th class="p-3">Telepon</th>
            <th class="p-3">Peran</th><th class="p-3">Status</th><th class="p-3">Aksi</th></tr>
    </thead>
    <tbody>
    @forelse ($users as $u)
        <tr class="border-t">
            <td class="p-3 font-medium">{{ $u->name }}</td>
            <td class="p-3">{{ $u->email }}</td>
            <td class="p-3">{{ $u->phone ?? '-' }}</td>
            <td class="p-3">{{ $u->roleLabel() }}</td>
            <td class="p-3">{{ $u->is_active ? 'Aktif' : 'Nonaktif' }}</td>
            <td class="p-3">
                <a class="text-blue-700" href="{{ route('user.edit', $u) }}">Edit</a>
                @if ($u->id !== auth()->id())
                    <form method="POST" action="{{ route('user.destroy', $u) }}" class="inline">
                        @csrf @method('DELETE')
                        <button class="text-red-700">Nonaktifkan</button>
                    </form>
                @endif
            </td>
        </tr>
    @empty
        <tr><td colspan="6" class="p-4">Belum ada pengguna.</td></tr>
    @endforelse
    </tbody>
</table>
</div>
<div class="mt-4">{{ $users->links() }}</div>
@endsection
