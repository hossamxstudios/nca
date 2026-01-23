<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $clientId = $this->route('client');

        return [
            'name' => 'required|string|max:255',
            'national_id' => [
                'nullable',
                'digits:14',
                Rule::unique('clients', 'national_id')->ignore($clientId),
            ],
            'client_code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('clients', 'client_code')->ignore($clientId),
            ],
            'files_code' => 'nullable|array',
            'files_code.*' => 'string|max:100',
            'telephone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'اسم العميل مطلوب',
            'national_id.digits' => 'الرقم القومي يجب أن يكون 14 رقم',
            'national_id.unique' => 'الرقم القومي مسجل مسبقاً',
            'client_code.unique' => 'كود العميل مسجل مسبقاً',
        ];
    }
}
