<?php

namespace App\Http\Requests\Item;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:items,name',
            'description' => 'nullable|string',
            'order' => 'required|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'اسم نوع المحتوى مطلوب',
            'name.unique' => 'نوع المحتوى موجود مسبقاً',
        ];
    }
}
