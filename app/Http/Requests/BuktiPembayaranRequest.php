<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BuktiPembayaranRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isPenyewa() ?? false;
    }

    public function rules(): array
    {
        return [
            'bukti_path' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'tanggal_bayar' => ['required', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'bukti_path.required' => 'Bukti pembayaran wajib diunggah.',
            'bukti_path.mimes' => 'Bukti harus berupa JPG, PNG, atau PDF.',
            'bukti_path.max' => 'Ukuran bukti maksimal 2 MB.',
        ];
    }
}
