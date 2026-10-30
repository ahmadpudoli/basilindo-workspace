<?php

namespace App\Modules\HRIS\Application\Services;

use Illuminate\Foundation\Http\FormRequest;

class OvertimeRequestForm extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array { return ['request_number' => ['nullable', 'string', 'regex:/^HRIS-OVERTIME-[0-9]{8}-[A-Z0-9]{10}$/'], 'employee_id' => ['required', 'string', 'size:26'], 'work_date' => ['required', 'date'], 'start_time' => ['required', 'date_format:H:i'], 'end_time' => ['required', 'date_format:H:i'], 'reason' => ['required', 'string', 'max:2000']]; }
}
