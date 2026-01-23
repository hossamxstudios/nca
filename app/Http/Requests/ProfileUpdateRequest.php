<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'phone' => ['nullable', 'string', 'max:20'],
            'job_title' => ['nullable', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'الاسم الأول مطلوب',
            'first_name.max' => 'الاسم الأول يجب ألا يتجاوز 255 حرف',
            'last_name.required' => 'الاسم الأخير مطلوب',
            'last_name.max' => 'الاسم الأخير يجب ألا يتجاوز 255 حرف',
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email' => 'يرجى إدخال بريد إلكتروني صحيح',
            'email.unique' => 'البريد الإلكتروني مستخدم بالفعل',
            'phone.max' => 'رقم الهاتف يجب ألا يتجاوز 20 حرف',
            'job_title.max' => 'المسمى الوظيفي يجب ألا يتجاوز 255 حرف',
            'department.max' => 'القسم يجب ألا يتجاوز 255 حرف',
            'bio.max' => 'النبذة يجب ألا تتجاوز 1000 حرف',
            'avatar.image' => 'الملف يجب أن يكون صورة',
            'avatar.mimes' => 'الصورة يجب أن تكون من نوع: jpeg, png, jpg, gif, webp',
            'avatar.max' => 'حجم الصورة يجب ألا يتجاوز 2 ميجابايت',
        ];
    }
}
