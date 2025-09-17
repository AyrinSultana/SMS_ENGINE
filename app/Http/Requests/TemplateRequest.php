<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
// use App\Models\Employee; // ← uncomment if you want to validate against employees table

class TemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Normalize inputs before validating.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'name'       => is_string($this->name) ? trim($this->name) : $this->name,
            'authorizer' => is_string($this->authorizer) ? trim($this->authorizer) : $this->authorizer,
        ]);
    }

    public function rules(): array
    {
        $rules = [
            'name'                   => ['required', 'string', 'max:255'],
            // Validate authorizer is a proper email
            // Use 'email:rfc' for internal domains/IP SMTP; add 'dns' only if public DNS is correct.
            'authorizer'             => ['required', 'email:rfc', 'max:254'],

            'messages'               => ['required', 'array', 'min:1'],
            'messages.*.title'       => ['required', 'string', 'max:255'],
            'messages.*.message'     => ['required', 'string'],

            // For create (POST): allow CSV/XLS/XLSX
            'template_file'          => ['nullable', 'file', 'mimes:csv,xls,xlsx'],
        ];

        // OPTIONAL: Ensure authorizer is one of your employees (uncomment if desired)
        /*
        $rules['authorizer'][] = Rule::exists('employees', 'email');
        */

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required'                 => 'Template name is required.',
            'name.max'                      => 'Template name cannot exceed 255 characters.',

            'authorizer.required'           => 'Authorizer is required.',
            'authorizer.email'              => 'Please select a valid authorizer email address.',

            'messages.required'             => 'At least one message is required.',
            'messages.min'                  => 'At least one message is required.',
            'messages.*.title.required'     => 'Message title is required.',
            'messages.*.title.max'          => 'Message title cannot exceed 255 characters.',
            'messages.*.message.required'   => 'Message text is required.',

            'template_file.file'            => 'The uploaded file is invalid.',
            'template_file.mimes'           => 'The file must be a CSV or Excel file.',
        ];
    }

    public function attributes(): array
    {
        return [
            'authorizer' => 'authorizer email',
        ];
    }
}
