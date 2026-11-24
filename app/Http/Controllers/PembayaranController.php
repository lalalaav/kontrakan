<?php

namespace App\Http\Controllers;

use App\Http\Requests\BuktiPembayaranRequest;
use App\Http\Requests\VerifikasiPembayaranRequest;
use App\Models\KontrakSewa;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PembayaranController extends Controller
{
    public function index(Request $request)
    {
        $cari = $request->string('q')->toString();
        $pembayaran = Pembayaran::with('kontrak.penyewa', 'kontrak.unit')
            ->when($cari !== '', fn ($q) => $q->where(fn ($w) => $w
                ->where('periode', 'like', "%{$cari}%")
                ->orWhereHas('kontrak.penyewa', fn ($u) => $u->where('name', 'like', "%{$cari}%"))))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('dari'), fn ($q) => $q->whereDate('tanggal_jatuh_tempo', '>=', $request->date('dari')))
            ->when($request->filled('sampai'), fn ($q) => $q->whereDate('tanggal_jatuh_tempo', '<=', $request->date('sampai')))
            ->latest()->paginate(15)->withQueryString();

        return view('pembayaran.index', compact('pembayaran'));
    }

    public function create(KontrakSewa $kontrak)
    {
        abort_unless($kontrak->user_id === request()->user()->id || request()->user()->isManager(), 403);
        return view('pembayaran.form', compact('kontrak'));
    }

    public function store(BuktiPembayaranRequest $request, KontrakSewa $kontrak)
    {
        abort_unless($kontrak->user_id === $request->user()->id, 403);

        $data = $request->validate([
            'periode' => [
                'required', 'date_format:Y-m',
                Rule::unique('pembayaran', 'periode')->where('kontrak_sewa_id', $kontrak->id),
            ],
        ]);

        Pembayaran::create([
            'kontrak_sewa_id' => $kontrak->id,
            'periode' => $data['periode'],
            'jumlah' => $kontrak->nominal_sewa,
            'tanggal_jatuh_tempo' => $data['periode'].'-10',
            'tanggal_bayar' => $request->date('tanggal_bayar'),
            'status' => 'menunggu',
            'bukti_path' => $request->file('bukti_path')->store('bukti-pembayaran', 'public'),
        ]);

        return redirect()->route('dashboard')->with('success', 'Bukti pembayaran dikirim. Menunggu verifikasi.');
    }

    public function verify(VerifikasiPembayaranRequest $request, Pembayaran $pembayaran)
    {
        $data = $request->validated();
        $pembayaran->update([
            'status' => $data['status'],
            'tanggal_bayar' => $data['tanggal_bayar'] ?? null,
            'catatan' => $data['catatan'] ?? null,
            'diverifikasi_oleh' => $request->user()->id,
        ]);

        return back()->with('success', 'Pembayaran diverifikasi.');
    }
}
