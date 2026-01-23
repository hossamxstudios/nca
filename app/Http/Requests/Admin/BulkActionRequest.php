<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class BulkActionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'ids' => 'required|array|min:1',
            'ids.*' => 'required|integer|exists:users,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'ids.required' => 'يجب تحديد عنصر واحد على الأقل',
            'ids.array' => 'البيانات المرسلة غير صحيحة',
            'ids.min' => 'يجب تحديد عنصر واحد على الأقل',
            'ids.*.exists' => 'أحد العناصر المحددة غير موجود',
        ];
    }
}
