<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerifikasiPembayaranRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isManager() ?? false;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'in:lunas,ditolak'],
            'tanggal_bayar' => ['required_unless:status,ditolak', 'date'],
            'catatan' => ['required_if:status,ditolak', 'nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'tanggal_bayar.required_unless' => 'Tanggal bayar wajib diisi bila dinyatakan lunas.',
            'catatan.required_if' => 'Alasan penolakan wajib diisi.',
        ];
    }
}
