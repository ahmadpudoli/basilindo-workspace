<?php

namespace App\Services\Documents;

use App\Models\Document;
use Core\Models\User;
use Illuminate\Database\Eloquent\Builder;

class DocumentQueryService
{
    public function visibleTo(User $user): Builder
    {
        $query = Document::query();
        if (! $user->hasRole('super_admin')) {
            $query->whereIn('company_id', $user->companies()->select('core_companies.id'));
        }
        return $query;
    }

    public function apply(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['q'] ?? null, fn ($q, $value) => $q->where(function ($sub) use ($value) {
                $sub->where('title', 'ilike', "%{$value}%")
                    ->orWhere('reference_number', 'ilike', "%{$value}%")
                    ->orWhere('normalized_reference_number', 'ilike', "%".preg_replace('/[^A-Z0-9]/', '', strtoupper((string) $value))."%")
                    ->orWhere('original_filename', 'ilike', "%{$value}%");
            }))
            ->when($filters['company_id'] ?? null, fn ($q, $value) => $q->where('company_id', $value))
            ->when($filters['project_id'] ?? null, fn ($q, $value) => $q->where('project_id', $value))
            ->when($filters['vendor_id'] ?? null, fn ($q, $value) => $q->where('vendor_id', $value))
            ->when($filters['document_type_id'] ?? null, fn ($q, $value) => $q->where('document_type_id', $value))
            ->when($filters['status'] ?? null, fn ($q, $value) => $q->where('status', $value))
            ->when($filters['year'] ?? null, fn ($q, $value) => $q->whereYear('document_date', $value))
            ->when($filters['from'] ?? null, fn ($q, $value) => $q->whereDate('document_date', '>=', $value))
            ->when($filters['until'] ?? null, fn ($q, $value) => $q->whereDate('document_date', '<=', $value))
            ->when($filters['tag'] ?? null, fn ($q, $value) => $q->whereJsonContains('tags', $value));
    }
}
