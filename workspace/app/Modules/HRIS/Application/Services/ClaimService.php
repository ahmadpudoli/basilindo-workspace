<?php

namespace App\Modules\HRIS\Application\Services;

use App\Modules\HRIS\Domain\Models\Claim;
use App\Modules\HRIS\Domain\Models\ClaimExpenseItem;
use App\Modules\HRIS\Domain\Models\Employee;
use App\Modules\HRIS\Domain\Models\HrisSetting;
use App\Modules\HRIS\Domain\Models\HrisWorkflowApprover;
use App\Modules\HRIS\Domain\Models\HrisExpenseCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Core\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Modules\Core\Application\Services\NumberingService;
use Throwable;

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
        $existingItems = $claim->expenseItems()->orderBy('line_number')->get();
        $oldKeys = $existingItems->pluck('attachment_object_key')->filter()->values()->all();
        foreach ($data['items'] as $index => &$item) {
            if (blank($item['attachment_token'] ?? null) && ! filter_var($item['remove_attachment'] ?? false, FILTER_VALIDATE_BOOLEAN)) {
                $item['attachment_object_key'] = $existingItems->get($index)?->attachment_object_key;
            }
        }
        unset($item);

        $archivedCategoryIds = $existingItems->pluck('expense_category_id')->filter()->all();
        $updated = $this->persist($employee, $data, $actorId, true, $archivedCategoryIds);
        DB::connection('hris')->transaction(function () use ($claim): void {
            $claim->expenseItems()->delete();
            $claim->delete();
        });

        $newKeys = $updated->expenseItems()->pluck('attachment_object_key')->filter()->all();
        $obsoleteKeys = array_values(array_diff($oldKeys, $newKeys));
        if ($obsoleteKeys !== []) Storage::disk((string) config('hris.claim_attachment_disk', 's3'))->delete($obsoleteKeys);

        return $updated;
    }

    public function submitDraft(Claim $claim, Employee $employee, array $data, int $actorId): Claim
    {
        $oldKeys = $claim->expenseItems()->pluck('attachment_object_key')->filter()->values()->all();
        $existingItems = $claim->expenseItems()->orderBy('line_number')->get();
        foreach ($data['items'] as $index => &$item) {
            if (blank($item['attachment_token'] ?? null) && ! filter_var($item['remove_attachment'] ?? false, FILTER_VALIDATE_BOOLEAN)) {
                $item['attachment_object_key'] = $existingItems->get($index)?->attachment_object_key;
            }
        }
        unset($item);
        $submitted = $this->submit($employee, $data, $actorId);
        $claim->expenseItems()->delete();
        $claim->delete();
        $newKeys = $submitted->expenseItems()->pluck('attachment_object_key')->filter()->all();
        $obsoleteKeys = array_values(array_diff($oldKeys, $newKeys));
        if ($obsoleteKeys !== []) Storage::disk((string) config('hris.claim_attachment_disk', 's3'))->delete($obsoleteKeys);
        return $submitted;
    }

    public function resubmit(Claim $claim, Employee $employee, array $data, int $actorId): Claim
    {
        if ($claim->status !== 'needs_revision' || (string) $claim->employee_id !== (string) $employee->getKey()) {
            throw ValidationException::withMessages(['request' => 'Pengajuan tidak sedang menunggu revisi atau bukan milik Anda.']);
        }

        $data = $this->prepareRevisionItems($claim, $data);

        return $this->persist($employee, $data, $actorId, false, $claim->expenseItems()->pluck('expense_category_id')->filter()->all(), $claim, true);
    }

    public function saveRevisionDraft(Claim $claim, Employee $employee, array $data, int $actorId): Claim
    {
        if ($claim->status !== 'needs_revision' || (string) $claim->employee_id !== (string) $employee->getKey()) {
            throw ValidationException::withMessages(['request' => 'Pengajuan tidak sedang menunggu revisi atau bukan milik Anda.']);
        }

        $data = $this->prepareRevisionItems($claim, $data);

        return $this->persist($employee, $data, $actorId, false, $claim->expenseItems()->pluck('expense_category_id')->filter()->all(), $claim, false, true);
    }

    private function prepareRevisionItems(Claim $claim, array $data): array
    {
        $existingItems = $claim->expenseItems()->orderBy('line_number')->get()->keyBy(fn (ClaimExpenseItem $item): string => (string) $item->getKey());
        if (! isset($data['items']) || ! is_array($data['items'])) {
            $firstItem = $existingItems->first();
            $data['items'] = [[
                'id' => $firstItem?->getKey(), 'category' => $data['category'] ?? $firstItem?->category ?? 'Lain-lain',
                'expense_category_id' => $firstItem?->expense_category_id,
                'description' => $data['purpose'] ?? $firstItem?->description ?? '',
                'currency' => $data['currency'] ?? $firstItem?->currency ?? 'IDR',
                'amount' => $data['amount'] ?? $firstItem?->amount ?? 0,
                'attachment_reference' => $firstItem?->attachment_reference,
            ]];
        }
        foreach ($data['items'] as &$item) {
            $itemId = trim((string) ($item['id'] ?? ''));
            if ($itemId !== '' && ! $existingItems->has($itemId)) throw ValidationException::withMessages(['items' => 'Rincian biaya tidak sesuai dengan pengajuan ini.']);
            if (blank($item['attachment_token'] ?? null) && ! filter_var($item['remove_attachment'] ?? false, FILTER_VALIDATE_BOOLEAN)) {
                $item['attachment_object_key'] = $existingItems->get($itemId)?->attachment_object_key;
            }
        }
        unset($item);

        return $data;
    }

    private function persist(Employee $employee, array $data, int $actorId, bool $draft, array $archivedCategoryIds = [], ?Claim $existingClaim = null, bool $resubmission = false, bool $revisionDraft = false): Claim
    {
        if (($data['type'] ?? null) === 'cash_advance') {
            throw ValidationException::withMessages(['type' => 'Cash advance akan tersedia pada tahap pengembangan berikutnya.']);
        }
        $items = collect($data['items'] ?? [[
            'category' => $data['category'] ?? 'Lainnya',
            'description' => $data['purpose'] ?? '',
            'currency' => $data['currency'] ?? 'IDR',
            'amount' => $data['amount'] ?? 0,
            'attachment_reference' => $data['supporting_document_id'] ?? null,
        ]])->values()->map(function (array $item, int $index) use ($archivedCategoryIds): array {
            $categoryId = $item['expense_category_id'] ?? null;
            $categoryQuery = HrisExpenseCategory::query()->whereKey($categoryId);
            if (! in_array((string) $categoryId, array_map('strval', $archivedCategoryIds), true)) $categoryQuery->active();
            $category = $categoryQuery->first();
            if (! $category && filled($item['category'] ?? null)) {
                $category = HrisExpenseCategory::query()->active()->where('name', trim((string) $item['category']))->first();
            }
            if (! $category) throw ValidationException::withMessages(["items.{$index}.expense_category_id" => 'Pilih kategori biaya yang masih aktif.']);
            $item['expense_category_id'] = $category->getKey();
            $item['category'] = $category->name;
            return $item;
        });
        $currencies = $items->pluck('currency')->unique();
        $amount = $items->sum(fn (array $item): float => (float) $item['amount']);
        if ($currencies->count() !== 1) throw ValidationException::withMessages(['items' => 'Satu pengajuan hanya dapat menggunakan satu mata uang.']);
        if ($amount <= 0) throw ValidationException::withMessages(['items' => 'Total nominal harus lebih besar dari nol.']);
        if (($data['expense_scope'] ?? 'internal') !== 'external_project') {
            $data['work_reference_type'] = 'none';
            $data['project_id'] = null;
            $data['opportunity_id'] = null;
        } elseif (($data['work_reference_type'] ?? 'none') === 'project') {
            $user = User::findOrFail($actorId);
            $project = app(OvertimeProjectService::class)->available($employee, $user)->whereKey($data['project_id'] ?? null)->first();
            if (! $project) throw ValidationException::withMessages(['project_id' => 'Project tidak berada dalam scope perusahaan karyawan.']);
            $data['reference_name_snapshot'] = $project->name;
            $data['reference_status_snapshot'] = 'Project';
            $data['opportunity_id'] = null;
        } elseif (($data['work_reference_type'] ?? 'none') === 'opportunity') {
            $user = User::findOrFail($actorId);
            $opportunity = app(OvertimeWorkReferenceService::class)->availableOpportunities($employee, $user)->whereKey($data['opportunity_id'] ?? null)->first();
            if (! $opportunity || Gate::forUser($user)->denies('view', $opportunity)) throw ValidationException::withMessages(['opportunity_id' => 'Opportunity tidak tersedia atau tidak dapat Anda akses.']);
            $data['reference_name_snapshot'] = $opportunity->name;
            $data['reference_status_snapshot'] = 'Opportunity';
            $data['project_id'] = null;
        } else {
            $data['project_id'] = null;
            $data['opportunity_id'] = null;
        }
        if (! empty($data['client_id']) && ! \Core\Models\Company::query()->whereKey($data['client_id'])->exists()) throw ValidationException::withMessages(['client_id' => 'Client tidak ditemukan.']);
        $disk = Storage::disk((string) config('hris.claim_attachment_disk', 's3'));
        $temporaryKeys = [];
        $promotedKeys = [];
        $nextStatus = $existingClaim && $existingClaim->revision_type === 'administrative' && $existingClaim->revision_requested_role === 'finance' ? 'pending_finance' : 'pending_manager';
        try {
            $claim = DB::connection('hris')->transaction(function () use ($employee, $data, $actorId, $draft, $items, $disk, &$temporaryKeys, &$promotedKeys, $existingClaim, $resubmission, $revisionDraft, $nextStatus): Claim {
            $currency = (string) $items->first()['currency'];
            $amount = $items->sum(fn (array $item): float => (float) $item['amount']);
            $claim = $existingClaim ?: new Claim;
            if ($resubmission) {
                $claimSnapshot = $existingClaim->only(['request_number', 'employee_id', 'company_id', 'type', 'expense_scope', 'work_reference_type', 'status', 'category', 'purpose', 'amount', 'currency', 'supporting_document_id', 'department', 'project_id', 'opportunity_id', 'client_id', 'po_number', 'reference_name_snapshot', 'reference_status_snapshot']);
                $itemSnapshots = $existingClaim->expenseItems()->orderBy('line_number')->get()->map(fn (ClaimExpenseItem $item): array => $item->only(['id', 'line_number', 'receipt_date', 'activity_date', 'expense_category_id', 'category', 'description', 'currency', 'amount', 'attachment_reference', 'attachment_object_key']))->all();
                DB::connection('hris')->table('claim_revision_snapshots')->insert([
                    'claim_id' => $existingClaim->getKey(), 'revision_number' => ((int) $existingClaim->revision_number) + 1,
                    'actor_id' => (int) $existingClaim->revision_requested_by, 'requested_role' => (string) $existingClaim->revision_requested_role,
                    'revision_type' => (string) $existingClaim->revision_type, 'revision_reason' => (string) $existingClaim->revision_reason,
                    'claim_snapshot' => json_encode($claimSnapshot, JSON_THROW_ON_ERROR), 'expense_items_snapshot' => json_encode($itemSnapshots, JSON_THROW_ON_ERROR),
                ]);
            }
            $claim->fill(array_merge($data, [
                'request_number' => $existingClaim?->request_number ?? ($draft ? null : app(NumberingService::class)->next((int) $employee->company_id, 'hris_claim')),
                'employee_id' => $employee->id, 'company_id' => $employee->company_id,
                'created_by' => $existingClaim?->created_by ?: $actorId,
                'requester_user_id' => $existingClaim?->requester_user_id ?: $actorId,
                'status' => $revisionDraft ? 'needs_revision' : ($resubmission ? $nextStatus : ($draft ? 'draft' : 'pending_manager')),
                'submitted_at' => $existingClaim?->submitted_at ?: ($draft ? null : now()),
                'category' => $items->first()['category'],
                'purpose' => $data['purpose'] ?? $items->first()['description'],
                'amount' => $amount, 'currency' => $currency,
            ]))->save();
            if ($resubmission) {
                $claim->forceFill([
                    'revision_requested_by' => null, 'revision_requested_role' => null, 'revision_type' => null,
                    'revision_reason' => null, 'revision_requested_at' => null, 'revision_submitted_at' => now(),
                    'revision_number' => ((int) $claim->revision_number) + 1, 'decision_reason' => null,
                    'manager_approved_by' => $nextStatus === 'pending_manager' ? null : $claim->manager_approved_by,
                    'manager_approved_at' => $nextStatus === 'pending_manager' ? null : $claim->manager_approved_at,
                    'finance_approved_by' => null, 'finance_approved_at' => null,
                    'final_approved_by' => null, 'final_approved_at' => null,
                ])->save();
            }
            $activeExistingItems = ($resubmission || $revisionDraft) ? $existingClaim->expenseItems()->get()->keyBy(fn (ClaimExpenseItem $existingItem): string => (string) $existingItem->getKey()) : collect();
            $retainedItemIds = [];
            foreach ($items as $index => $item) {
                $itemId = trim((string) ($item['id'] ?? ''));
                $token = trim((string) ($item['attachment_token'] ?? ''));
                if ($token !== '') {
                    $temporaryKey = 'hris-claim-tmp/'.$actorId.'/'.$token;
                    $permanentKey = 'hris-claims/'.$employee->company_id.'/'.$claim->id.'/receipts/'.(string) Str::ulid();
                    if (! $disk->copy($temporaryKey, $permanentKey)) {
                        throw ValidationException::withMessages(["items.{$index}.attachment_token" => 'File bukti gagal dipindahkan ke penyimpanan klaim. Silakan coba lagi.']);
                    }
                    $promotedKeys[] = $permanentKey;
                    $temporaryKeys[] = $temporaryKey;
                    $item['attachment_object_key'] = $permanentKey;
                }
                unset($item['id']);
                unset($item['attachment_token']);
                unset($item['remove_attachment']);
                $itemData = array_merge($item, ['claim_id' => $claim->id, 'line_number' => $index + 1, 'is_active' => true]);
                if (($resubmission || $revisionDraft) && $itemId !== '' && $activeExistingItems->has($itemId)) {
                    $existingItem = $activeExistingItems->get($itemId);
                    $existingItem->fill($itemData)->save();
                    $retainedItemIds[] = $itemId;
                } else {
                    ClaimExpenseItem::create($itemData);
                }
            }
            if ($resubmission || $revisionDraft) $activeExistingItems->reject(fn (ClaimExpenseItem $item): bool => in_array((string) $item->getKey(), $retainedItemIds, true))->each(fn (ClaimExpenseItem $item) => $item->forceFill(['is_active' => false])->save());
            if ($draft || $revisionDraft) return $claim;
            $label = $claim->type === 'cash_advance' ? 'cash advance' : 'reimbursement';
            $workflow = app(HrisWorkflowService::class);
            $workflow->notify($actorId, ($resubmission ? 'Revisi pengajuan ' : 'Pengajuan ').$label.' ['.$claim->request_number.'] terkirim', ($resubmission ? 'Revisi pengajuan ' : 'Pengajuan ').$label.' nomor '.$claim->request_number.' berhasil dikirim.', ['request_type' => 'claim', 'request_id' => $claim->id, 'status' => $claim->status, 'action' => $resubmission ? 'resubmitted' : 'submit']);
            if ($resubmission && $nextStatus === 'pending_finance') {
                foreach (HrisWorkflowApprover::approverIds((int) $claim->company_id, 'claim', 'finance') as $approverId) $workflow->notify($approverId, 'Revisi reimbursement menunggu pemeriksaan Finance', 'Revisi reimbursement nomor '.$claim->request_number.' dari '.$employee->full_name.' telah dikirim kembali untuk pemeriksaan Finance.', ['request_type' => 'claim', 'request_id' => $claim->id, 'status' => $claim->status, 'action' => 'pending_approval', 'approval' => true, 'step' => 'finance', 'actor_id' => $actorId]);
            } elseif ($employee->manager?->user_id) {
                $workflow->notify($employee->manager->user_id, $resubmission ? 'Revisi reimbursement menunggu persetujuan Atasan' : 'Pengajuan '.$label.' ['.$claim->request_number.'] baru', 'Pengajuan '.$label.' nomor '.$claim->request_number.' dari '.$employee->full_name.' menunggu persetujuan Anda sebagai Atasan.', ['request_type' => 'claim', 'request_id' => $claim->id, 'action' => 'pending_approval', 'approval' => true, 'step' => 'manager', 'actor_id' => $actorId]);
            }
            if ($resubmission) \App\Modules\HRIS\Domain\Models\ApprovalEvent::create(['request_type' => 'claim', 'request_id' => $claim->getKey(), 'step' => 'requester', 'action' => 'resubmitted', 'actor_id' => $actorId, 'comment' => 'Pengajuan hasil revisi dikirim ulang.', 'created_at' => now('UTC')]);
            return $claim;
            });
        } catch (Throwable $exception) {
            if ($promotedKeys !== []) $disk->delete($promotedKeys);
            throw $exception;
        }

        if ($temporaryKeys !== []) $disk->delete($temporaryKeys);

        return $claim;
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
