<?php

namespace App\Modules\DocumentManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\DocumentManagement\Domain\Models\Bundle;
use App\Modules\DocumentManagement\Domain\Models\Document;
use App\Modules\DocumentManagement\Domain\Models\DocumentType;
use App\Modules\DocumentManagement\Application\Services\Documents\DocumentCompanyScope;
use Illuminate\View\View;
use Illuminate\Support\Facades\Gate;

class DocumentManagementDashboardController extends Controller
{
    public function __invoke(DocumentCompanyScope $companyScope): View
    {
        Gate::authorize('viewAny', Document::class);
        $user = auth()->user();
        $companyIds = $companyScope->ids($user);
        $documents = Document::query()->when($companyIds !== null, fn ($query) => $query->whereIn('company_id', $companyIds));
        $documentTypes = DocumentType::query()->when($companyIds !== null, function ($query) use ($companyIds): void {
            $query->where(function ($scope) use ($companyIds): void {
                $scope->whereNull('company_id')
                    ->orWhereIn('company_id', $companyIds)
                    ->orWhereExists(function ($pivot) use ($companyIds): void {
                        $pivot->selectRaw('1')
                            ->from('document_type_companies')
                            ->whereColumn('document_type_companies.document_type_id', 'document_types.id')
                            ->whereIn('document_type_companies.company_id', $companyIds);
                    });
            });
        });
        $bundles = Bundle::query()->when($companyIds !== null, fn ($query) => $query->whereIn('company_id', $companyIds));
        $readyDocuments = (clone $documents)->where('status', 'ready');

        return view('modules.documents.dashboard', [
            'stats' => [
                ['label' => 'Dokumen', 'value' => (clone $readyDocuments)->count()],
                ['label' => 'Tipe dokumen', 'value' => $documentTypes->count()],
                ['label' => 'Dokumen tersedia', 'value' => (clone $readyDocuments)->count()],
                ['label' => 'Bundel', 'value' => $bundles->count()],
            ],
            'documents' => $readyDocuments->with(['company', 'documentType'])->latest()->limit(10)->get(),
        ]);
    }
}
