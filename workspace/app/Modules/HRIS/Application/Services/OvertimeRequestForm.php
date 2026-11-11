<?php

namespace App\Modules\HRIS\Application\Services;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class OvertimeRequestForm extends FormRequest
{
    public function authorize(): bool { return true; }

    protected function prepareForValidation(): void
    {
        $entries = $this->input('entries');
        if (is_array($entries)) {
            $entries = array_map(static function (mixed $entry): mixed {
                if (is_array($entry) && ! filled($entry['day_type'] ?? null)) $entry['day_type'] = 'workday';
                return $entry;
            }, $entries);
            $this->merge(['entries' => $entries]);
        }

        if ($this->input('overtime_type') !== 'external_project') {
            $this->merge(['work_reference_type' => 'none']);
            return;
        }

        $this->merge(['work_reference_type' => $this->input('work_reference_type') ?: (filled($this->input('opportunity_id')) ? 'opportunity' : (filled($this->input('project_id')) ? 'project' : 'none'))]);
    }

    public function rules(): array
    {
        return [
            'action' => ['nullable', 'in:draft,submit'],
            'request_number' => ['nullable', 'string', 'max:100'],
            'employee_id' => ['nullable', 'string', 'size:26'],
            'overtime_type' => ['required', 'in:internal,external_project'],
            'work_reference_type' => ['required_if:overtime_type,external_project', 'in:project,opportunity,none'],
            'project_id' => ['exclude_unless:work_reference_type,project', 'nullable', 'integer', 'min:1'],
            'opportunity_id' => ['exclude_unless:work_reference_type,opportunity', 'nullable', 'uuid'],
            'work_location' => ['nullable', 'string', 'max:150'],
            'reason' => ['nullable', 'string', 'max:2000'],
            'entries' => ['required', 'array', 'min:1', 'max:31'],
            'entries.*.work_date' => ['required', 'date_format:Y-m-d', 'before_or_equal:today'],
            'entries.*.day_type' => ['required', 'in:workday,holiday'],
            'entries.*.start_time' => ['required', 'date_format:H:i'],
            'entries.*.end_time' => ['required', 'date_format:H:i'],
            'entries.*.work_description' => ['required', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'overtime_type.required' => 'Jenis lembur wajib dipilih.',
            'overtime_type.in' => 'Jenis lembur tidak valid.',
            'work_reference_type.required_if' => 'Referensi pekerjaan wajib dipilih untuk lembur external/project.',
            'project_id.*' => 'Project yang dipilih tidak valid.',
            'opportunity_id.uuid' => 'Opportunity yang dipilih tidak valid.',
            'entries.required' => 'Minimal satu rincian lembur harus diisi.',
            'entries.min' => 'Minimal satu rincian lembur harus diisi.',
            'entries.max' => 'Rincian lembur tidak boleh lebih dari 31 baris.',
            'entries.*.work_date.required' => 'Tanggal lembur wajib diisi.',
            'entries.*.work_date.date_format' => 'Tanggal lembur harus berformat YYYY-MM-DD.',
            'entries.*.work_date.before_or_equal' => 'Tanggal lembur tidak boleh setelah hari ini.',
            'entries.*.day_type.required' => 'Jenis hari lembur wajib dipilih.',
            'entries.*.day_type.in' => 'Jenis hari lembur tidak valid.',
            'entries.*.start_time.required' => 'Jam mulai wajib dipilih.',
            'entries.*.start_time.date_format' => 'Jam mulai harus berformat HH:mm.',
            'entries.*.end_time.required' => 'Jam selesai wajib dipilih.',
            'entries.*.end_time.date_format' => 'Jam selesai harus berformat HH:mm.',
            'entries.*.work_description.required' => 'Uraian pekerjaan wajib diisi.',
            'entries.*.work_description.max' => 'Uraian pekerjaan maksimal 2.000 karakter.',
        ];
    }

    public function attributes(): array
    {
        return [
            'entries.*.work_date' => 'tanggal lembur',
            'entries.*.day_type' => 'jenis hari lembur',
            'entries.*.start_time' => 'jam mulai',
            'entries.*.end_time' => 'jam selesai',
            'entries.*.work_description' => 'uraian pekerjaan',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            foreach ((array) $this->input('entries', []) as $index => $entry) {
                $start = $entry['start_time'] ?? null;
                $end = $entry['end_time'] ?? null;
                if (is_string($start) && is_string($end) && preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $start) && preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $end) && $end <= $start) {
                    $validator->errors()->add("entries.{$index}.end_time", 'Jam selesai harus lebih besar dari jam mulai. Jika lembur melewati tengah malam, buat rincian baru dengan tanggal berikutnya.');
                }
            }
        });
    }
}
