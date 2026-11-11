<?php

namespace App\Modules\Integration\Application\Services;

use Illuminate\Support\Facades\DB;

class OdooPostgresDataSource
{
    public function testConnection(): void
    {
        DB::connection('odoo')->selectOne('select 1 as connection_ok');
    }

    public function sourceDatabase(): string
    {
        return (string) config('database.connections.odoo.database');
    }

    /** @return array<int, object> */
    public function changedCompanies(?string $since, bool $full = false): array
    {
        $query = DB::connection('odoo')->table('res_company')
            ->select(['id', 'name', 'partner_id', 'write_date', 'active'])
            ->where('active', true)
            ->orderBy('id');
        if (! $full && $since) $query->where('res_company.write_date', '>', $since);
        return $query->get()->all();
    }

    /** @return array<int, object> */
    public function changedPartners(?string $since, bool $full = false): array
    {
        $query = DB::connection('odoo')->table('res_partner')
            ->leftJoin('res_country_state as state', 'state.id', '=', 'res_partner.state_id')
            ->leftJoin('res_country as country', 'country.id', '=', 'res_partner.country_id')
            ->select([
                'res_partner.id', 'res_partner.name', 'res_partner.email', 'res_partner.phone', 'res_partner.mobile',
                'res_partner.is_company', 'res_partner.parent_id', 'res_partner.commercial_partner_id', 'res_partner.vat',
                'res_partner.ref', 'res_partner.customer_rank', 'res_partner.supplier_rank', 'res_partner.write_date',
                'res_partner.company_id', 'res_partner.active', 'res_partner.street', 'res_partner.street2',
                'res_partner.city', 'res_partner.zip', 'res_partner.state_id', 'state.name as state_name',
                'res_partner.country_id', 'country.name as country_name',
            ])
            ->where('res_partner.active', true)
            ->orderBy('id');
        if (! $full && $since) $query->where('res_partner.write_date', '>', $since);
        return $query->get()->all();
    }

    /** @return array<int, object> */
    public function changedOpportunities(?string $since, bool $full = false): array
    {
        $query = DB::connection('odoo')->table('crm_lead as lead')
            ->leftJoin('crm_stage as stage', 'stage.id', '=', 'lead.stage_id')
            ->leftJoin('crm_team as team', 'team.id', '=', 'lead.team_id')
            ->leftJoin('crm_lost_reason as lost_reason', 'lost_reason.id', '=', 'lead.lost_reason_id')
            ->select(['lead.id', 'lead.name', 'lead.partner_id', 'lead.company_id', 'lead.user_id', 'lead.team_id', 'team.name as team_name', 'lead.stage_id', 'stage.name as stage_name', 'lead.expected_revenue', 'lead.probability', 'lead.date_deadline', 'lead.date_closed', 'lead.lost_reason_id', 'lost_reason.name as lost_reason', 'lead.type', 'lead.email_from', 'lead.phone', 'lead.description', 'lead.write_date'])
            ->where('lead.active', true)
            ->orderBy('lead.id');
        if (! $full && $since) $query->where('lead.write_date', '>', $since);
        return $query->get()->all();
    }

    /** @return array<int, object> */
    public function changedProjects(?string $since, bool $full = false): array
    {
        $query = DB::connection('odoo')->table('project_project as project')
            ->leftJoin('res_partner as partner', 'partner.id', '=', 'project.partner_id')
            ->select(['project.id', 'project.name', 'project.company_id', 'project.partner_id', 'partner.name as partner_name', 'project.user_id', 'project.opportunity_id', 'project.active', 'project.write_date'])
            ->where('project.active', true)
            ->orderBy('project.id');
        if (! $full && $since) $query->where('project.write_date', '>', $since);
        return $query->get()->all();
    }
}
