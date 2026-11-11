<?php

namespace App\Modules\HRIS\Application\Services;

use Illuminate\Foundation\Http\FormRequest;

class ClaimRequestForm extends FormRequest
{
    public function authorize(): bool { return true; }
    protected function prepareForValidation(): void
    {
        if (! $this->filled('items') && $this->filled('category')) {
            $this->merge(['items' => [[
                'category' => $this->input('category'),
                'description' => $this->input('purpose'),
                'currency' => $this->input('currency', 'IDR'),
                'amount' => $this->input('amount'),
                'attachment_reference' => $this->input('supporting_document_id'),
            ]]]);
        }
    }

    public function rules(): array
    {
        return [
            'action' => ['nullable', 'in:draft,submit'],
            'request_number' => ['nullable', 'string', 'regex:/^HRIS-CLAIM-[0-9]{8}-[A-Z0-9]{10}$/'],
            'employee_id' => ['nullable', 'string', 'size:26'],
            'type' => ['required', 'in:reimbursement,cash_advance'],
            'department' => ['nullable', 'string', 'max:150'],
            'project_id' => ['nullable', 'integer'],
            'client_id' => ['nullable', 'integer'],
            'po_number' => ['nullable', 'string', 'max:100'],
            'items' => ['required', 'array', 'min:1', 'max:100'],
            'items.*.receipt_date' => ['nullable', 'date'],
            'items.*.activity_date' => ['nullable', 'date'],
            'items.*.category' => ['required', 'string', 'max:100'],
            'items.*.description' => ['required', 'string', 'max:2000'],
            'items.*.currency' => ['required', 'in:IDR,USD,SGD'],
            'items.*.amount' => ['required', 'numeric', 'min:0.01'],
            'items.*.attachment_reference' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:100'],
            'purpose' => ['nullable', 'string', 'max:2000'],
            'amount' => ['nullable', 'numeric', 'min:0.01'],
            'currency' => ['nullable', 'in:IDR,USD,SGD'],
            'supporting_document_id' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $currencies = collect($this->input('items', []))->pluck('currency')->filter()->unique();
            if ($currencies->count() > 1) $validator->errors()->add('items', 'Satu pengajuan hanya dapat menggunakan satu mata uang.');
        });
    }
}
