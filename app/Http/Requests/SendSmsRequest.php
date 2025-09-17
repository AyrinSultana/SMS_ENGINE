<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendSmsRequest extends FormRequest
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
     */
    public function rules(): array
    {
        $baseRules = [
            'sms_method'        => 'required|in:all_users,comma_separated,upload_excel',
            'templateDropdown'  => 'required|string',
            'messageDropdown'   => 'required|string',
            'authorizer'        => 'required|string',
        ];

        // Apply conditional rules based on the SMS method
        $smsMethod = $this->input('sms_method');

        if ($smsMethod === 'comma_separated') {
            $baseRules['numbers'] = 'required|string';
        } elseif ($smsMethod === 'upload_excel') {
            $baseRules['excel_file'] = 'required|file|mimes:csv,xls,xlsx,txt|max:2048';
        }

        return $baseRules;
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'sms_method.required'       => 'Please select an SMS method.',
            'sms_method.in'             => 'Invalid SMS method selected.',
            'templateDropdown.required' => 'Please select a template.',
            'messageDropdown.required'  => 'Please select a message.',
            'authorizer.required'       => 'Please select or enter an authorizer.',
            'numbers.required'          => 'Please enter comma-separated mobile numbers.',
            'excel_file.required'       => 'Please upload a file containing mobile numbers.',
            'excel_file.file'           => 'The uploaded file is invalid.',
            'excel_file.mimes'          => 'The file must be a CSV, Excel, or text file.',
            'excel_file.max'            => 'The uploaded file may not be greater than 2MB.',
        ];
    }
}
