<?php

namespace App\Http\Requests\PhysicalLocation;

use Illuminate\Foundation\Http\FormRequest;

class StoreRackRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'stand_id' => 'required|exists:stands,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'stand_id.required' => 'الستاند مطلوب',
            'stand_id.exists' => 'الستاند غير موجود',
            'name.required' => 'اسم الرف مطلوب',
        ];
    }
}
