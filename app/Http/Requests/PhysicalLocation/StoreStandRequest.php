<?php

namespace App\Http\Requests\PhysicalLocation;

use Illuminate\Foundation\Http\FormRequest;

class StoreStandRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'lane_id' => 'required|exists:lanes,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'lane_id.required' => 'الممر مطلوب',
            'lane_id.exists' => 'الممر غير موجود',
            'name.required' => 'اسم الستاند مطلوب',
        ];
    }
}
