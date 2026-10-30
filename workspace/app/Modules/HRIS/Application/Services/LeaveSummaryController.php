<?php

namespace App\Modules\HRIS\Application\Services;

use App\Http\Controllers\Controller;
use App\Modules\HRIS\Domain\Models\LeaveRequest as LeaveRequestModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class LeaveSummaryController extends Controller
{
    public function show(Request $request, LeaveRequestModel $leaveRequest): View
    {
        Gate::authorize('view', $leaveRequest);
        $leaveRequest->load(['employee.company', 'approvalEvents', 'dates']);

        $disk = Storage::disk(config('filesystems.default', 's3'));
        $hasSupportingDocument = filled($leaveRequest->supporting_document_id)
            && $disk->exists((string) $leaveRequest->supporting_document_id);
        $supportingMimeType = $hasSupportingDocument
            ? ($disk->mimeType((string) $leaveRequest->supporting_document_id) ?: 'application/octet-stream')
            : null;
        $canPreviewSupportingDocument = $supportingMimeType === 'application/pdf'
            || str_starts_with((string) $supportingMimeType, 'image/');
        $detailContext = 'summary';
        $showRequesterActions = false;

        return view('modules.hris-leave-show-detail', compact(
            'leaveRequest',
            'hasSupportingDocument',
            'supportingMimeType',
            'canPreviewSupportingDocument',
            'detailContext',
            'showRequesterActions',
        ));
    }
}
