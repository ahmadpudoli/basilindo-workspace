<?php

namespace App\Modules\HRIS\Application\Services;

use Illuminate\Foundation\Http\FormRequest;

class ClaimRequestForm extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array { return ['request_number' => ['nullable', 'string', 'regex:/^HRIS-CLAIM-[0-9]{8}-[A-Z0-9]{10}$/'], 'employee_id' => ['required', 'string', 'size:26'], 'type' => ['required', 'in:reimbursement,cash_advance'], 'category' => ['required', 'string', 'max:100'], 'purpose' => ['required', 'string', 'max:2000'], 'amount' => ['required', 'numeric', 'min:0.01'], 'currency' => ['required', 'in:IDR,USD,SGD'], 'supporting_document_id' => ['nullable', 'string', 'max:100']]; }
}
