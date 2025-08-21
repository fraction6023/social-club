<?php

namespace App\Http\Requests;

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
            'name'            => ['required', 'string', 'max:255'],
            'email'           => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users')->ignore($this->user()->id)],

            // الحقول الإضافية
            'nationality'     => ['nullable', 'string', 'max:255'],
            'national_id'     => ['nullable', 'string', 'max:50'],
            'birth_date'      => ['nullable', 'date'],
            'phone'           => ['nullable', 'string', 'max:50'],
            'address'         => ['nullable', 'string', 'max:255'],
            'weight'          => ['nullable', 'numeric', 'min:0'],
            'height'          => ['nullable', 'numeric', 'min:0'],
            'health_status'   => ['nullable', 'string', 'max:255'],
            'swimming_level'  => ['required', Rule::in(['none','basic','intermediate','advanced'])],

            // إن أردت تمكين تعديل هذه من الصفحة، أزل التعليق وأضفها للـ fillable
            // 'account_status'  => ['required', Rule::in(['pending','active','suspended'])],
            // 'presence_status' => ['required', Rule::in(['in','out'])],
        ];
    }
}
