<?php

namespace App\Modules\HRIS\Application\Services;

use App\Modules\HRIS\Domain\Models\Claim;
use App\Modules\HRIS\Domain\Models\Employee;
use App\Modules\HRIS\Domain\Models\HrisSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ClaimService
{
    public function submit(Employee $employee, array $data, int $actorId): Claim
    {
        if ((float) $data['amount'] <= 0) throw ValidationException::withMessages(['amount' => 'Nominal harus lebih besar dari nol.']);
        return DB::connection('hris')->transaction(function () use ($employee, $data, $actorId): Claim {
            $claim = Claim::create(array_merge($data, ['employee_id' => $employee->id, 'company_id' => $employee->company_id, 'created_by' => $actorId, 'status' => 'submitted']));
            $label = $claim->type === 'cash_advance' ? 'cash advance' : 'reimbursement';
            app(HrisWorkflowService::class)->notify($actorId, 'Pengajuan '.$label.' ['.$claim->request_number.'] terkirim', 'Pengajuan '.$label.' nomor '.$claim->request_number.' berhasil dikirim.', ['request_type' => 'claim', 'request_id' => $claim->id, 'status' => $claim->status]);
        if ($employee->manager?->user_id) app(HrisWorkflowService::class)->notify($employee->manager->user_id, 'Pengajuan '.$label.' ['.$claim->request_number.'] baru', 'Pengajuan '.$label.' nomor '.$claim->request_number.' dari '.$employee->full_name.' menunggu persetujuan Anda sebagai Atasan.', ['request_type' => 'claim', 'request_id' => $claim->id, 'action' => 'pending_approval', 'approval' => true, 'step' => 'manager', 'actor_id' => $actorId]);
            return $claim;
        });
    }

    public function requiresFinalApproval(Claim $claim): bool
    {
        return (float) $claim->amount >= (float) HrisSetting::resolve($claim->company_id, 'hris.claim_final_approval_threshold', PHP_FLOAT_MAX);
    }
}
