<?php

namespace App\Modules\HRIS\Application\Services;

use Carbon\CarbonImmutable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class LeaveRequestForm extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'request_number' => ['nullable', 'string', 'regex:/^HRIS-LEAVE-[0-9]{8}-[A-Z0-9]{10}$/'],
            'type' => ['required', 'in:leave,sick'],
            'dates' => ['required', 'array', 'min:1'],
            'dates.*' => ['required', 'date', 'distinct'],
            'reason' => ['required', 'string', 'max:2000'],
            'supporting_document' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240', 'required_if:type,sick'],
        ];
    }

    public function messages(): array
    {
        return [
            'dates.required' => 'Pilih minimal satu tanggal pengajuan.',
            'dates.*.date' => 'Format tanggal pengajuan tidak valid.',
            'dates.*.distinct' => 'Tanggal pengajuan tidak boleh duplikat.',
            'supporting_document.required_if' => 'Surat pendukung wajib diunggah untuk pengajuan sakit.',
            'reason.required' => 'Alasan pengajuan wajib diisi.',
            'reason.max' => 'Alasan maksimal 2.000 karakter.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $dates = collect($this->input('dates', []))->filter(fn ($date): bool => filled($date));
            $years = $dates->map(function ($date): ?int {
                try {
                    return CarbonImmutable::parse((string) $date)->year;
                } catch (\Throwable) {
                    return null;
                }
            })->filter()->unique();

            if ($years->count() > 1) {
                $validator->errors()->add('dates', 'Tanggal pengajuan harus berada pada tahun yang sama. Pengajuan lintas tahun harus dibuat terpisah.');
            }
        });
    }
}
