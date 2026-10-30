<?php

namespace App\Modules\HRIS\Application\Services;

use App\Http\Controllers\Controller;
use App\Modules\Core\Application\Services\Audit\AuditLogger;
use App\Modules\HRIS\Domain\Models\Employee;
use App\Modules\HRIS\Domain\Models\EmployeeDocument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class EmployeeDocumentController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', EmployeeDocument::class);
        $user = $request->user();
        $canViewAll = $user->hasRole('super_admin') || $user->can('view_employee_documents');
        $employees = Employee::query()->visibleTo($user)->when(! $canViewAll, fn ($query) => $query->where('user_id', $user->getKey()))->orderBy('full_name')->get(['id', 'full_name', 'employee_number', 'company_id']);
        $employeeIds = $employees->pluck('id');
        $perPage = in_array($request->integer('limit'), [10, 25, 50, 100], true) ? $request->integer('limit') : 10;
        $sort = in_array($request->string('sort')->toString(), ['created_at', 'type', 'effective_from', 'expires_at'], true) ? $request->string('sort')->toString() : 'created_at';
        $direction = $request->string('direction')->toString() === 'asc' ? 'asc' : 'desc';
        $documents = EmployeeDocument::query()
            ->with('employee')
            ->whereIn('employee_id', $employeeIds)
            ->when($request->filled('q'), function ($query) use ($request): void {
                $term = trim((string) $request->input('q'));
                $query->where(fn ($nested) => $nested->where('document_number', 'ilike', "%{$term}%")->orWhere('title', 'ilike', "%{$term}%")->orWhere('original_filename', 'ilike', "%{$term}%"));
            })
            ->when($request->filled('employee_id') && $employeeIds->contains($request->input('employee_id')), fn ($query) => $query->where('employee_id', $request->input('employee_id')))
            ->when(in_array($request->input('type'), ['employment_contract', 'contract_addendum', 'appointment_letter', 'identity_document', 'other'], true), fn ($query) => $query->where('type', $request->input('type')))
            ->orderBy($sort, $direction)
            ->paginate($perPage)
            ->withQueryString();

        return view('modules.hris-employee-documents', compact('documents', 'employees', 'sort', 'direction', 'perPage'));
    }

    public function create(Request $request): View
    {
        Gate::authorize('create', EmployeeDocument::class);
        $employees = Employee::query()->visibleTo($request->user())->where('status', 'active')->orderBy('full_name')->get();
        $documentNumber = EmployeeDocument::generateDocumentNumber();
        return view('modules.hris-employee-document-create', compact('employees', 'documentNumber'));
    }

    public function store(EmployeeDocumentRequest $request): RedirectResponse
    {
        Gate::authorize('create', EmployeeDocument::class);
        $employee = Employee::query()->visibleTo($request->user())->findOrFail($request->string('employee_id')->toString());
        $file = $request->file('file');
        $diskName = config('hris.document_disk', 's3');
        $disk = Storage::disk($diskName);
        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension());
        $documentNumber = $request->string('document_number')->toString();
        if ($documentNumber === '' || EmployeeDocument::query()->where('document_number', $documentNumber)->exists()) {
            $documentNumber = EmployeeDocument::generateDocumentNumber();
        }
        $key = 'hris/employee-documents/'.$employee->getKey().'/'.$documentNumber.'.'.$extension;
        $disk->putFileAs(dirname($key), $file, basename($key), ['visibility' => 'private']);

        EmployeeDocument::create([
            'document_number' => $documentNumber,
            'employee_id' => $employee->getKey(),
            'company_id' => $employee->company_id,
            'type' => $request->string('type')->toString(),
            'title' => $request->string('title')->toString(),
            'original_filename' => Str::limit(basename($file->getClientOriginalName()), 255, ''),
            'object_key' => $key,
            'disk' => $diskName,
            'mime_type' => (string) $file->getMimeType(),
            'file_size' => (int) $file->getSize(),
            'checksum' => hash_file('sha256', $file->getRealPath()),
            'effective_from' => $request->input('effective_from'),
            'expires_at' => $request->input('expires_at'),
            'uploaded_by' => $request->user()->getKey(),
        ]);

        return redirect()->route('workspace.hris.employee-documents')->with('status', 'Dokumen karyawan berhasil diunggah.');
    }

    public function download(Request $request, EmployeeDocument $employeeDocument, AuditLogger $audit)
    {
        Gate::authorize('download', $employeeDocument);
        abort_unless(Storage::disk($employeeDocument->disk)->exists($employeeDocument->object_key), 404);
        $stream = Storage::disk($employeeDocument->disk)->readStream($employeeDocument->object_key);
        abort_unless(is_resource($stream), 404);
        $audit->record('employee_document.downloaded', $employeeDocument, $employeeDocument->company_id, [
            'document_type' => $employeeDocument->type,
            'mime_type' => $employeeDocument->mime_type,
        ]);

        return response()->streamDownload(function () use ($stream): void {
            fpassthru($stream);
            fclose($stream);
        }, basename($employeeDocument->original_filename), ['Content-Type' => $employeeDocument->mime_type]);
    }

    public function destroy(Request $request, EmployeeDocument $employeeDocument): RedirectResponse
    {
        Gate::authorize('delete', $employeeDocument);
        $employeeDocument->delete();
        return back()->with('status', 'Dokumen karyawan diarsipkan. File tetap disimpan untuk kebutuhan retensi dan audit.');
    }
}
