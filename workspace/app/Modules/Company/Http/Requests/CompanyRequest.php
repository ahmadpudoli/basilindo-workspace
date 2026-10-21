<?php

namespace App\Modules\Company\Http\Requests;

use Core\Models\Company;
use Core\Models\Vendor;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompanyRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $company = $this->route('company');
        $companyTable = (new Company)->getConnectionName().'.'.(new Company)->getTable();
        $notCurrent = fn ($query) => $company ? $query->where('id', '!=', $company->getKey()) : $query;

        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50'],
            'entity_type' => ['required', Rule::in(['group', 'company'])],
            'classification' => ['required', Rule::in(['internal', 'external'])],
            'roles' => ['nullable', 'array'],
            'roles.*' => [Rule::in(['operating_company', 'client', 'vendor', 'partner'])],
            'visibility_company_ids' => ['nullable', 'array'],
            'visibility_company_ids.*' => ['integer', Rule::exists($companyTable, 'id')->where(fn ($query) => $query->where('entity_type', 'company')->where('classification', 'internal')->where('status', 'active')->whereJsonContains('roles', 'operating_company')->where($notCurrent))],
            'parent_company_id' => ['nullable', 'integer', Rule::exists($companyTable, 'id')->where(fn ($query) => $query->where('entity_type', 'group')->where('classification', $this->input('classification'))->where('status', 'active')->where($notCurrent))],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'logo' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return ['name.required' => 'Nama perusahaan wajib diisi.', 'code.required' => 'Kode perusahaan wajib diisi.', 'entity_type.required' => 'Jenis entitas wajib dipilih.', 'classification.required' => 'Klasifikasi wajib dipilih.', 'roles.*.in' => 'Peran bisnis perusahaan tidak valid.', 'parent_company_id.exists' => 'Parent perusahaan tidak valid.', 'status.required' => 'Status wajib dipilih.', 'logo.mimes' => 'Logo harus berupa JPG, PNG, atau WEBP.', 'logo.max' => 'Ukuran logo maksimal 2 MB.'];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $company = $this->route('company');
            $vendorRelationsLocked = $company && Vendor::query()->where(fn ($query) => $query->where('vendor_company_id', $company->getKey())->orWhere('company_id', $company->getKey()))->exists();

            if ($vendorRelationsLocked) {
                foreach (['entity_type', 'classification', 'status'] as $field) {
                    if ((string) $this->input($field) !== (string) $company->{$field}) {
                        $validator->errors()->add($field, 'Field ini tidak dapat diubah karena perusahaan sudah memiliki relasi vendor.');
                    }
                }

                if (! in_array('vendor', (array) $this->input('roles', []), true)) {
                    $validator->errors()->add('roles', 'Peran Vendor tidak dapat dihapus selama perusahaan masih memiliki relasi vendor.');
                }
            }

            if ($this->input('classification') === 'external' && in_array('operating_company', (array) $this->input('roles', []), true)) {
                $validator->errors()->add('roles', 'Perusahaan dengan klasifikasi External tidak dapat memiliki peran Perusahaan operasional.');
            }

            if ((! filled($this->input('entity_type')) || ! filled($this->input('classification'))) && filled($this->input('roles'))) {
                $validator->errors()->add('roles', 'Pilih jenis entitas dan klasifikasi sebelum memilih peran bisnis.');
            }
        });
    }
}
