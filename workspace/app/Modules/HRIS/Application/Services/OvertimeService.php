<?php

namespace App\Modules\HRIS\Application\Services;

use App\Modules\HRIS\Domain\Models\Employee;
use App\Modules\HRIS\Domain\Models\HrisSetting;
use App\Modules\HRIS\Domain\Models\OvertimeRequest;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OvertimeService
{
    public function submit(Employee $employee, array $data, int $actorId): OvertimeRequest
    {
        return DB::connection('hris')->transaction(function () use ($employee, $data, $actorId): OvertimeRequest {
            $start = CarbonImmutable::createFromFormat('H:i', $data['start_time']);
            $end = CarbonImmutable::createFromFormat('H:i', $data['end_time']);
            if ($end->lessThanOrEqualTo($start)) $end = $end->addDay();
            $minutes = $start->diffInMinutes($end);
            $config = HrisSetting::resolve($employee->company_id, 'hris.overtime_rounding', ['mode' => 'down', 'interval_minutes' => 30]);
            $interval = max(1, (int) ($config['interval_minutes'] ?? 30));
            $minutes = ($config['mode'] ?? 'down') === 'up' ? (int) (ceil($minutes / $interval) * $interval) : (int) (floor($minutes / $interval) * $interval);
            $maximum = HrisSetting::resolve($employee->company_id, 'hris.overtime_maximum_minutes');
            if ($minutes <= 0) throw ValidationException::withMessages(['end_time' => 'Durasi lembur setelah pembulatan harus lebih dari 0 menit.']);
            if ($maximum !== null && $minutes > (int) $maximum) throw ValidationException::withMessages(['end_time' => "Durasi lembur setelah pembulatan tidak boleh melebihi {$maximum} menit."]);
            $request = OvertimeRequest::create(['employee_id' => $employee->id, 'company_id' => $employee->company_id, 'work_date' => $data['work_date'], 'start_time' => $data['start_time'], 'end_time' => $data['end_time'], 'duration_minutes' => $minutes, 'reason' => $data['reason'], 'created_by' => $actorId, 'status' => 'submitted']);
            app(HrisWorkflowService::class)->notify($actorId, 'Pengajuan lembur terkirim', 'Pengajuan lembur Anda berhasil dikirim dan menunggu pemeriksaan atasan.', ['request_type' => 'overtime', 'request_id' => $request->id, 'status' => $request->status]);
        if ($employee->manager?->user_id) app(HrisWorkflowService::class)->notify($employee->manager->user_id, 'Pengajuan lembur baru', 'Pengajuan lembur dari '.$employee->full_name.' menunggu persetujuan Anda sebagai Atasan.', ['request_type' => 'overtime', 'request_id' => $request->id, 'action' => 'pending_approval', 'approval' => true, 'step' => 'manager', 'actor_id' => $actorId]);
            return $request;
        });
    }
}
