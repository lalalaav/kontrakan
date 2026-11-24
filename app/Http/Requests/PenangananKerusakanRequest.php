<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PenangananKerusakanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isManager() ?? false;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'in:baru,diproses,selesai'],
            'biaya_material' => ['nullable', 'numeric', 'min:0'],
            'biaya_upah' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
