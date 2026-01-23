<?php

namespace App\Http\Requests\File;

use Illuminate\Foundation\Http\FormRequest;

class StoreFileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'client_id' => 'required|exists:clients,id',
            'land_id' => 'required_without:new_governorate_id|nullable|exists:lands,id',
            'new_governorate_id' => 'nullable|exists:governorates,id',
            'new_city_id' => 'nullable|exists:cities,id',
            'new_district_id' => 'nullable|exists:districts,id',
            'new_zone_id' => 'nullable|exists:zones,id',
            'new_area_id' => 'nullable|exists:areas,id',
            'new_land_no' => 'nullable|string|max:255',
            'new_address' => 'nullable|string',
            'new_notes' => 'nullable|string',
            'room_id' => 'nullable|exists:rooms,id',
            'lane_id' => 'nullable|exists:lanes,id',
            'stand_id' => 'nullable|exists:stands,id',
            'rack_id' => 'nullable|exists:racks,id',
            'file_name' => 'nullable|string|max:255',
            'document' => 'required|file|mimes:pdf|max:51200',
            'items_json' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'client_id.required' => 'العميل مطلوب',
            'land_id.required' => 'القطعة مطلوبة',
            'document.required' => 'ملف PDF مطلوب',
            'document.mimes' => 'يجب أن يكون الملف بصيغة PDF',
            'document.max' => 'حجم الملف يجب ألا يتجاوز 50 ميجابايت',
        ];
    }
}
