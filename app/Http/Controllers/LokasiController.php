<?php

namespace App\Http\Controllers;

use App\Http\Requests\LokasiRequest;
use App\Models\Lokasi;

class LokasiController extends Controller
{
    public function index()
    {
        $lokasis = Lokasi::withCount('unitKontrakan')->orderBy('nama')->paginate(12)->withQueryString();

        return view('lokasi.index', compact('lokasis'));
    }

    public function create()
    {
        return view('lokasi.form', ['lokasi' => new Lokasi]);
    }

    public function store(LokasiRequest $request)
    {
        Lokasi::create($request->validated());

        return redirect()->route('lokasi.index')->with('success', 'Lokasi ditambahkan.');
    }

    public function edit(Lokasi $lokasi)
    {
        return view('lokasi.form', compact('lokasi'));
    }

    public function update(LokasiRequest $request, Lokasi $lokasi)
    {
        $lokasi->update($request->validated());

        return redirect()->route('lokasi.index')->with('success', 'Lokasi diperbarui.');
    }

    public function destroy(Lokasi $lokasi)
    {
        if ($lokasi->unitKontrakan()->exists()) {
            return back()->withErrors(['lokasi' => 'Lokasi masih memiliki unit kontrakan.']);
        }

        $lokasi->delete();

        return back()->with('success', 'Lokasi dihapus.');
    }
}