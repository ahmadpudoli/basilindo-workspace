<?php

namespace App\Modules\HRIS\Application\Services;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EmployeeRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array { $employee = $this->route('employee'); return ['company_id' => ['required', 'integer'], 'user_id' => ['nullable', 'integer'], 'employee_number' => ['required', 'string', 'max:50', Rule::unique('hris.employees', 'employee_number')->where(fn ($query) => $query->where('company_id', $this->input('company_id')))->ignore($employee?->getKey())], 'full_name' => ['required', 'string', 'max:150'], 'email' => ['nullable', 'email', 'max:255'], 'phone' => ['nullable', 'string', 'max:50'], 'job_title' => ['nullable', 'string', 'max:150'], 'manager_id' => ['nullable', 'string', 'size:26'], 'hire_date' => ['nullable', 'date'], 'status' => ['required', 'in:active,inactive,archived']]; }
}
