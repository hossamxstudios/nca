<?php

namespace App\Http\Requests\Land;

use Illuminate\Foundation\Http\FormRequest;

class StoreLandRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'client_id' => 'required|exists:clients,id',
            'governorate_id' => 'required|exists:governorates,id',
            'city_id' => 'nullable|exists:cities,id',
            'district_id' => 'nullable|exists:districts,id',
            'zone_id' => 'nullable|exists:zones,id',
            'area_id' => 'nullable|exists:areas,id',
            'land_no' => 'required|string|max:50',
            'unit_no' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'client_id.required' => 'العميل مطلوب',
            'client_id.exists' => 'العميل غير موجود',
            'governorate_id.required' => 'المحافظة مطلوبة',
            'land_no.required' => 'رقم القطعة مطلوب',
        ];
    }
}
