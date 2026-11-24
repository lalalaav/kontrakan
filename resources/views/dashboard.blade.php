@extends('layouts.app')
@section('title','Dashboard')
@section('content')
<h1 class="mb-6 text-2xl font-bold">Dashboard</h1>
@if(auth()->user()->isPenyewa())
<div class="rounded bg-white p-5 shadow">@if($kontrak)<p>Unit: <b>{{ $kontrak->unit->nomor_unit }}</b></p><p>Status: {{ ucfirst($kontrak->status) }}</p><a class="mt-3 inline-block text-blue-700" href="{{ route('penyewa.kontrak.show',$kontrak) }}">Detail kontrak</a>@else Belum memiliki kontrak aktif.@endif</div>
@else
<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">@foreach($stats as $k=>$v)<div class="rounded bg-white p-4 shadow"><div class="text-sm text-slate-500">{{ ucfirst($k) }}</div><div class="text-2xl font-bold">{{ is_numeric($v) ? number_format($v,0,',','.') : $v }}</div></div>@endforeach</div>
<h2 class="mt-8 mb-3 text-xl font-semibold">Pembayaran menunggu</h2><div class="rounded bg-white p-4">@forelse($jatuhTempo as $p)<p class="border-b py-2">{{ $p->kontrak->penyewa->name }} — {{ $p->kontrak->unit->nomor_unit }} — Rp {{ number_format($p->jumlah,0,',','.') }}</p>@empty Tidak ada pembayaran menunggu.@endforelse</div>
@endif
@endsection
