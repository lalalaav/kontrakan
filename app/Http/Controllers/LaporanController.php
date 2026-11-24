<?php

namespace App\Http\Controllers;

use App\Models\BiayaUpgrade;
use App\Models\LaporanKerusakan;
use App\Models\Lokasi;
use App\Models\Pembayaran;
use App\Models\UnitKontrakan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        [$dari, $sampai] = $this->rentang($request);

        return view('laporan.index', $this->data($request, $dari, $sampai) + compact('dari', 'sampai'));
    }

    public function pdf(Request $request)
    {
        [$dari, $sampai] = $this->rentang($request);

        return Pdf::loadView('laporan.pdf', $this->data($request, $dari, $sampai) + compact('dari', 'sampai'))
            ->setPaper('a4', 'landscape')
            ->download("laporan-{$dari}-{$sampai}.pdf");
    }

    public function csv(Request $request)
    {
        [$dari, $sampai] = $this->rentang($request);
        $d = $this->data($request, $dari, $sampai);

        return response()->streamDownload(function () use ($d, $dari, $sampai) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Periode', 'Penyewa', 'Unit', 'Jumlah', 'Status', 'Tanggal Bayar']);
            foreach ($d['pembayaran'] as $p) {
                fputcsv($out, [
                    $p->periode,
                    $p->kontrak->penyewa->name,
                    $p->kontrak->unit->nomor_unit,
                    $p->jumlah,
                    $p->status,
                    $p->tanggal_bayar?->format('Y-m-d'),
                ]);
            }
            fputcsv($out, []);
            fputcsv($out, ['Periode laporan', $dari, 'sampai', $sampai]);
            fputcsv($out, ['Pemasukan', $d['pemasukan']]);
            fputcsv($out, ['Tunggakan', $d['tunggakan']]);
            fputcsv($out, ['Biaya perbaikan', $d['perbaikan']]);
            fputcsv($out, ['Biaya upgrade', $d['upgrade']]);
            fclose($out);
        }, "laporan-{$dari}-{$sampai}.csv", ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function rentang(Request $request): array
    {
        return [
            $request->date('dari')?->toDateString() ?? now()->startOfMonth()->toDateString(),
            $request->date('sampai')?->toDateString() ?? now()->toDateString(),
        ];
    }

    private function data(Request $request, string $dari, string $sampai): array
    {
        $base = Pembayaran::query()
            ->whereBetween('tanggal_jatuh_tempo', [$dari, $sampai])
            ->when($request->filled('unit'), fn ($q) => $q->whereHas('kontrak', fn ($k) => $k->where('unit_kontrakan_id', $request->integer('unit'))))
            ->when($request->filled('lokasi'), fn ($q) => $q->whereHas('kontrak.unit', fn ($u) => $u->where('lokasi_id', $request->integer('lokasi'))));

        $perbaikan = LaporanKerusakan::whereBetween('created_at', [$dari.' 00:00:00', $sampai.' 23:59:59'])->get();

        return [
            'pemasukan' => (clone $base)->where('status', 'lunas')->sum('jumlah'),
            'tunggakan' => (clone $base)->whereIn('status', ['menunggu', 'ditolak'])->sum('jumlah'),
            'pembayaran' => (clone $base)->with(['kontrak.penyewa', 'kontrak.unit.lokasi'])->orderBy('periode')->get(),
            'perbaikan' => $perbaikan->sum(fn ($l) => $l->totalBiaya()),
            'upgrade' => BiayaUpgrade::whereBetween('tanggal', [$dari, $sampai])->sum('jumlah'),
            'units' => UnitKontrakan::orderBy('nomor_unit')->get(),
            'lokasis' => Lokasi::orderBy('nama')->get(),
        ];
    }
}
