@extends('layouts.app')
@section('title', 'Detail Kontrak')
@section('content')
<h1 class="mb-4 text-2xl font-bold">Detail Kontrak</h1>
<div class="mb-4 grid gap-3 rounded bg-white p-5 md:grid-cols-2">
<p>Penyewa: <b>{{ $kontrak->penyewa->name }}</b></p><p>Unit: <b>{{ $kontrak->unit->nomor_unit }}</b></p>
<p>Lokasi: {{ $kontrak->unit->lokasi->nama }}</p><p>Status: <b>{{ ucfirst($kontrak->status) }}</b></p>
<p>Mulai: {{ $kontrak->tanggal_mulai->format('d/m/Y') }}</p><p>Selesai: {{ $kontrak->tanggal_selesai->format('d/m/Y') }}</p>
<p>Nominal: Rp {{ number_format($kontrak->nominal_sewa,0,',','.') }}</p>
</div>
@if(auth()->user()->isManager())
<div class="mb-4 flex flex-wrap gap-2 rounded bg-white p-5">
@foreach(\App\Models\KontrakSewa::TRANSITIONS[$kontrak->status] ?? [] as $next)
<form method="POST" action="{{ route('kontrak.status',$kontrak) }}">@csrf @method('PATCH')<input type="hidden" name="status" value="{{ $next }}"><button class="rounded bg-slate-900 px-3 py-2 text-white">{{ ucfirst($next) }}</button></form>
@endforeach
<form method="POST" action="{{ route('kontrak.tagihan',$kontrak) }}" class="flex gap-2">@csrf<input type="month" name="periode" required class="rounded border px-2"><button class="rounded bg-emerald-700 px-3 py-2 text-white">Buat tagihan</button></form>
</div>
@else
<a href="{{ route('pembayaran.create',$kontrak) }}" class="rounded bg-slate-900 px-3 py-2 text-white">Unggah bukti bayar</a>
@endif
<div class="mt-4 overflow-hidden rounded bg-white"><table class="w-full text-left text-sm"><thead class="bg-slate-100"><tr><th class="p-3">Periode</th><th class="p-3">Jumlah</th><th class="p-3">Jatuh tempo</th><th class="p-3">Status</th></tr></thead><tbody>@forelse($kontrak->pembayaran as $p)<tr class="border-t"><td class="p-3">{{ $p->periode }}</td><td class="p-3">Rp {{ number_format($p->jumlah,0,',','.') }}</td><td class="p-3">{{ $p->tanggal_jatuh_tempo->format('d/m/Y') }}</td><td class="p-3">{{ ucfirst($p->status) }}</td></tr>@empty<tr><td colspan="4" class="p-3">Belum ada tagihan.</td></tr>@endforelse</tbody></table></div>
@endsection
