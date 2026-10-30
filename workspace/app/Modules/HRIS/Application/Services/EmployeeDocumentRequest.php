<?php

namespace App\Modules\HRIS\Application\Services;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeDocumentRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'document_number' => ['nullable', 'string', 'regex:/^HRIS-[0-9]{8}-[A-Z0-9]{10}$/'],
            'employee_id' => ['required', 'string', 'size:26'],
            'type' => ['required', 'in:employment_contract,contract_addendum,appointment_letter,identity_document,other'],
            'title' => ['required', 'string', 'max:200'],
            'file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:20480'],
            'effective_from' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:effective_from'],
        ];
    }
}
