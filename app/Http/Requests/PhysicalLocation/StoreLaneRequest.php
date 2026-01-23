<?php

namespace App\Http\Requests\PhysicalLocation;

use Illuminate\Foundation\Http\FormRequest;

class StoreLaneRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'room_id' => 'required|exists:rooms,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'room_id.required' => 'الغرفة مطلوبة',
            'room_id.exists' => 'الغرفة غير موجودة',
            'name.required' => 'اسم الممر مطلوب',
        ];
    }
}
