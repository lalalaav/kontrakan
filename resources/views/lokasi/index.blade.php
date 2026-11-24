@extends('layouts.app')
@section('title', 'Lokasi')
@section('content')
<div class="mb-4 flex items-center justify-between"><h1 class="text-2xl font-bold">Lokasi</h1><a href="{{ route('lokasi.create') }}" class="rounded bg-slate-900 px-3 py-2 text-white">Tambah</a></div>
<div class="overflow-hidden rounded bg-white shadow"><table class="w-full text-left text-sm"><thead class="bg-slate-100"><tr><th class="p-3">Nama</th><th class="p-3">Alamat</th><th class="p-3">Unit</th><th class="p-3">Aksi</th></tr></thead><tbody>@forelse($lokasis as $lokasi)<tr class="border-t"><td class="p-3 font-medium">{{ $lokasi->nama }}</td><td class="p-3">{{ $lokasi->alamat }}</td><td class="p-3">{{ $lokasi->unit_kontrakan_count }}</td><td class="p-3"><a class="text-blue-700" href="{{ route('lokasi.edit', $lokasi) }}">Edit</a></td></tr>@empty<tr><td colspan="4" class="p-4">Belum ada lokasi.</td></tr>@endforelse</tbody></table></div>
<div class="mt-4">{{ $lokasis->links() }}</div>
@endsection
