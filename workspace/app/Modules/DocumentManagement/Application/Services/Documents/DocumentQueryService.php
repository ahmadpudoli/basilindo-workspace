<?php

namespace App\Modules\DocumentManagement\Application\Services\Documents;

use App\Modules\DocumentManagement\Domain\Models\Document;
use Core\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class DocumentQueryService
{
    public function __construct(private readonly DocumentCompanyScope $companyScope) {}

    public function visibleTo(User $user): Builder
    {
        $query = Document::query();

        if (($companyIds = $this->companyScope->ids($user)) !== null) {
            $query->whereIn('company_id', $companyIds);
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
            ->when($filters['company_ids'] ?? null, fn ($q, $value) => $q->whereIn('company_id', $value))
            ->when($filters['company_id'] ?? null, fn ($q, $value) => $q->where('company_id', $value))
            ->when($filters['project_id'] ?? null, fn ($q, $value) => $q->where('project_id', $value))
            ->when($filters['document_type_id'] ?? null, fn ($q, $value) => $q->where('document_type_id', $value))
            ->when($filters['status'] ?? null, fn ($q, $value) => $q->where('status', $value))
            ->when($filters['year'] ?? null, fn ($q, $value) => $q->whereYear('document_date', $value))
            ->when($filters['from'] ?? null, fn ($q, $value) => $q->whereDate('document_date', '>=', $value))
            ->when($filters['until'] ?? null, fn ($q, $value) => $q->whereDate('document_date', '<=', $value))
            ->when($filters['tag'] ?? null, fn ($q, $value) => $q->whereJsonContains('tags', $value));
    }

    public function sort(Builder $query, string $sort, string $direction): Builder
    {
        $direction = $direction === 'asc' ? 'asc' : 'desc';
        $documentTable = $query->getModel()->getTable();

        return match ($sort) {
            'company' => $query
                ->leftJoin(DB::connection('core')->getTablePrefix().'companies as sort_companies', 'sort_companies.id', '=', $documentTable.'.company_id')
                ->select($documentTable.'.*')
                ->orderBy('sort_companies.name', $direction),
            'type' => $query
                ->leftJoin(DB::connection('documents')->getTablePrefix().'document_types as sort_types', 'sort_types.id', '=', $documentTable.'.document_type_id')
                ->select($documentTable.'.*')
                ->orderBy('sort_types.name', $direction),
            'title', 'document_date', 'status' => $query->orderBy($documentTable.'.'.$sort, $direction),
            default => $query->orderBy($documentTable.'.document_date', 'desc'),
        };
    }
}
