<?php

namespace App\Modules\DocumentManagement\Domain\Models;

use Core\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DocumentType extends Model
{
    protected $connection = 'documents';

    protected $table = 'document_types';

    protected $fillable = ['company_id', 'name', 'code', 'required_fields', 'is_active'];

    protected function casts(): array
    {
        return ['required_fields' => 'array', 'is_active' => 'boolean'];
    }

    /** @return array<string, array{type: string, mandatory: bool}> */
    public function metadataDefinitions(): array
    {
        $fields = is_array($this->required_fields) ? $this->required_fields : [];

        if (array_is_list($fields)) {
            return collect($fields)
                ->filter(fn ($field): bool => is_array($field) && filled($field['field_name'] ?? $field['name'] ?? null))
                ->mapWithKeys(fn (array $field): array => [(string) ($field['field_name'] ?? $field['name']) => [
                    'type' => Str::lower((string) ($field['type'] ?? 'string')),
                    'mandatory' => filter_var($field['mandatory'] ?? true, FILTER_VALIDATE_BOOLEAN),
                ]])
                ->all();
        }

        return collect($fields)
            ->mapWithKeys(function ($config, $name): array {
                $config = is_array($config) ? $config : ['type' => $config, 'mandatory' => true];

                return [(string) $name => [
                    'type' => Str::lower((string) ($config['type'] ?? 'string')),
                    'mandatory' => filter_var($config['mandatory'] ?? true, FILTER_VALIDATE_BOOLEAN),
                ]];
            })
            ->all();
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }
}
