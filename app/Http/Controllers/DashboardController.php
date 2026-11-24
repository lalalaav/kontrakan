<?php

namespace App\Http\Controllers;

use App\Models\BiayaUpgrade;
use App\Models\LaporanKerusakan;
use App\Models\Pembayaran;
use App\Models\UnitKontrakan;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $user = request()->user();

        if ($user->isPenyewa()) {
            $kontrak = $user->kontrak()->with(['unit.lokasi', 'pembayaran'])->latest()->first();

            return view('dashboard', compact('kontrak'));
        }

        $stats = [
            'unit' => UnitKontrakan::count(),
            'kosong' => UnitKontrakan::where('status', 'kosong')->count(),
            'terisi' => UnitKontrakan::where('status', 'terisi')->count(),
            'perbaikan' => UnitKontrakan::where('status', 'perbaikan')->count(),
            'menunggu' => Pembayaran::where('status', 'menunggu')->count(),
            'tunggakan' => Pembayaran::where('status', 'menunggu')->sum('jumlah'),
            'pemasukan' => Pembayaran::where('status', 'lunas')->sum('jumlah'),
            'kerusakan' => LaporanKerusakan::whereIn('status', ['baru', 'diproses'])->count(),
            'upgrade' => BiayaUpgrade::sum('jumlah'),
        ];

        $jatuhTempo = Pembayaran::with('kontrak.penyewa', 'kontrak.unit')
            ->where('status', 'menunggu')
            ->orderBy('tanggal_jatuh_tempo')
            ->limit(8)
            ->get();

        return view('dashboard', compact('stats', 'jatuhTempo'));
    }
}