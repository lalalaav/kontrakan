<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KontrakSewaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $penyewa = $this->user()->isPenyewa();

        return [
            'unit_kontrakan_id' => ['required', 'integer', 'exists:unit_kontrakan,id'],
            'user_id' => $penyewa ? ['nullable'] : ['required', 'integer', 'exists:users,id'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'nominal_sewa' => ['nullable', 'numeric', 'min:0'],
            'ktp_path' => $penyewa
                ? ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048']
                : ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'catatan' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'unit_kontrakan_id.exists' => 'Unit kontrakan tidak ditemukan.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus setelah atau sama dengan tanggal mulai.',
            'ktp_path.required' => 'Scan/foto KTP wajib diunggah.',
            'ktp_path.mimes' => 'KTP harus berupa JPG, PNG, atau PDF.',
            'ktp_path.max' => 'Ukuran KTP maksimal 2 MB.',
        ];
    }
}
