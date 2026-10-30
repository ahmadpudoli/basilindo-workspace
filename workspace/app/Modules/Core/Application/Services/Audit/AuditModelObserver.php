<?php

namespace App\Modules\Core\Application\Services\Audit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AuditModelObserver
{
    public function created(Model $model): void
    {
        app(AuditLogger::class)->record(
            $this->action($model, 'created'),
            $model,
            metadata: ['source' => 'model_observer'],
            newValues: $this->snapshot($model->getAttributes()),
        );
    }

    public function updated(Model $model): void
    {
        $changes = $model->getChanges();
        if ($changes === []) {
            return;
        }

        $old = [];
        foreach (array_keys($changes) as $field) {
            $old[$field] = $model->getOriginal($field);
        }

        app(AuditLogger::class)->recordChange(
            $this->action($model, 'updated'),
            $model,
            $this->snapshot($old),
            $this->snapshot($changes),
            metadata: ['source' => 'model_observer', 'changed_fields' => array_keys($changes)],
        );
    }

    public function deleted(Model $model): void
    {
        app(AuditLogger::class)->recordChange(
            $this->action($model, method_exists($model, 'isForceDeleting') && $model->isForceDeleting() ? 'force_deleted' : 'deleted'),
            $model,
            $this->snapshot($model->getOriginal()),
            ['deleted_at' => now()->toIso8601String()],
            metadata: ['source' => 'model_observer'],
        );
    }

    private function action(Model $model, string $verb): string
    {
        $resource = Str::snake(class_basename($model));
        $module = match (true) {
            str_contains($model::class, '\\HRIS\\') => 'hris',
            str_contains($model::class, '\\CRM\\') => 'crm',
            str_contains($model::class, '\\ProjectManagement\\') || $model instanceof \Core\Models\Project => 'project_management',
            str_contains($model::class, '\\Company\\') || in_array($model::class, [\Core\Models\Company::class, \Core\Models\Vendor::class], true) => 'company',
            str_contains($model::class, '\\DocumentManagement\\') => 'documents',
            default => 'workspace',
        };

        return $module.'.'.$resource.'.'.$verb;
    }

    private function snapshot(array $values): array
    {
        $sensitive = ['password', 'remember_token', 'token', 'secret', 'private_key', 'access_key', 'file_contents', 'binary'];

        return collect($values)->except($sensitive)->map(function ($value, $key) use ($sensitive) {
            $key = Str::lower((string) $key);
            foreach ($sensitive as $needle) {
                if ($key === $needle || Str::contains($key, $needle)) {
                    return '[REDACTED]';
                }
            }

            return is_string($value) ? Str::limit($value, 2000, '') : $value;
        })->all();
    }
}
