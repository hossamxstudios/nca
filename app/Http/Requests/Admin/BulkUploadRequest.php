<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class BulkUploadRequest extends FormRequest
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
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
            'mode' => 'required|in:create,upsert',
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
            'file.required' => 'يجب اختيار ملف للاستيراد',
            'file.file' => 'الملف المرفق غير صالح',
            'file.mimes' => 'يجب أن يكون الملف من نوع: xlsx, xls, csv',
            'file.max' => 'حجم الملف يجب ألا يتجاوز 10 ميجابايت',
            'mode.required' => 'يجب تحديد وضع الاستيراد',
            'mode.in' => 'وضع الاستيراد غير صحيح',
        ];
    }
}
