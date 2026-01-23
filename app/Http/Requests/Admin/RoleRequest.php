<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class RoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $roleId = $this->route('role');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:roles,name,' . $roleId,
            ],
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'اسم الدور مطلوب',
            'name.unique' => 'اسم الدور موجود بالفعل',
            'name.max' => 'اسم الدور يجب ألا يتجاوز 255 حرف',
            'permissions.array' => 'الصلاحيات يجب أن تكون مصفوفة',
            'permissions.*.exists' => 'صلاحية غير صالحة',
        ];
    }
}
