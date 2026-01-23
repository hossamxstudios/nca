<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'national_id' => 'nullable|digits:14|unique:clients,national_id',
            'client_code' => 'nullable|string|max:50|unique:clients,client_code',
            'files_code' => 'nullable|array',
            'files_code.*' => 'string|max:100',
            'telephone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
            'lands' => 'nullable|array',
            'lands.*.governorate_id' => 'required_with:lands|exists:governorates,id',
            'lands.*.city_id' => 'nullable|exists:cities,id',
            'lands.*.district_id' => 'nullable|exists:districts,id',
            'lands.*.zone_id' => 'nullable|exists:zones,id',
            'lands.*.area_id' => 'nullable|exists:areas,id',
            'lands.*.land_no' => 'required_with:lands|string|max:50',
            'lands.*.unit_no' => 'nullable|string|max:50',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'اسم العميل مطلوب',
            'national_id.digits' => 'الرقم القومي يجب أن يكون 14 رقم',
            'national_id.unique' => 'الرقم القومي مسجل مسبقاً',
            'client_code.unique' => 'كود العميل مسجل مسبقاً',
            'lands.*.governorate_id.required_with' => 'المحافظة مطلوبة',
            'lands.*.land_no.required_with' => 'رقم القطعة مطلوب',
        ];
    }
}
