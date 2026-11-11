<?php

namespace App\Modules\Core\Http\Requests;

use Core\Models\OrganizationUnit;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class OrganizationUnitRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        $unit = $this->route('organizationUnit');
        return [
            'company_id' => ['required', 'integer'],
            'parent_id' => ['nullable', 'string', 'size:26'],
            'code' => ['required', 'string', 'max:50', Rule::unique('core.organization_units', 'code')->where(fn ($query) => $query->where('company_id', $this->integer('company_id')))->ignore($unit?->getKey())],
            'name' => ['required', 'string', 'max:150'],
            'type' => ['required', Rule::in(['directorate', 'division', 'department', 'team', 'unit'])],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'sort_order' => ['required', 'integer', 'min:0', 'max:9999'],
        ];
    }
    public function after(): array
    {
        return [function (Validator $validator): void {
            if (blank($this->parent_id)) return;
            $parent = OrganizationUnit::query()->find($this->parent_id);
            if (! $parent || (int) $parent->company_id !== $this->integer('company_id')) {
                $validator->errors()->add('parent_id', 'Induk unit harus berasal dari perusahaan yang sama.');
                return;
            }
            $unit = $this->route('organizationUnit');
            $cursor = $parent;
            while ($unit && $cursor) {
                if ((string) $cursor->getKey() === (string) $unit->getKey()) {
                    $validator->errors()->add('parent_id', 'Struktur organisasi tidak boleh membentuk siklus.');
                    break;
                }
                $cursor = $cursor->parent;
            }
        }];
    }
}
