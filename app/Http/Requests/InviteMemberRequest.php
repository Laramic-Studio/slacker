<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InviteMemberRequest extends FormRequest
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
            'emails' => 'required|array|min:1',
            'emails.*' => 'required|email|distinct',
            'role' => 'nullable|in:member,admin',
        ];
    }


    public function messages(): array
    {
        return [
            'emails.required' => 'At least one email is required.',
            'emails.array' => 'Emails must be provided as an lists.',
            'emails.min' => 'At least one email must be provided.',
            'emails.*.required' => 'Each email is required.',
            'emails.*.email' => 'Each email must be a valid email address.',
            'emails.*.distinct' => 'Duplicate email addresses are not allowed.',
            'role.required' => 'Role is required.',
            'role.in' => 'Role must be either member or admin.',
        ];
    }
}
