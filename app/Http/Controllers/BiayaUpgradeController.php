<?php

namespace App\Http\Controllers;

use App\Http\Requests\BiayaUpgradeRequest;
use App\Models\BiayaUpgrade;
use App\Models\UnitKontrakan;

class BiayaUpgradeController extends Controller
{
    public function index()
    {
        $biayas=BiayaUpgrade::with('unit')->latest('tanggal')->paginate(12);
        return view('upgrade.index', compact('biayas'));
    }
    public function create(){return view('upgrade.form',['biaya'=>new BiayaUpgrade(['tanggal'=>now()->toDateString()]),'units'=>UnitKontrakan::orderBy('nomor_unit')->get()]);}
    public function store(BiayaUpgradeRequest $request){BiayaUpgrade::create($request->validated());return redirect()->route('upgrade.index')->with('success','Biaya upgrade dicatat.');}
    public function edit(BiayaUpgrade $upgrade){return view('upgrade.form',['biaya'=>$upgrade,'units'=>UnitKontrakan::orderBy('nomor_unit')->get()]);}
    public function update(BiayaUpgradeRequest $request,BiayaUpgrade $upgrade){$upgrade->update($request->validated());return redirect()->route('upgrade.index')->with('success','Biaya upgrade diperbarui.');}
    public function destroy(BiayaUpgrade $upgrade){$upgrade->delete();return back()->with('success','Biaya upgrade dihapus.');}
}