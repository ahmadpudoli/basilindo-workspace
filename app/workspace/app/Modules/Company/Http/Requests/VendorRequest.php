<?php

namespace App\Modules\Company\Http\Requests;

use Core\Models\Company;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VendorRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $companyTable = (new Company)->getConnectionName().'.'.(new Company)->getTable();
        return ['vendor_company_id' => ['required', 'integer', Rule::exists($companyTable, 'id')->where(fn ($query) => $query->where('entity_type', 'company')->where('status', 'active')->whereJsonContains('roles', 'vendor'))], 'operational_company_ids' => ['required', 'array', 'min:1'], 'operational_company_ids.*' => ['integer', Rule::exists($companyTable, 'id')->where(fn ($query) => $query->where('entity_type', 'company')->where('classification', 'internal')->where('status', 'active')->whereJsonContains('roles', 'operating_company'))], 'name' => ['required', 'string', 'max:255'], 'code' => ['nullable', 'string', 'max:50'], 'tax_identifier' => ['nullable', 'string', 'max:100'], 'status' => ['required', Rule::in(['active', 'inactive'])]];
    }

    public function messages(): array
    {
        return ['vendor_company_id.required' => 'Master perusahaan vendor wajib dipilih.', 'vendor_company_id.exists' => 'Master perusahaan vendor tidak valid.', 'operational_company_ids.required' => 'Pilih minimal satu perusahaan operasional.', 'operational_company_ids.min' => 'Pilih minimal satu perusahaan operasional.', 'operational_company_ids.*.exists' => 'Perusahaan operasional tidak valid.', 'name.required' => 'Nama vendor wajib diisi.', 'status.required' => 'Status wajib dipilih.'];
    }
}
