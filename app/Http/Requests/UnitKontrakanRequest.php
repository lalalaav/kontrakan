<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UnitKontrakanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isManager() ?? false;
    }

    public function rules(): array
    {
        $unit = $this->route('unit');

        return [
            'lokasi_id' => ['required', 'integer', 'exists:lokasi,id'],
            'nomor_unit' => [
                'required', 'string', 'max:20',
                Rule::unique('unit_kontrakan', 'nomor_unit')->ignore($unit),
            ],
            'tarif_sewa' => ['required', 'numeric', 'min:0', 'max:999999999'],
            'jumlah_kamar' => ['required', 'integer', 'min:1', 'max:50'],
            'luas_bangunan' => ['nullable', 'numeric', 'min:0'],
            'fasilitas' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', 'in:kosong,terisi,perbaikan'],
            'izin_hewan' => ['nullable', 'boolean'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'lokasi_id.exists' => 'Lokasi tidak ditemukan.',
            'nomor_unit.unique' => 'Nomor unit sudah terpakai.',
        ];
    }
}
