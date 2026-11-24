<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Kontrakan')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 text-slate-800">
@auth
<nav class="bg-slate-900 text-white">
    <div class="mx-auto flex max-w-7xl flex-wrap items-center gap-4 px-4 py-3 text-sm">
        <a href="{{ route('dashboard') }}" class="font-semibold">Kontrakan</a>
        @if (auth()->user()->isManager())
            <a href="{{ route('lokasi.index') }}" class="hover:underline">Lokasi</a>
            <a href="{{ route('unit.index') }}" class="hover:underline">Unit</a>
            <a href="{{ route('kontrak.index') }}" class="hover:underline">Kontrak</a>
            <a href="{{ route('pembayaran.index') }}" class="hover:underline">Pembayaran</a>
            <a href="{{ route('kerusakan.index') }}" class="hover:underline">Kerusakan</a>
            <a href="{{ route('upgrade.index') }}" class="hover:underline">Upgrade</a>
            <a href="{{ route('laporan.index') }}" class="hover:underline">Laporan</a>
        @else
            <a href="{{ route('kerusakan.index') }}" class="hover:underline">Kerusakan</a>
        @endif
        @if (auth()->user()->isAdmin())
            <a href="{{ route('user.index') }}" class="hover:underline">Pengguna</a>
        @endif
        <span class="ml-auto text-slate-300">{{ auth()->user()->name }} ({{ auth()->user()->roleLabel() }})</span>
        <a href="{{ route('password.edit') }}" class="hover:underline">Sandi</a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="hover:underline">Logout</button>
        </form>
    </div>
</nav>
@endauth

<main class="mx-auto max-w-7xl px-4 py-6">
    @if (session('success'))
        <div class="mb-4 rounded bg-green-100 px-4 py-3 text-green-900">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="mb-4 rounded bg-red-100 px-4 py-3 text-red-900">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @yield('content')
</main>
</body>
</html>
