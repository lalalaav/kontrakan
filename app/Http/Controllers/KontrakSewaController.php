<?php

namespace App\Http\Controllers;

use App\Http\Requests\KontrakSewaRequest;
use App\Models\KontrakSewa;
use App\Models\UnitKontrakan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KontrakSewaController extends Controller
{
    public function index(Request $request)
    {
        $cari = $request->string('q')->toString();

        $kontraks = KontrakSewa::with(['unit.lokasi', 'penyewa'])
            ->when($cari !== '', fn ($q) => $q->where(fn ($w) => $w
                ->whereHas('penyewa', fn ($u) => $u->where('name', 'like', "%{$cari}%"))
                ->orWhereHas('unit', fn ($u) => $u->where('nomor_unit', 'like', "%{$cari}%"))))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('dari'), fn ($q) => $q->whereDate('tanggal_mulai', '>=', $request->date('dari')))
            ->when($request->filled('sampai'), fn ($q) => $q->whereDate('tanggal_mulai', '<=', $request->date('sampai')))
            ->latest()->paginate(12)->withQueryString();

        return view('kontrak.index', compact('kontraks'));
    }

    public function create(Request $request)
    {
        return view('kontrak.form', [
            'kontrak' => new KontrakSewa(['status' => 'diajukan']),
            'units' => UnitKontrakan::where('status', 'kosong')->with('lokasi')->orderBy('nomor_unit')->get(),
            'penyewas' => $request->user()->isManager()
                ? User::where('role', 'penyewa')->where('is_active', true)->orderBy('name')->get()
                : collect(),
        ]);
    }

    public function store(KontrakSewaRequest $request)
    {
        $unit = UnitKontrakan::findOrFail($request->integer('unit_kontrakan_id'));

        if ($unit->status !== 'kosong') {
            return back()->withErrors(['unit_kontrakan_id' => 'Unit tidak tersedia.'])->withInput();
        }

        $data = $request->validated();
        $data['nominal_sewa'] = $unit->tarif_sewa;
        $data['user_id'] = $request->user()->isPenyewa() ? $request->user()->id : $request->integer('user_id');
        $data['status'] = $request->user()->isPenyewa() ? 'diajukan' : 'disetujui';

        if ($request->hasFile('ktp_path')) {
            $data['ktp_path'] = $request->file('ktp_path')->store('ktp', 'public');
        }

        DB::transaction(function () use ($data, $unit, $request) {
            $kontrak = KontrakSewa::create($data);
            if ($kontrak->status === 'disetujui') {
                $unit->update(['status' => 'terisi']);
                $kontrak->update(['disetujui_oleh' => $request->user()->id]);
            }
        });

        return redirect()
            ->route($request->user()->isPenyewa() ? 'dashboard' : 'kontrak.index')
            ->with('success', 'Kontrak berhasil dibuat.');
    }

    public function show(Request $request, KontrakSewa $kontrak)
    {
        abort_if($request->user()->isPenyewa() && $kontrak->user_id !== $request->user()->id, 403);
        $kontrak->load(['unit.lokasi', 'penyewa', 'pembayaran']);

        return view('kontrak.show', compact('kontrak'));
    }

    public function updateStatus(Request $request, KontrakSewa $kontrak)
    {
        $data = $request->validate([
            'status' => ['required', 'in:diajukan,disetujui,aktif,ditolak,selesai'],
            'catatan' => ['nullable', 'string', 'max:1000'],
        ]);

        abort_unless($kontrak->dapatTransitionKe($data['status']), 422, 'Transisi status kontrak tidak sah.');

        DB::transaction(function () use ($kontrak, $data, $request) {
            $kontrak->update([
                'status' => $data['status'],
                'catatan' => $data['catatan'] ?? $kontrak->catatan,
                'disetujui_oleh' => $request->user()->id,
            ]);

            if (in_array($data['status'], ['disetujui', 'aktif'], true)) {
                $kontrak->unit()->update(['status' => 'terisi']);
            }
            if (in_array($data['status'], ['selesai', 'ditolak'], true)) {
                $kontrak->unit()->update(['status' => 'kosong']);
            }
        });

        return back()->with('success', 'Status kontrak diperbarui.');
    }

    public function buatTagihan(Request $request, KontrakSewa $kontrak)
    {
        $periode = $request->validate(['periode' => ['required', 'date_format:Y-m']])['periode'];

        if ($kontrak->pembayaran()->where('periode', $periode)->exists()) {
            return back()->withErrors(['periode' => "Tagihan periode {$periode} sudah ada."]);
        }

        $kontrak->pembayaran()->create([
            'periode' => $periode,
            'jumlah' => $kontrak->nominal_sewa,
            'tanggal_jatuh_tempo' => $periode.'-10',
            'status' => 'menunggu',
        ]);

        return back()->with('success', "Tagihan {$periode} dibuat.");
    }
}
