<?php

namespace App\Modules\DocumentManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\DocumentManagement\Domain\Models\Bundle;
use App\Modules\DocumentManagement\Domain\Models\Document;
use App\Modules\DocumentManagement\Domain\Models\DocumentType;
use Illuminate\View\View;

class DocumentManagementDashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('modules.documents.dashboard', [
            'stats' => [
                ['label' => 'Dokumen', 'value' => Document::query()->where('status', 'ready')->count()],
                ['label' => 'Tipe dokumen', 'value' => DocumentType::query()->count()],
                ['label' => 'Dokumen tersedia', 'value' => Document::query()->where('status', 'ready')->count()],
                ['label' => 'Bundel', 'value' => Bundle::query()->count()],
            ],
                'documents' => Document::query()->where('status', 'ready')->with(['company', 'documentType'])->latest()->limit(10)->get(),
        ]);
    }
}
