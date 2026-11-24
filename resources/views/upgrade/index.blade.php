@extends('layouts.app')
@section('title', 'Biaya Upgrade')
@section('content')
<div class="mb-4 flex items-center justify-between">
    <h1 class="text-2xl font-bold">Biaya Upgrade</h1>
    <a href="{{ route('upgrade.create') }}" class="rounded bg-slate-900 px-3 py-2 text-white">Tambah</a>
</div>
<div class="overflow-hidden rounded bg-white shadow">
<table class="w-full text-left text-sm">
    <thead class="bg-slate-100">
        <tr><th class="p-3">Tanggal</th><th class="p-3">Judul</th><th class="p-3">Unit</th>
            <th class="p-3">Jumlah</th><th class="p-3">Aksi</th></tr>
    </thead>
    <tbody>
    @forelse ($biayas as $b)
        <tr class="border-t">
            <td class="p-3">{{ $b->tanggal->format('d/m/Y') }}</td>
            <td class="p-3 font-medium">{{ $b->judul }}</td>
            <td class="p-3">{{ optional($b->unit)->nomor_unit ?? '-' }}</td>
            <td class="p-3">Rp {{ number_format($b->jumlah, 0, ',', '.') }}</td>
            <td class="p-3">
                <a class="text-blue-700" href="{{ route('upgrade.edit', $b) }}">Edit</a>
                <form method="POST" action="{{ route('upgrade.destroy', $b) }}" class="inline">
                    @csrf @method('DELETE')
                    <button class="text-red-700">Hapus</button>
                </form>
            </td>
        </tr>
    @empty
        <tr><td colspan="5" class="p-4">Belum ada biaya upgrade.</td></tr>
    @endforelse
    </tbody>
</table>
</div>
<div class="mt-4">{{ $biayas->links() }}</div>
@endsection
