<?php

namespace App\Http\Requests\PhysicalLocation;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'building_name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'اسم الغرفة مطلوب',
            'building_name.required' => 'اسم المبنى مطلوب',
        ];
    }
}
