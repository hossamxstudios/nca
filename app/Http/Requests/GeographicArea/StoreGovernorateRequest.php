<?php

namespace App\Http\Requests\GeographicArea;

use Illuminate\Foundation\Http\FormRequest;

class StoreGovernorateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'اسم المحافظة مطلوب',
        ];
    }
}
