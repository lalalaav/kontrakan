<?php

namespace App\Http\Controllers;

use App\Http\Requests\UnitKontrakanRequest;
use App\Models\Lokasi;
use App\Models\UnitKontrakan;
use Illuminate\Support\Facades\Storage;

class UnitKontrakanController extends Controller
{
    public function index()
    {
        $units = UnitKontrakan::with('lokasi')
            ->when(request('q'), fn ($q, $v) => $q->where('nomor_unit', 'like', "%$v%"))
            ->when(request('status'), fn ($q, $v) => $q->where('status', $v))
            ->when(request('lokasi_id'), fn ($q, $v) => $q->where('lokasi_id', $v))
            ->orderBy('nomor_unit')
            ->paginate(12)
            ->withQueryString();

        return view('unit.index', [
            'units' => $units,
            'lokasis' => Lokasi::orderBy('nama')->get(),
        ]);
    }

    public function create()
    {
        return view('unit.form', [
            'unit' => new UnitKontrakan(['status' => 'kosong', 'jumlah_kamar' => 1]),
            'lokasis' => Lokasi::orderBy('nama')->get(),
        ]);
    }

    public function store(UnitKontrakanRequest $request)
    {
        $data = $request->validated();
        $data['izin_hewan'] = $request->boolean('izin_hewan');

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('unit', 'public');
        }

        UnitKontrakan::create($data);

        return redirect()->route('unit.index')->with('success', 'Unit ditambahkan.');
    }

    public function edit(UnitKontrakan $unit)
    {
        return view('unit.form', [
            'unit' => $unit,
            'lokasis' => Lokasi::orderBy('nama')->get(),
        ]);
    }

    public function update(UnitKontrakanRequest $request, UnitKontrakan $unit)
    {
        $data = $request->validated();
        $data['izin_hewan'] = $request->boolean('izin_hewan');

        if ($request->hasFile('foto')) {
            if ($unit->foto) {
                Storage::disk('public')->delete($unit->foto);
            }
            $data['foto'] = $request->file('foto')->store('unit', 'public');
        }

        $unit->update($data);

        return redirect()->route('unit.index')->with('success', 'Unit diperbarui.');
    }

    public function destroy(UnitKontrakan $unit)
    {
        if ($unit->kontrak()->exists()) {
            return back()->withErrors(['unit' => 'Unit sudah punya riwayat kontrak, tidak dihapus.']);
        }

        if ($unit->foto) {
            Storage::disk('public')->delete($unit->foto);
        }

        $unit->delete();

        return back()->with('success', 'Unit dihapus.');
    }
}