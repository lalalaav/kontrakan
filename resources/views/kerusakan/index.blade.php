@extends('layouts.app')
@section('title', 'Kerusakan')
@section('content')
<div class="mb-4 flex items-center justify-between">
    <h1 class="text-2xl font-bold">Laporan Kerusakan</h1>
    <a href="{{ route('kerusakan.create') }}" class="rounded bg-slate-900 px-3 py-2 text-white">Lapor</a>
</div>
<form class="mb-4 flex flex-wrap gap-2">
    <select name="status" class="rounded border px-3 py-2">
        <option value="">Semua status</option>
        @foreach (['baru','diproses','selesai'] as $s)
            <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>
        @endforeach
    </select>
    <button class="rounded bg-slate-700 px-4 py-2 text-white">Filter</button>
</form>
<div class="overflow-hidden rounded bg-white shadow">
<table class="w-full text-left text-sm">
    <thead class="bg-slate-100">
        <tr><th class="p-3">Judul</th><th class="p-3">Unit</th><th class="p-3">Pelapor</th>
            <th class="p-3">Status</th><th class="p-3">Biaya</th><th class="p-3">Aksi</th></tr>
    </thead>
    <tbody>
    @forelse ($laporan as $l)
        <tr class="border-t">
            <td class="p-3 font-medium">{{ $l->judul }}</td>
            <td class="p-3">{{ $l->unit->nomor_unit }}</td>
            <td class="p-3">{{ $l->pelapor->name }}</td>
            <td class="p-3">{{ ucfirst($l->status) }}</td>
            <td class="p-3">Rp {{ number_format($l->totalBiaya(), 0, ',', '.') }}</td>
            <td class="p-3">
                @if (auth()->user()->isManager())
                    <form method="POST" action="{{ route('kerusakan.update', $l) }}" class="flex gap-1">
                        @csrf @method('PATCH')
                        <select name="status" class="rounded border px-2 py-1">
                            @foreach (['baru','diproses','selesai'] as $s)
                                <option value="{{ $s }}" @selected($l->status === $s)>{{ ucfirst($s) }}</option>
                            @endforeach
                        </select>
                        <input name="biaya_material" value="{{ (int) $l->biaya_material }}" class="w-24 rounded border px-2 py-1">
                        <input name="biaya_upah" value="{{ (int) $l->biaya_upah }}" class="w-24 rounded border px-2 py-1">
                        <button class="rounded bg-slate-700 px-2 py-1 text-white">Simpan</button>
                    </form>
                @endif
            </td>
        </tr>
    @empty
        <tr><td colspan="6" class="p-4">Belum ada laporan.</td></tr>
    @endforelse
    </tbody>
</table>
</div>
<div class="mt-4">{{ $laporan->links() }}</div>
@endsection
