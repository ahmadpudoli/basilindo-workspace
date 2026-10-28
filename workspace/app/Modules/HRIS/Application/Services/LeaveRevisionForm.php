<?php

namespace App\Modules\HRIS\Application\Services;

use Carbon\CarbonImmutable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class LeaveRevisionForm extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'type' => ['required', 'in:leave,sick'],
            'dates' => ['required', 'array', 'min:1'],
            'dates.*' => ['required', 'date', 'distinct'],
            'reason' => ['required', 'string', 'max:2000'],
            'supporting_document' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ];
    }
    public function messages(): array
    {
        return ['dates.required' => 'Pilih minimal satu tanggal pengajuan.', 'dates.*.date' => 'Format tanggal pengajuan tidak valid.', 'dates.*.distinct' => 'Tanggal pengajuan tidak boleh duplikat.', 'reason.required' => 'Alasan pengajuan wajib diisi.', 'reason.max' => 'Alasan maksimal 2.000 karakter.'];
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
