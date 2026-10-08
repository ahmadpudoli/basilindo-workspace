<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $legacyTables = [
        'fo_audit_events', 'fo_bundles', 'fo_cache', 'fo_cache_locks',
        'fo_document_parties', 'fo_document_relations', 'fo_document_types',
        'fo_document_versions', 'fo_documents', 'fo_failed_jobs', 'fo_job_batches',
        'fo_jobs', 'fo_model_has_permissions', 'fo_model_has_roles',
        'fo_permissions', 'fo_role_has_permissions', 'fo_roles',
        'fo_verification_cases', 'fo_verification_items',
        'pm_cache', 'pm_cache_locks', 'pm_epics', 'pm_external_access',
        'pm_failed_jobs', 'pm_job_batches', 'pm_jobs',
        'pm_model_has_permissions', 'pm_model_has_roles', 'pm_notifications',
        'pm_permissions', 'pm_project_notes', 'pm_role_has_permissions', 'pm_roles',
        'pm_ticket_comments', 'pm_ticket_histories', 'pm_ticket_priorities',
        'pm_ticket_statuses', 'pm_ticket_users', 'pm_tickets',
    ];

    private array $renames = [
        'ws_crm_accounts' => 'crm_accounts',
        'ws_crm_contacts' => 'crm_contacts',
        'ws_crm_leads' => 'crm_leads',
        'ws_crm_opportunities' => 'crm_opportunities',
        'ws_crm_activities' => 'crm_activities',
        'ws_tickets' => 'pm_tickets',
        'ws_ticket_statuses' => 'pm_ticket_statuses',
        'ws_ticket_priorities' => 'pm_ticket_priorities',
        'ws_ticket_users' => 'pm_ticket_users',
        'ws_documents' => 'doc_documents',
        'ws_document_types' => 'doc_document_types',
        'ws_document_versions' => 'doc_document_versions',
        'ws_document_relations' => 'doc_document_relations',
        'ws_document_parties' => 'doc_document_parties',
        'ws_verification_cases' => 'doc_verification_cases',
        'ws_verification_items' => 'doc_verification_items',
        'ws_bundles' => 'doc_bundles',
        'ws_audit_events' => 'doc_audit_events',
        'ws_saved_searches' => 'doc_saved_searches',
        'ws_roles' => 'core_roles',
        'ws_permissions' => 'core_permissions',
        'ws_model_has_roles' => 'core_model_has_roles',
        'ws_model_has_permissions' => 'core_model_has_permissions',
        'ws_role_has_permissions' => 'core_role_has_permissions',
    ];

    public function up(): void
    {
        $this->moveTechnicalLegacyTables();

        DB::connection('pgsql')->statement('CREATE SCHEMA IF NOT EXISTS legacy');

        foreach ($this->legacyTables as $table) {
            if ($this->exists($table) && $this->count($table) > 0) {
                DB::connection('pgsql')->statement(sprintf(
                    'ALTER TABLE %s SET SCHEMA legacy',
                    $this->quote($table),
                ));
            }
        }

        foreach ($this->legacyTables as $table) {
            if ($this->exists($table)) {
                DB::connection('pgsql')->statement('DROP TABLE '.$this->quote($table).' CASCADE');
            }
        }

        foreach ($this->renames as $from => $to) {
            if ($this->exists($from) && ! $this->exists($to)) {
                DB::connection('pgsql')->statement(sprintf(
                    'ALTER TABLE %s RENAME TO %s',
                    $this->quote($from),
                    $this->quote($to),
                ));
            }
        }
    }

    public function down(): void
    {
        foreach (array_reverse($this->renames, true) as $from => $to) {
            if ($this->exists($to) && ! $this->exists($from)) {
                DB::connection('pgsql')->statement(sprintf(
                    'ALTER TABLE %s RENAME TO %s',
                    $this->quote($to),
                    $this->quote($from),
                ));
            }
        }
    }

    private function exists(string $table): bool
    {
        return DB::connection('pgsql')->selectOne(
            'select to_regclass(?) as relation_name', ['public.'.$table],
        )?->relation_name !== null;
    }

    private function moveTechnicalLegacyTables(): void
    {
        DB::connection('pgsql')->statement('CREATE SCHEMA IF NOT EXISTS legacy');

        foreach (['fo_migrations', 'pm_cache', 'pm_migrations'] as $table) {
            if ($this->exists($table)) {
                DB::connection('pgsql')->statement(sprintf(
                    'ALTER TABLE %s SET SCHEMA legacy',
                    $this->quote($table),
                ));
            }
        }
    }

    private function count(string $table): int
    {
        return (int) DB::connection('pgsql')->selectOne(
            'select count(*) as aggregate from '.$this->quote($table),
        )->aggregate;
    }

    private function quote(string $identifier): string
    {
        return '"'.str_replace('"', '""', $identifier).'"';
    }
};
