<?php

namespace App\Http\Controllers;

use App\Http\Requests\LaporanKerusakanRequest;
use App\Http\Requests\PenangananKerusakanRequest;
use App\Models\LaporanKerusakan;
use App\Models\UnitKontrakan;
use Illuminate\Http\Request;

class LaporanKerusakanController extends Controller
{
    public function index()
    {
        $laporan = LaporanKerusakan::with(['unit','pelapor'])->when(request('status'), fn($q,$v)=>$q->where('status',$v))->latest()->paginate(12)->withQueryString();
        return view('kerusakan.index', compact('laporan'));
    }
    public function create()
    {
        $units = request()->user()->isPenyewa() ? UnitKontrakan::whereHas('kontrak', fn($q)=>$q->where('user_id',request()->user()->id)->where('status','aktif'))->get() : UnitKontrakan::orderBy('nomor_unit')->get();
        return view('kerusakan.form', compact('units'));
    }
    public function store(LaporanKerusakanRequest $request)
    {
        $data=$request->validated(); $data['user_id']=$request->user()->id;
        if($request->hasFile('foto')) $data['foto']=$request->file('foto')->store('kerusakan','public');
        LaporanKerusakan::create($data);
        return redirect()->route('kerusakan.index')->with('success','Laporan kerusakan dikirim.');
    }
    public function update(PenangananKerusakanRequest $request, LaporanKerusakan $kerusakan)
    {
        $kerusakan->update($request->validated());
        return back()->with('success','Penanganan kerusakan diperbarui.');
    }
}