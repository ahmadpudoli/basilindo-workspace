<?php

namespace App\Modules\HRIS\Application\Services;

use App\Modules\HRIS\Domain\Models\Claim;
use App\Modules\HRIS\Domain\Models\ClaimExpenseItem;
use App\Modules\HRIS\Domain\Models\Employee;
use App\Modules\HRIS\Domain\Models\HrisSetting;
use App\Modules\HRIS\Domain\Models\HrisWorkflowApprover;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Core\Models\Project;
use App\Modules\Core\Application\Services\NumberingService;

class ClaimService
{
    public function submit(Employee $employee, array $data, int $actorId): Claim
    {
        return $this->persist($employee, $data, $actorId, false);
    }

    public function saveDraft(Employee $employee, array $data, int $actorId): Claim
    {
        return $this->persist($employee, $data, $actorId, true);
    }

    public function updateDraft(Claim $claim, Employee $employee, array $data, int $actorId): Claim
    {
        $claim->expenseItems()->delete();
        $claim->delete();
        return $this->saveDraft($employee, $data, $actorId);
    }

    public function submitDraft(Claim $claim, Employee $employee, array $data, int $actorId): Claim
    {
        $submitted = $this->submit($employee, $data, $actorId);
        $claim->expenseItems()->delete();
        $claim->delete();
        return $submitted;
    }

    private function persist(Employee $employee, array $data, int $actorId, bool $draft): Claim
    {
        $items = collect($data['items'] ?? [[
            'category' => $data['category'] ?? 'Lainnya',
            'description' => $data['purpose'] ?? '',
            'currency' => $data['currency'] ?? 'IDR',
            'amount' => $data['amount'] ?? 0,
            'attachment_reference' => $data['supporting_document_id'] ?? null,
        ]])->values();
        $currencies = $items->pluck('currency')->unique();
        $amount = $items->sum(fn (array $item): float => (float) $item['amount']);
        if ($currencies->count() !== 1) throw ValidationException::withMessages(['items' => 'Satu pengajuan hanya dapat menggunakan satu mata uang.']);
        if ($amount <= 0) throw ValidationException::withMessages(['items' => 'Total nominal harus lebih besar dari nol.']);
        if (! empty($data['project_id']) && ! Project::query()->whereKey($data['project_id'])->where('company_id', $employee->company_id)->exists()) throw ValidationException::withMessages(['project_id' => 'Project tidak berada dalam scope perusahaan karyawan.']);
        if (! empty($data['client_id']) && ! \Core\Models\Company::query()->whereKey($data['client_id'])->exists()) throw ValidationException::withMessages(['client_id' => 'Client tidak ditemukan.']);
        return DB::connection('hris')->transaction(function () use ($employee, $data, $actorId, $draft): Claim {
            $items = collect($data['items'] ?? [[
                'category' => $data['category'] ?? 'Lainnya',
                'description' => $data['purpose'] ?? '',
                'currency' => $data['currency'] ?? 'IDR',
                'amount' => $data['amount'] ?? 0,
                'attachment_reference' => $data['supporting_document_id'] ?? null,
            ]])->values();
            $currency = (string) $items->first()['currency'];
            $amount = $items->sum(fn (array $item): float => (float) $item['amount']);
            $claim = Claim::create(array_merge($data, [
                'request_number' => $draft ? null : app(NumberingService::class)->next((int) $employee->company_id, 'hris_claim'),
                'employee_id' => $employee->id, 'company_id' => $employee->company_id,
                'created_by' => $actorId, 'status' => $draft ? 'draft' : 'pending_manager',
                'category' => $data['category'] ?? $items->first()['category'],
                'purpose' => $data['purpose'] ?? $items->first()['description'],
                'amount' => $amount, 'currency' => $currency,
            ]));
            foreach ($items as $index => $item) ClaimExpenseItem::create(array_merge($item, ['claim_id' => $claim->id, 'line_number' => $index + 1]));
            if ($draft) return $claim;
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

    public function finalApproverConfigured(Claim $claim): bool
    {
        return HrisWorkflowApprover::query()->forWorkflow((int) $claim->company_id, 'claim', 'final')->exists();
    }
}
