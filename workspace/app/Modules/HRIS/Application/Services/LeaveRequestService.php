<?php

namespace App\Modules\HRIS\Application\Services;

use App\Modules\HRIS\Domain\Exceptions\OverlappingLeaveRequest;
use App\Modules\HRIS\Domain\Models\Employee;
use App\Modules\HRIS\Domain\Models\HrisSetting;
use App\Modules\HRIS\Domain\Models\HrisWorkflowApprover;
use App\Modules\HRIS\Domain\Models\ApprovalEvent;
use App\Modules\HRIS\Domain\Models\LeaveRequest;
use App\Modules\HRIS\Domain\Models\LeaveRequestDate;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LeaveRequestService
{
    public function submit(Employee $employee, array $data, int $actorId): LeaveRequest
    {
        return DB::connection('hris')->transaction(function () use ($employee, $data, $actorId): LeaveRequest {
            $rawDates = $data['dates'] ?? [];
            $dates = collect($rawDates)->map(fn (string $date): string => CarbonImmutable::parse($date)->toDateString())->unique()->sort()->values();
            if ($dates->isEmpty()) throw ValidationException::withMessages(['dates' => 'Pilih minimal satu tanggal pengajuan.']);
            $parsedDates = $dates->map(fn (string $date): CarbonImmutable => CarbonImmutable::parse($date));
            $days = $dates->count();
            $year = $parsedDates->first()->year;
            if ($parsedDates->contains(fn (CarbonImmutable $date): bool => $date->year !== $year)) {
                throw ValidationException::withMessages(['dates' => 'Tanggal pengajuan harus berada pada tahun yang sama.']);
            }

            $dateOverlap = LeaveRequestDate::query()
                ->whereIn('leave_date', $dates->all())
                ->whereHas('leaveRequest', fn ($query) => $query->where('employee_id', $employee->getKey())->active())
                ->exists();
            if ($dateOverlap) throw new OverlappingLeaveRequest($this->overlapMessage($dates->all(), $employee->getKey()));

            $hrApproverIds = HrisWorkflowApprover::approverIds((int) $employee->company_id, 'leave_sick', 'hr');
            if ($hrApproverIds === []) {
                throw ValidationException::withMessages(['type' => 'Approver HR belum dikonfigurasi untuk perusahaan karyawan ini. Hubungi administrator HRIS.']);
            }

            if (($data['type'] ?? null) === 'leave') {
                $this->validateSubmissionCutoff($employee, $year);
                $quotaSetting = HrisSetting::resolveForYear($employee->company_id, 'hris.annual_leave_quota', $year);
                if ($quotaSetting === null) throw ValidationException::withMessages(['dates' => "Kuota cuti untuk tahun {$year} belum dikonfigurasi. Hubungi administrator HRIS."]);
                $quota = (int) $quotaSetting;
                $used = (int) LeaveRequestDate::query()->whereYear('leave_date', $year)->whereHas('leaveRequest', fn ($query) => $query->where('employee_id', $employee->getKey())->where('type', 'leave')->where('status', 'approved'))->count();
                if ($used + $days > $quota) throw ValidationException::withMessages(['dates' => "Pengajuan melebihi sisa kuota cuti tahun {$year}."]);
            }

            $request = LeaveRequest::create(array_merge($data, [
                'company_id' => $employee->company_id,
                'employee_id' => $employee->getKey(),
                'total_days' => $days,
                'requester_user_id' => $actorId,
                'status' => 'pending_manager',
                'created_by' => $actorId,
                'submitted_at' => now(),
            ]));
            $request->dates()->createMany($dates->map(fn (string $date): array => ['leave_date' => $date])->all());
            app(HrisWorkflowService::class)->notify($actorId, 'Pengajuan cuti/sakit terkirim', 'Pengajuan Anda berhasil dikirim dan menunggu pemeriksaan atasan.', ['request_type' => 'leave', 'request_id' => $request->id, 'status' => $request->status]);
            if ($employee->manager?->user_id) app(HrisWorkflowService::class)->notify($employee->manager->user_id, 'Pengajuan '.($request->type === 'sick' ? 'sakit' : 'cuti').' baru', ($request->type === 'sick' ? 'Pengajuan sakit' : 'Pengajuan cuti').' dari '.$employee->full_name.' menunggu persetujuan Anda sebagai Atasan.', ['request_type' => 'leave', 'request_id' => $request->id, 'action' => 'pending_approval', 'approval' => true, 'step' => 'manager', 'actor_id' => $actorId]);
            return $request;
        });
    }

    public function resubmit(LeaveRequest $request, Employee $employee, array $data, int $actorId): LeaveRequest
    {
        return DB::connection('hris')->transaction(function () use ($request, $employee, $data, $actorId): LeaveRequest {
            if ($request->status !== 'needs_revision') {
                throw ValidationException::withMessages(['status' => 'Pengajuan ini tidak sedang menunggu revisi.']);
            }
            if (($data['type'] ?? null) !== $request->type) {
                throw ValidationException::withMessages(['type' => 'Jenis pengajuan tidak dapat diubah saat revisi.']);
            }
            $rawDates = $data['dates'] ?? [];
            $dates = collect($rawDates)->map(fn (string $date): string => CarbonImmutable::parse($date)->toDateString())->unique()->sort()->values();
            $parsedDates = $dates->map(fn (string $date): CarbonImmutable => CarbonImmutable::parse($date));
            if ($dates->isEmpty()) throw ValidationException::withMessages(['dates' => 'Pilih minimal satu tanggal pengajuan.']);
            if ($parsedDates->contains(fn (CarbonImmutable $date): bool => $date->year !== $parsedDates->first()->year)) {
                throw ValidationException::withMessages(['dates' => 'Tanggal pengajuan harus berada pada tahun yang sama.']);
            }
            $overlap = LeaveRequestDate::query()->whereIn('leave_date', $dates->all())->where('leave_request_id', '!=', $request->getKey())->whereHas('leaveRequest', fn ($q) => $q->where('employee_id', $employee->getKey())->active())->exists();
            if ($overlap) throw new OverlappingLeaveRequest($this->overlapMessage($dates->all(), $employee->getKey(), $request->getKey()));
            if ($request->type === 'leave') {
                $year = $parsedDates->first()->year;
                $this->validateSubmissionCutoff($employee, $year);
                $quotaSetting = HrisSetting::resolveForYear($employee->company_id, 'hris.annual_leave_quota', $year);
                if ($quotaSetting === null) throw ValidationException::withMessages(['dates' => "Kuota cuti untuk tahun {$year} belum dikonfigurasi. Hubungi administrator HRIS."]);
                $quota = (int) $quotaSetting;
                $used = (int) LeaveRequestDate::query()->whereYear('leave_date', $year)->where('leave_request_id', '!=', $request->getKey())->whereHas('leaveRequest', fn ($q) => $q->where('employee_id', $employee->getKey())->where('type', 'leave')->where('status', 'approved'))->count();
                if ($used + $dates->count() > $quota) throw ValidationException::withMessages(['dates' => 'Pengajuan melebihi sisa kuota cuti.']);
            }
            $request->dates()->delete();
            $request->dates()->createMany($dates->map(fn (string $date): array => ['leave_date' => $date])->all());
            $nextStatus = $request->revision_type === 'administrative'
                ? ($request->revision_requested_role === 'hr' ? 'pending_hr' : 'pending_manager')
                : 'pending_manager';
            $resetApproval = $nextStatus === 'pending_manager' ? ['manager_approved_by' => null, 'manager_approved_at' => null] : [];
            $attributes = $data;
            unset($attributes['dates']);
            $request->forceFill(array_merge($attributes, $resetApproval, [
                'status' => $nextStatus,
                'total_days' => $dates->count(),
                'revision_submitted_at' => now(),
                'revision_number' => ((int) $request->revision_number) + 1,
                'hr_approved_by' => null,
                'hr_approved_at' => null,
                'decision_reason' => null,
                'revision_requested_by' => null,
                'revision_requested_role' => null,
                'revision_type' => null,
                'revision_reason' => null,
                'revision_requested_at' => null,
            ]))->save();
            ApprovalEvent::create(['request_type' => 'leave', 'request_id' => $request->getKey(), 'step' => 'requester', 'action' => 'resubmitted', 'actor_id' => $actorId, 'comment' => 'Pengajuan hasil revisi dikirim ulang.']);
            if ($nextStatus === 'pending_manager' && $employee->manager?->user_id) app(HrisWorkflowService::class)->notify($employee->manager->user_id, 'Pengajuan cuti/sakit hasil revisi', 'Pengajuan hasil revisi menunggu verifikasi ulang.', ['request_type' => 'leave', 'request_id' => $request->id, 'approval' => true, 'step' => 'manager']);
            if ($nextStatus === 'pending_hr') foreach (HrisWorkflowApprover::approverIds((int) $employee->company_id, 'leave_sick', 'hr') as $approverId) app(HrisWorkflowService::class)->notify($approverId, 'Pengajuan cuti/sakit hasil revisi', 'Pengajuan administrasi hasil revisi menunggu pemeriksaan HR.', ['request_type' => 'leave', 'request_id' => $request->id, 'approval' => true, 'step' => 'hr']);
            return $request;
        });
    }

    private function validateSubmissionCutoff(Employee $employee, int $year): void
    {
        $cutoff = HrisSetting::resolveLeaveSubmissionCutoff($employee->company_id, $year);
        if ($cutoff === null) {
            throw ValidationException::withMessages(['dates' => "Batas akhir pengajuan cuti untuk tahun {$year} belum dikonfigurasi. Hubungi administrator HRIS."]);
        }
        if (now(config('app.timezone', 'Asia/Jakarta'))->greaterThan($cutoff)) {
            throw ValidationException::withMessages(['dates' => "Batas pengajuan cuti untuk tahun {$year} telah lewat pada {$cutoff->format('d/m/Y')}."]);
        }
    }

    /**
     * Explain whether each conflicting date is already approved or still in
     * the approval process, so the requester knows why the revision is blocked.
     */
    private function overlapMessage(array $dates, string|int $employeeId, ?string $ignoreRequestId = null): string
    {
        $conflicts = LeaveRequestDate::query()
            ->with('leaveRequest')
            ->whereIn('leave_date', $dates)
            ->when($ignoreRequestId, fn ($query) => $query->where('leave_request_id', '!=', $ignoreRequestId))
            ->whereHas('leaveRequest', fn ($query) => $query->where('employee_id', $employeeId)->active())
            ->get()
            ->groupBy(fn (LeaveRequestDate $date): string => (string) $date->leaveRequest?->status);

        $messages = [];
        $statusLabels = [
            'approved' => 'sudah digunakan oleh pengajuan yang telah disetujui',
            'pending_manager' => 'sedang dalam proses dan menunggu persetujuan atasan',
            'pending_hr' => 'sedang dalam proses pemeriksaan HR',
        ];

        foreach ($statusLabels as $status => $label) {
            $statusDates = $conflicts->get($status, collect())
                ->map(fn (LeaveRequestDate $date): string => $date->leave_date?->format('d/m/Y') ?? '-')
                ->unique()
                ->sort()
                ->values();
            if ($statusDates->isNotEmpty()) {
                $messages[] = 'Tanggal '. $statusDates->implode(', ') .' '. $label . '.';
            }
        }

        return $messages !== []
            ? implode(' ', $messages)
            : 'Tanggal beririsan dengan pengajuan aktif karyawan.';
    }
}
