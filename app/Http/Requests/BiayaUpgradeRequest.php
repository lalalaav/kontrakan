<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BiayaUpgradeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isManager() ?? false;
    }

    public function rules(): array
    {
        $biaya = $this->route('biaya');

        return [
            'unit_kontrakan_id' => ['nullable', 'integer', 'exists:unit_kontrakan,id'],
            'judul' => ['required', 'string', 'max:150'],
            'keterangan' => ['nullable', 'string', 'max:1000'],
            'jumlah' => ['required', 'numeric', 'min:0', 'max:999999999'],
            'tanggal' => ['required', 'date'],
        ];
    }
}
