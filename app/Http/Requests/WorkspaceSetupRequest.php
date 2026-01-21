<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WorkspaceSetupRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return !authUser()->hasWorkspace();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'domain' => 'nullable|string|max:100|unique:workspaces,domain',
            'description' => 'nullable|string|max:500',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'visibility' => 'required|in:public,private',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Workspace name is required.',
            'name.string' => 'Workspace name must be a string.',
            'name.max' => 'Workspace name may not be greater than 255 characters.',
            'domain.string' => 'Domain must be a string.',
            'domain.max' => 'Domain may not be greater than 100 characters.',
            'domain.unique' => 'The specified domain is already in use.',
            'description.string' => 'Description must be a string.',
            'description.max' => 'Description may not be greater than 500 characters.',
            'logo.image' => 'Logo must be an image file.',
            'logo.mimes' => 'Logo must be a file of type: jpeg, png, jpg, gif.',
            'logo.max' => 'Logo may not be greater than 2048 kilobytes.',
        ];
    }
}
