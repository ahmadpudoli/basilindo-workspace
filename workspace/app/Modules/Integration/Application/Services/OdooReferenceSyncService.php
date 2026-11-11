<?php

namespace App\Modules\Integration\Application\Services;

use App\Modules\Integration\Domain\Models\IntegrationSyncRun;
use Core\Models\Company;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class OdooReferenceSyncService
{
    public function __construct(private readonly OdooPostgresDataSource $source) {}

    public function run(bool $full = false): IntegrationSyncRun
    {
        $run = IntegrationSyncRun::create(['source_system' => 'odoo', 'sync_type' => $full ? 'initial' : 'incremental', 'status' => 'running', 'started_at' => now()]);
        $cursor = $full ? null : DB::connection('core')->table('integration_sync_runs')->where('source_system', 'odoo')->where('status', 'completed')->max('finished_at');
        $counts = ['processed_count' => 0, 'created_count' => 0, 'updated_count' => 0, 'failed_count' => 0];
        $activePipelineExternalIds = [];

        try {
            foreach ([
                ['type' => 'res_company', 'rows' => $this->source->changedCompanies($cursor, $full), 'name' => fn ($row) => $row->name],
                ['type' => 'res_partner', 'rows' => $this->source->changedPartners($cursor, $full), 'name' => fn ($row) => $row->name],
                ['type' => 'crm_lead', 'rows' => $this->source->changedOpportunities($cursor, $full), 'name' => fn ($row) => $row->name],
                ['type' => 'project_project', 'rows' => $this->source->changedProjects($cursor, $full), 'name' => fn ($row) => $row->name],
            ] as $group) {
                if ($full && $group['type'] === 'crm_lead') {
                    $activePipelineExternalIds = collect($group['rows'])->pluck('id')->map(fn ($id) => (string) $id)->all();
                }
                foreach ($group['rows'] as $row) {
                    try {
                        $payload = (array) $row;
                        $key = ['source_system' => 'odoo', 'source_database' => $this->source->sourceDatabase(), 'entity_type' => $group['type'], 'external_id' => (string) $row->id];
                        $existing = DB::connection('core')->table('integration_reference_records')->where($key)->exists();
                        $displayName = $this->displayName($group['name']($row));
                        DB::connection('core')->table('integration_reference_records')->updateOrInsert($key, ['display_name' => $displayName, 'source_updated_at_value' => (string) ($row->write_date ?? ''), 'payload' => json_encode($payload, JSON_THROW_ON_ERROR), 'last_synced_at' => now(), 'updated_at' => now(), 'created_at' => now()]);
                        $reference = DB::connection('core')->table('external_references')->where($key)->first();
                        $syncStatus = $reference?->local_entity_type === 'company' && filled($reference->local_id) ? 'mapped' : 'synced';
                        DB::connection('core')->table('external_references')->updateOrInsert($key, ['source_updated_at' => $row->write_date ?? null, 'last_synced_at' => now(), 'sync_status' => $syncStatus, 'metadata' => json_encode(['display_name' => $displayName], JSON_THROW_ON_ERROR), 'updated_at' => now(), 'created_at' => now()]);
                        $this->normalize($group['type'], $row, $displayName);
                        $counts['processed_count']++;
                        $counts[$existing ? 'updated_count' : 'created_count']++;
                    } catch (Throwable $exception) {
                        $counts['failed_count']++;
                        DB::connection('core')->table('integration_sync_errors')->insert(['sync_run_id' => $run->getKey(), 'entity_type' => $group['type'], 'external_id' => (string) ($row->id ?? ''), 'message' => Str::limit($exception->getMessage(), 1000), 'created_at' => now()]);
                    }
                }
            }
            if ($full) {
                $this->reconcileDeletedPipelineItems($activePipelineExternalIds);
            }
            $run->update([...$counts, 'status' => 'completed', 'finished_at' => now(), 'message' => 'Projection referensi Odoo berhasil diperbarui.']);
        } catch (Throwable $exception) {
            $run->update([...$counts, 'status' => 'failed', 'finished_at' => now(), 'message' => Str::limit($exception->getMessage(), 1000)]);
        }
        return $run->fresh();
    }

    private function normalize(string $entityType, object $row, string $displayName): void
    {
        if ($entityType === 'res_partner') {
            $this->normalizePartner($row, $displayName);
        }

        if ($entityType === 'crm_lead') {
            $this->normalizePipelineItem($row, $displayName);
        }

        if ($entityType === 'project_project') {
            $this->normalizeProject($row, $displayName);
        }
    }

    private function normalizePipelineItem(object $row, string $displayName): void
    {
        $companyId = $this->mappedCompanyId($row->company_id ?? null);
        if ($companyId === null) {
            return;
        }

        $crm = DB::connection('crm');
        $externalId = (string) $row->id;
        $reference = DB::connection('core')->table('external_references')->where([
            'source_system' => 'odoo',
            'source_database' => $this->source->sourceDatabase(),
            'entity_type' => 'crm_lead',
            'external_id' => $externalId,
            'local_entity_type' => 'crm_pipeline_item',
        ])->first();
        $accountId = $this->resolveAccountId($row->partner_id ?? null, $companyId);
        $teamId = $this->resolveTeamId($row->team_id ?? null, $row->team_name ?? null, $companyId);
        $recordType = ($row->type ?? 'opportunity') === 'lead' ? 'lead' : 'opportunity';
        $stageCode = $this->stageCode($row->stage_name ?? null, $recordType);
        $stageId = $crm->table('pipeline_stages')->where('code', $stageCode)->value('id');
        $closing = $this->closingState($row, $stageCode);
        $closingStatusId = $closing['code'] ? $crm->table('closing_statuses')->where('code', $closing['code'])->value('id') : null;
        $values = [
            'company_id' => $companyId,
            'account_id' => $accountId,
            'team_id' => $teamId,
            'record_type' => $recordType,
            'name' => $displayName,
            'email' => $row->email_from ?? null,
            'phone' => $row->phone ?? null,
            'source' => 'odoo',
            'status' => $closing['code'],
            'stage' => $stageCode,
            'stage_id' => $stageId,
            'closing_status_id' => $closingStatusId,
            'amount' => $row->expected_revenue ?? null,
            'expected_close_date' => $row->date_deadline ?? null,
            'notes' => $row->description ?? null,
            'lost_reason' => $this->displayName($row->lost_reason ?? null) !== 'Tanpa nama' ? $this->displayName($row->lost_reason ?? null) : null,
            'source_record_status' => 'active',
            'deleted_at' => null,
            'won_at' => $closing['is_won'] ? ($row->date_closed ?? null) : null,
            'closed_at' => $closing['code'] ? ($row->date_closed ?? null) : null,
            'external_source' => 'odoo',
            'external_id' => $externalId,
            ...$this->integrationValues($externalId),
            'updated_at' => now(),
        ];
        if ($teamId === null) unset($values['team_id']);
        // Odoo crm.lead does not provide PO number/document fields. Preserve
        // existing local values during sync and leave them null for new rows.
        if ($reference?->local_id && $crm->table('opportunities')->where('id', $reference->local_id)->exists()) {
            $crm->table('opportunities')->where('id', $reference->local_id)->update($values);
            return;
        }

        $existingItemId = $crm->table('opportunities')->where(['external_source' => 'odoo', 'external_id' => $externalId])->whereNull('deleted_at')->value('id');
        if ($existingItemId) {
            $crm->table('opportunities')->where('id', $existingItemId)->update($values);
            $this->saveLocalReference('crm_lead', $externalId, 'crm_pipeline_item', (string) $existingItemId, $displayName);
            return;
        }

        $localId = (string) Str::uuid();
        $crm->table('opportunities')->insert(array_merge(['id' => $localId, 'created_at' => now()], $values));
        $this->saveLocalReference('crm_lead', $externalId, 'crm_pipeline_item', $localId, $displayName);
    }

    /** @param array<int, string> $activeExternalIds */
    private function reconcileDeletedPipelineItems(array $activeExternalIds): void
    {
        $sourceDatabase = $this->source->sourceDatabase();
        $crm = DB::connection('crm');
        $staleQuery = $crm->table('opportunities')
            ->where('external_source', 'odoo')
            ->where('source_system', 'odoo')
            ->where('source_database', $sourceDatabase)
            ->whereNotNull('external_id')
            ->whereNull('deleted_at');

        if ($activeExternalIds !== []) {
            $staleQuery->whereNotIn('external_id', $activeExternalIds);
        }

        $staleExternalIds = (clone $staleQuery)->pluck('external_id')->map(fn ($id) => (string) $id)->all();
        if ($staleExternalIds === []) {
            return;
        }

        $staleQuery->update([
            'source_record_status' => 'deleted',
            'deleted_at' => now(),
            'updated_at' => now(),
        ]);

        DB::connection('core')->table('external_references')
            ->where('source_system', 'odoo')
            ->where('source_database', $sourceDatabase)
            ->where('entity_type', 'crm_lead')
            ->whereIn('external_id', $staleExternalIds)
            ->update(['sync_status' => 'deleted', 'updated_at' => now()]);
    }

    private function normalizeProject(object $row, string $displayName): void
    {
        $companyId = $this->mappedCompanyId($row->company_id ?? null);
        if ($companyId === null) {
            return;
        }

        $core = DB::connection('core');
        $externalId = (string) $row->id;
        $reference = $core->table('external_references')->where([
            'source_system' => 'odoo',
            'source_database' => $this->source->sourceDatabase(),
            'entity_type' => 'project_project',
            'external_id' => $externalId,
            'local_entity_type' => 'project',
        ])->first();
        $values = ['company_id' => $companyId, 'name' => $displayName, ...$this->integrationValues($externalId), 'updated_at' => now()];
        if ($reference?->local_id && $core->table('projects')->where('id', $reference->local_id)->exists()) {
            $core->table('projects')->where('id', $reference->local_id)->update($values);
            $localId = (int) $reference->local_id;
        } else {
            $localId = (int) $core->table('projects')->insertGetId(array_merge($values, ['created_at' => now()]));
            $this->saveLocalReference('project_project', $externalId, 'project', (string) $localId, $displayName);
        }

        if (! empty($row->opportunity_id)) {
            DB::connection('crm')->table('opportunities')->where(['external_source' => 'odoo', 'external_id' => (string) $row->opportunity_id])->update(['project_id' => $localId, ...$this->integrationValues((string) $row->opportunity_id), 'updated_at' => now()]);
        }
    }

    private function resolveAccountId(?int $partnerId, ?int $companyId): ?string
    {
        if (! $partnerId) {
            return null;
        }

        $core = DB::connection('core');
        $externalId = (string) $partnerId;
        $reference = $core->table('external_references')->where([
            'source_system' => 'odoo',
            'source_database' => $this->source->sourceDatabase(),
            'entity_type' => 'res_partner',
            'external_id' => $externalId,
        ])->first();
        $existingAccountId = $reference?->local_entity_type === 'crm_account' && $reference->local_id
            ? (string) $reference->local_id
            : null;
        if ($reference?->local_entity_type === 'crm_contact' && $reference->local_id) {
            return DB::connection('crm')->table('contacts')->where('id', $reference->local_id)->value('account_id');
        }
        $projection = $core->table('integration_reference_records')->where([
            'source_system' => 'odoo',
            'source_database' => $this->source->sourceDatabase(),
            'entity_type' => 'res_partner',
            'external_id' => $externalId,
        ])->first();
        if (! $projection) {
            return null;
        }

        $payload = json_decode((string) $projection->payload, true) ?: [];
        if (! empty($payload['parent_id'])) {
            return $this->resolveAccountId((int) $payload['parent_id'], $companyId);
        }

        $accountName = $projection->display_name ?: 'Client Odoo '.$externalId;
        $isCompany = (bool) ($payload['is_company'] ?? false);
        $partyCompanyId = $isCompany ? $this->resolveCounterpartyCompany($externalId, $accountName) : null;
        $address = $this->addressValues($payload);
        if ($isCompany && $partyCompanyId !== null) {
            $canonicalAccountId = DB::connection('crm')->table('accounts')
                ->where('party_company_id', $partyCompanyId)
                ->where('account_type', 'company')
                ->whereNull('deleted_at')
                ->value('id');
            if ($canonicalAccountId !== null && (string) $canonicalAccountId !== (string) $existingAccountId) {
                return (string) $canonicalAccountId;
            }
        }
        if ($existingAccountId && $isCompany) {
            $canonicalAccountId = DB::connection('crm')->table('accounts')
                ->where('party_company_id', $partyCompanyId)
                ->where('id', '!=', $existingAccountId)
                ->value('id');
            if ($canonicalAccountId) {
                return (string) $canonicalAccountId;
            }
            DB::connection('crm')->table('accounts')->where('id', $existingAccountId)->update([
                'account_type' => 'company',
                'party_company_id' => $partyCompanyId,
                ...$address,
                ...$this->integrationValues($externalId),
                'updated_at' => now(),
            ]);
            $this->syncCompanyAddress($partyCompanyId, $address);
            return $existingAccountId;
        }
        if ($existingAccountId) {
            return $existingAccountId;
        }
        $accountId = $partyCompanyId
            ? DB::connection('crm')->table('accounts')->where('party_company_id', $partyCompanyId)->value('id')
            : DB::connection('crm')->table('accounts')->where(['company_id' => $companyId, 'name' => $accountName])->value('id');
        if (! $accountId) {
            $accountId = (string) Str::uuid();
            DB::connection('crm')->table('accounts')->insert([
                'id' => $accountId,
                'company_id' => $companyId,
                'party_company_id' => $partyCompanyId,
                'account_type' => $isCompany ? 'company' : 'person',
                'name' => $accountName,
                'status' => null,
                'metadata' => json_encode(['source' => 'odoo', 'external_id' => $externalId], JSON_THROW_ON_ERROR),
                ...$address,
                ...$this->integrationValues($externalId),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            DB::connection('crm')->table('accounts')->where('id', $accountId)->update([
                'account_type' => $isCompany ? 'company' : 'person',
                'party_company_id' => $partyCompanyId,
                ...$address,
                ...$this->integrationValues($externalId),
                'updated_at' => now(),
            ]);
        }
        $this->syncCompanyAddress($partyCompanyId, $address);
        $this->saveLocalReference(
            'res_partner',
            $externalId,
            $isCompany && $partyCompanyId ? 'company' : 'crm_account',
            $isCompany && $partyCompanyId ? (string) $partyCompanyId : $accountId,
            $projection->display_name,
        );
        return $accountId;
    }

    private function resolveCounterpartyCompany(string $externalId, string $displayName): ?int
    {
        $core = DB::connection('core');
        $reference = $core->table('external_references')->where([
            'source_system' => 'odoo',
            'source_database' => $this->source->sourceDatabase(),
            'entity_type' => 'res_partner',
            'external_id' => $externalId,
        ])->first();

        $mappedOperatingCompanyId = $this->mappedCompanyForOdooPartner($externalId);
        if ($mappedOperatingCompanyId !== null) {
            $oldCompanyId = null;
            if ($reference?->local_entity_type === 'company' && $reference->local_id) {
                $oldCompanyId = (int) $reference->local_id;
            } elseif ($reference?->local_entity_type === 'crm_account' && $reference->local_id) {
                $oldCompanyId = DB::connection('crm')->table('accounts')->where('id', $reference->local_id)->value('party_company_id');
            }
            if ($oldCompanyId !== null && (int) $oldCompanyId !== $mappedOperatingCompanyId) {
                $this->repointCounterpartyAccounts((int) $oldCompanyId, $mappedOperatingCompanyId);
            }
            $this->saveLocalReference('res_partner', $externalId, 'company', (string) $mappedOperatingCompanyId, $displayName);
            $this->syncCompanyIntegrationMetadata($mappedOperatingCompanyId, $externalId);
            return $mappedOperatingCompanyId;
        }

        if ($reference?->local_entity_type === 'company' && $reference->local_id) {
            $this->syncCompanyIntegrationMetadata((int) $reference->local_id, $externalId);
            return (int) $reference->local_id;
        }

        $normalized = $this->normalizeCompanyName($displayName);
        if ($normalized === '' || $normalized === 'tanpa nama' || $normalized === '-') {
            return null;
        }

        $candidates = Company::query()
            ->where('entity_type', 'company')
            ->get()
            ->filter(fn (Company $company): bool => $this->normalizeCompanyName($company->name) === $normalized)
            ->values();

        if ($candidates->count() > 1) {
            return null;
        }

        $company = $candidates->first();
        if (! $company) {
            $company = Company::create([
                'name' => $displayName,
                'code' => $this->odooCompanyCode($externalId),
                'entity_type' => 'company',
                'classification' => 'external',
                'roles' => ['client', 'partner'],
                'status' => 'active',
            ]);
        } else {
            $company->update(['roles' => array_values(array_unique(array_merge((array) $company->roles, ['client', 'partner'])))]);
        }

        $this->saveLocalReference('res_partner', $externalId, 'company', (string) $company->getKey(), $displayName);
        $this->syncCompanyIntegrationMetadata((int) $company->getKey(), $externalId);
        return (int) $company->getKey();
    }

    private function mappedCompanyForOdooPartner(string $partnerExternalId): ?int
    {
        $core = DB::connection('core');
        $odooCompanyExternalId = $core->table('integration_reference_records')
            ->where('source_system', 'odoo')
            ->where('source_database', $this->source->sourceDatabase())
            ->where('entity_type', 'res_company')
            ->whereRaw("payload->>'partner_id' = ?", [$partnerExternalId])
            ->value('external_id');

        if ($odooCompanyExternalId === null) {
            return null;
        }

        $localId = $core->table('external_references')->where([
            'source_system' => 'odoo',
            'source_database' => $this->source->sourceDatabase(),
            'entity_type' => 'res_company',
            'external_id' => (string) $odooCompanyExternalId,
            'local_entity_type' => 'company',
        ])->value('local_id');

        return $localId !== null ? (int) $localId : null;
    }

    private function repointCounterpartyAccounts(int $oldCompanyId, int $newCompanyId): void
    {
        if ($oldCompanyId === $newCompanyId) {
            return;
        }

        $crm = DB::connection('crm');
        $canonicalId = $crm->table('accounts')->where('party_company_id', $newCompanyId)->value('id');
        $oldAccounts = $crm->table('accounts')->where('party_company_id', $oldCompanyId)->get(['id']);

        foreach ($oldAccounts as $account) {
            if ($canonicalId && (string) $canonicalId !== (string) $account->id) {
                $crm->table('accounts')->where('id', $account->id)->update(['deleted_at' => now(), 'updated_at' => now()]);
                continue;
            }

            $crm->table('accounts')->where('id', $account->id)->update(['party_company_id' => $newCompanyId, 'updated_at' => now()]);
            $canonicalId = $account->id;
        }

        $oldCompany = Company::query()->find($oldCompanyId);
        if ($oldCompany && str_starts_with((string) $oldCompany->code, 'ODOO-')) {
            $references = DB::connection('core')->table('external_references')->where('local_entity_type', 'company')->where('local_id', (string) $oldCompanyId)->count();
            if ($references <= 1) {
                $oldCompany->delete();
            }
        }
    }

    private function odooCompanyCode(string $externalId): string
    {
        $base = 'ODOO-'.$this->source->sourceDatabase().'-P-'.$externalId;
        $code = substr(preg_replace('/[^A-Z0-9_-]/', '', strtoupper($base)), 0, 50);
        if (! Company::query()->where('code', $code)->exists()) {
            return $code;
        }
        return substr($code, 0, 43).'-'.Str::lower(Str::random(6));
    }

    private function normalizeCompanyName(?string $name): string
    {
        return mb_strtolower(trim(preg_replace('/\s+/', ' ', (string) $name)));
    }

    private function normalizePartner(object $row, string $displayName): void
    {
        $companyId = $this->mappedCompanyId($row->company_id ?? null);
        $address = $this->addressValues($row);
        if ((bool) ($row->is_company ?? false)) {
            $accountId = $this->resolveAccountId((int) $row->id, $companyId);
            $partyCompanyId = $accountId
                ? DB::connection('crm')->table('accounts')->where('id', $accountId)->value('party_company_id')
                : null;
            $this->syncCompanyAddress($partyCompanyId, $address);
            return;
        }

        if (empty($row->parent_id)) {
            // A standalone individual is a CRM account, but not a Company.
            $this->resolveAccountId((int) $row->id, $companyId);
            return;
        }

        $parentReference = DB::connection('core')->table('external_references')->where([
            'source_system' => 'odoo', 'source_database' => $this->source->sourceDatabase(),
            'entity_type' => 'res_partner', 'external_id' => (string) $row->parent_id,
        ])->first();
        $accountId = $parentReference?->local_entity_type === 'crm_account' ? (string) $parentReference->local_id : null;
        if (! $accountId && $parentReference?->local_entity_type === 'company' && $parentReference->local_id) {
            $accountId = DB::connection('crm')->table('accounts')->where('party_company_id', (int) $parentReference->local_id)->value('id');
        }
        if (! $accountId && $companyId !== null) $accountId = $this->resolveAccountId((int) $row->parent_id, $companyId);
        if (! $accountId) return;
        $parentPartyCompanyId = DB::connection('crm')->table('accounts')->where('id', $accountId)->value('party_company_id');
        $core = DB::connection('core');
        $externalId = (string) $row->id;
        $reference = $core->table('external_references')->where(['source_system' => 'odoo', 'source_database' => $this->source->sourceDatabase(), 'entity_type' => 'res_partner', 'external_id' => $externalId])->first();
        $legacyAccountId = $reference?->local_entity_type === 'crm_account' && $reference->local_id
            ? (string) $reference->local_id
            : DB::connection('crm')->table('accounts')
                ->whereRaw("metadata->>'external_id' = ?", [$externalId])
                ->whereNull('deleted_at')
                ->value('id');
        if ($legacyAccountId) {
            DB::connection('crm')->table('accounts')->where('id', $legacyAccountId)->update([
                'company_id' => $companyId ?? DB::connection('crm')->table('accounts')->where('id', $legacyAccountId)->value('company_id'),
                'party_company_id' => $parentPartyCompanyId,
                'account_type' => 'person',
                'name' => $displayName,
                'status' => null,
                ...$address,
                ...$this->integrationValues($externalId),
                'updated_at' => now(),
            ]);
        }
        $values = [
            'account_id' => $accountId,
            'name' => $displayName,
            'email' => $row->email ?? null,
            'phone' => $row->phone ?? null,
            'metadata' => json_encode(['source' => 'odoo', 'external_id' => $externalId], JSON_THROW_ON_ERROR),
            ...$this->integrationValues($externalId),
            'updated_at' => now(),
        ];
        if ($reference?->local_entity_type === 'crm_contact' && $reference->local_id) {
            DB::connection('crm')->table('contacts')->where('id', $reference->local_id)->update($values);
            return;
        }
        $contactId = (string) Str::uuid();
        DB::connection('crm')->table('contacts')->insert(array_merge(['id' => $contactId, 'created_at' => now()], $values));
        $this->saveLocalReference('res_partner', $externalId, 'crm_contact', $contactId, $displayName);
    }

    /** @param array<string, mixed>|object $source */
    private function addressValues(array|object $source): array
    {
        $value = static function (array|object $source, string $key): ?string {
            $raw = is_array($source) ? ($source[$key] ?? null) : ($source->{$key} ?? null);
            return filled($raw) ? trim((string) $raw) : null;
        };

        return [
            'street' => $value($source, 'street'),
            'street2' => $value($source, 'street2'),
            'city' => $value($source, 'city'),
            'state' => $value($source, 'state_name') ?: $value($source, 'state'),
            'zip' => $value($source, 'zip'),
            'country' => $value($source, 'country_name') ?: $value($source, 'country'),
        ];
    }

    private function syncCompanyAddress(?int $companyId, array $address): void
    {
        if ($companyId === null) {
            return;
        }

        Company::query()->whereKey($companyId)->update($address);
    }

    private function syncCompanyIntegrationMetadata(int $companyId, string $externalId): void
    {
        Company::query()->whereKey($companyId)->update($this->integrationValues($externalId));
    }

    private function integrationValues(string $externalId): array
    {
        return [
            'source_system' => 'odoo',
            'source_database' => $this->source->sourceDatabase(),
            'external_id' => $externalId,
            'is_integrated' => true,
            'last_synced_at' => now(),
        ];
    }

    private function resolveTeamId(?int $odooTeamId, mixed $teamName, int $companyId): ?string
    {
        if (! $odooTeamId) return null;
        $core = DB::connection('core');
        $externalId = (string) $odooTeamId;
        $reference = $core->table('external_references')->where([
            'source_system' => 'odoo',
            'source_database' => $this->source->sourceDatabase(),
            'entity_type' => 'crm_team',
            'external_id' => $externalId,
            'local_entity_type' => 'crm_team',
        ])->first();
        if ($reference?->local_id) return (string) $reference->local_id;
        $teamId = (string) Str::uuid();
        DB::connection('crm')->table('teams')->insert(['id' => $teamId, 'company_id' => $companyId, 'name' => $this->displayName($teamName), 'is_active' => true, 'created_at' => now(), 'updated_at' => now()]);
        $this->saveLocalReference('crm_team', $externalId, 'crm_team', $teamId, $this->displayName($teamName));
        return $teamId;
    }

    private function closingState(object $row, string $stageCode): array
    {
        if (! empty($row->lost_reason_id)) return ['code' => 'lost', 'is_won' => false];
        if (! empty($row->date_closed) || (float) ($row->probability ?? 0) >= 100 || $stageCode === 'won' || $stageCode === 'delivery') {
            return ['code' => 'closed', 'is_won' => $stageCode === 'won' || $stageCode === 'delivery'];
        }
        return ['code' => null, 'is_won' => false];
    }

    private function mappedCompanyId(?int $odooCompanyId): ?int
    {
        if (! $odooCompanyId) {
            return null;
        }

        $value = DB::connection('core')->table('external_references')->where([
            'source_system' => 'odoo',
            'source_database' => $this->source->sourceDatabase(),
            'entity_type' => 'res_company',
            'external_id' => (string) $odooCompanyId,
            'local_entity_type' => 'company',
        ])->value('local_id');
        return $value !== null ? (int) $value : null;
    }

    private function saveLocalReference(string $entityType, string $externalId, string $localType, string $localId, ?string $displayName): void
    {
        DB::connection('core')->table('external_references')->updateOrInsert(
            ['source_system' => 'odoo', 'source_database' => $this->source->sourceDatabase(), 'entity_type' => $entityType, 'external_id' => $externalId],
            ['local_entity_type' => $localType, 'local_id' => $localId, 'sync_status' => 'normalized', 'last_synced_at' => now(), 'metadata' => json_encode(['display_name' => $displayName], JSON_THROW_ON_ERROR), 'updated_at' => now(), 'created_at' => now()]
        );
    }

    private function stageCode(?string $stageName, string $recordType): string
    {
        $stage = mb_strtolower($this->displayName($stageName));
        if ($recordType === 'lead' || $stage === '') return 'new';
        return match (true) {
            str_contains($stage, 'won'), str_contains($stage, 'po') => 'won',
            str_contains($stage, 'delivery'), str_contains($stage, 'deliver') => 'delivery',
            str_contains($stage, 'negoti') => 'negotiation',
            str_contains($stage, 'proposal'), str_contains($stage, 'propos') => 'proposal',
            str_contains($stage, 'technical'), str_contains($stage, 'boq'), str_contains($stage, 'review') => 'technical_review',
            str_contains($stage, 'qualif') => 'qualified',
            default => 'qualified',
        };
    }

    private function displayName(mixed $value): string
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (is_array($decoded)) return (string) ($decoded['en_US'] ?? reset($decoded) ?: 'Tanpa nama');
            return $value;
        }
        if (is_array($value)) return (string) ($value['en_US'] ?? reset($value) ?: 'Tanpa nama');
        return (string) ($value ?: 'Tanpa nama');
    }
}
