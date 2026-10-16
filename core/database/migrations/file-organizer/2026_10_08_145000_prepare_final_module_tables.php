<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /** @var array<string, string> */
    private array $renames = [
        'ws_crm_accounts' => 'crm_accounts',
        'ws_crm_activities' => 'crm_activities',
        'ws_crm_contacts' => 'crm_contacts',
        'ws_crm_leads' => 'crm_leads',
        'ws_crm_opportunities' => 'crm_opportunities',
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
        // Permission models use the dedicated core connection before the
        // final module-prefix migration runs, so move only these shared
        // authorization tables early. Business tables remain ws_* until the
        // final migration isolates legacy pm_/fo_* tables.
        foreach (array_filter($this->renames, fn (string $to): bool => str_starts_with($to, 'core_')) as $from => $to) {
            if ($this->exists($from) && ! $this->exists($to)) {
                DB::connection('pgsql')->statement(
                    sprintf('ALTER TABLE %s RENAME TO %s', $this->quote($from), $this->quote($to)),
                );
            }
        }
    }

    public function down(): void
    {
        foreach (array_reverse(array_filter($this->renames, fn (string $to): bool => str_starts_with($to, 'core_')), true) as $from => $to) {
            if ($this->exists($to) && ! $this->exists($from)) {
                DB::connection('pgsql')->statement(
                    sprintf('ALTER TABLE %s RENAME TO %s', $this->quote($to), $this->quote($from)),
                );
            }
        }
    }

    private function exists(string $table): bool
    {
        return DB::connection('pgsql')->selectOne(
            'select to_regclass(?) as relation_name', ['public.'.$table],
        )?->relation_name !== null;
    }

    private function quote(string $identifier): string
    {
        return '"'.str_replace('"', '""', $identifier).'"';
    }
};
