<?php

namespace App\Modules\HRIS\Application\Services;

use App\Modules\HRIS\Domain\Models\Employee;
use App\Modules\HRIS\Domain\Models\HrisSetting;
use App\Modules\HRIS\Domain\Models\OvertimeRequest;
use App\Modules\Core\Application\Services\NumberingService;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OvertimeService
{
    public function submit(Employee $employee, array $data, int $actorId): OvertimeRequest
    {
        return $this->persist($employee, new OvertimeRequest, $data, $actorId, false, false);
    }

    public function saveDraft(Employee $employee, array $data, int $actorId): OvertimeRequest
    {
        return $this->persist($employee, new OvertimeRequest, $data, $actorId, false, true);
    }

    public function updateDraft(OvertimeRequest $request, Employee $employee, array $data, int $actorId): OvertimeRequest
    {
        return $this->persist($employee, $request, $data, $actorId, false, true);
    }

    public function submitDraft(OvertimeRequest $request, Employee $employee, array $data, int $actorId): OvertimeRequest
    {
        return $this->persist($employee, $request, $data, $actorId, false, false);
    }

    public function resubmit(OvertimeRequest $request, Employee $employee, array $data, int $actorId): OvertimeRequest
    {
        if ($request->status !== 'needs_revision' || (string) $request->employee_id !== (string) $employee->getKey()) throw ValidationException::withMessages(['request' => 'Pengajuan tidak dapat direvisi.']);
        return $this->persist($employee, $request, $data, $actorId, true, false);
    }

    private function persist(Employee $employee, OvertimeRequest $request, array $data, int $actorId, bool $resubmission, bool $draft): OvertimeRequest
    {
        return DB::connection('hris')->transaction(function () use ($employee, $request, $data, $actorId, $resubmission, $draft): OvertimeRequest {
            $reference = app(OvertimeWorkReferenceService::class)->resolve($employee, $data, $actorId);
            $employee->loadMissing(['manager', 'organizationUnit']);
            if (! $draft && ! $employee->manager?->user_id) throw ValidationException::withMessages(['employee_id' => 'Atasan langsung karyawan harus memiliki akun Workspace sebelum pengajuan dikirim.']);
            $entries = $data['entries'] ?? [[
                'work_date' => $data['work_date'], 'start_time' => $data['start_time'], 'end_time' => $data['end_time'],
                'day_type' => 'workday',
                'work_description' => $data['work_description'] ?? $data['reason'],
            ]];
            $config = HrisSetting::resolve($employee->company_id, 'hris.overtime_rounding', ['mode' => 'down', 'interval_minutes' => 30]);
            $interval = max(1, (int) ($config['interval_minutes'] ?? 30));
            $normalized = collect($entries)->values()->map(function (array $entry, int $index) use ($config, $interval): array {
                $start = CarbonImmutable::createFromFormat('H:i', $entry['start_time']);
                $end = CarbonImmutable::createFromFormat('H:i', $entry['end_time']);
                if ($end->lessThanOrEqualTo($start)) throw ValidationException::withMessages(["entries.{$index}.end_time" => 'Jam selesai harus lebih besar dari jam mulai. Lembur yang melewati tengah malam harus dibuat sebagai rincian dengan tanggal berikutnya.']);
                $minutes = $start->diffInMinutes($end);
                $minutes = ($config['mode'] ?? 'down') === 'up' ? (int) (ceil($minutes / $interval) * $interval) : (int) (floor($minutes / $interval) * $interval);
                if ($minutes <= 0) throw ValidationException::withMessages(["entries.{$index}.end_time" => 'Durasi setelah pembulatan harus lebih dari 0 menit.']);
                return [...$entry, 'duration_minutes' => $minutes, 'sort_order' => $index];
            });
            $minutes = (int) $normalized->sum('duration_minutes');
            $maximum = HrisSetting::resolve($employee->company_id, 'hris.overtime_maximum_minutes');
            if ($maximum !== null && $minutes > (int) $maximum) throw ValidationException::withMessages(['entries' => "Total durasi lembur setelah pembulatan tidak boleh melebihi {$maximum} menit."]);
            $first = $normalized->first();
            $revisionRequestedBy = (int) ($request->revision_requested_by ?: 0);
            $revisionRequestedRole = $request->revision_requested_role;
            $revisionType = $request->revision_type;
            $returnsToHr = $resubmission && $revisionType === 'administrative' && $revisionRequestedRole === 'hr';
            $status = $draft ? 'draft' : ($returnsToHr ? 'pending_hr' : 'pending_manager');
            $requestNumber = $draft
                ? null
                : (($request->status === 'draft' || blank($request->request_number))
                    ? $this->nextUniqueRequestNumber((int) $employee->company_id, $request)
                    : $request->request_number);
            $request->fill([
                'request_number' => $requestNumber,
                'employee_id' => $employee->id, 'company_id' => $employee->company_id,
                ...$reference,
                'work_date' => $first['work_date'], 'start_time' => $first['start_time'], 'end_time' => $first['end_time'],
                'duration_minutes' => $minutes, 'reason' => $data['reason'] ?? ($first['work_description'] ?? 'Pengajuan lembur'), 'overtime_type' => $data['overtime_type'] ?? 'internal',
                'work_location' => $data['work_location'] ?? null, 'organization_unit_name' => $employee->organizationUnit?->name,
                'job_title_snapshot' => $employee->job_title, 'created_by' => $request->created_by ?: $actorId,
                'requester_user_id' => $request->requester_user_id ?: $actorId, 'status' => $status,
                'revision_requested_by' => null, 'revision_requested_role' => null, 'revision_type' => null,
                'revision_reason' => null, 'revision_requested_at' => null,
            ])->save();
            $request->entries()->delete();
            foreach ($normalized as $entry) $request->entries()->create($entry);
            if ($resubmission) \App\Modules\HRIS\Domain\Models\ApprovalEvent::create(['request_type' => 'overtime', 'request_id' => $request->getKey(), 'step' => 'employee', 'action' => 'resubmit', 'actor_id' => $actorId, 'comment' => null, 'created_at' => now('UTC')]);
            if ($draft) return $request;
            $workflow = app(HrisWorkflowService::class);
            $workflow->notify($actorId, $resubmission ? 'Revisi pengajuan lembur ['.$request->request_number.'] terkirim' : 'Pengajuan lembur ['.$request->request_number.'] terkirim', $resubmission ? 'Revisi pengajuan lembur nomor '.$request->request_number.' berhasil dikirim untuk pemeriksaan ulang.' : 'Pengajuan lembur nomor '.$request->request_number.' berhasil dikirim.', ['request_type' => 'overtime', 'request_id' => $request->id, 'status' => $request->status, 'action' => $resubmission ? 'resubmit' : 'submit']);
            if ($returnsToHr && $revisionRequestedBy > 0) {
                $workflow->notify($revisionRequestedBy, 'Revisi pengajuan lembur menunggu pemeriksaan', 'Revisi pengajuan lembur nomor '.$request->request_number.' dari '.$employee->full_name.' telah dikirim kembali untuk pemeriksaan Anda sebagai HR.', ['request_type' => 'overtime', 'request_id' => $request->id, 'status' => $request->status, 'action' => 'pending_approval', 'approval' => true, 'step' => 'hr', 'actor_id' => $actorId]);
            } else {
                $managerUserId = $revisionRequestedRole === 'manager' && $revisionRequestedBy > 0 ? $revisionRequestedBy : $employee->manager->user_id;
                $workflow->notify($managerUserId, $resubmission ? 'Revisi pengajuan lembur menunggu persetujuan' : 'Pengajuan lembur menunggu persetujuan', $resubmission ? 'Revisi pengajuan lembur nomor '.$request->request_number.' dari '.$employee->full_name.' telah dikirim kembali untuk pemeriksaan Anda sebagai Atasan.' : 'Pengajuan lembur nomor '.$request->request_number.' dari '.$employee->full_name.' menunggu persetujuan Anda sebagai Atasan.', ['request_type' => 'overtime', 'request_id' => $request->id, 'action' => 'pending_approval', 'approval' => true, 'step' => 'manager', 'actor_id' => $actorId]);
            }
            return $request;
        });
    }

    private function nextUniqueRequestNumber(int $companyId, OvertimeRequest $currentRequest): string
    {
        do {
            $candidate = app(NumberingService::class)->next($companyId, 'hris_overtime');
        } while (OvertimeRequest::query()
            ->where('request_number', $candidate)
            ->where('id', '!=', $currentRequest->getKey())
            ->exists());

        return $candidate;
    }
}
