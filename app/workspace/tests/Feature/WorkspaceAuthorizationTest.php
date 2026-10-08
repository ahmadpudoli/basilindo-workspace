<?php

use App\Models\CrmAccount;
use App\Models\CrmLead;
use App\Models\Ticket;
use App\Policies\CrmAccountPolicy;
use App\Policies\CrmLeadPolicy;
use App\Policies\TicketPolicy;
use App\Filament\Pages\CrmOverview;
use Core\Models\Company;
use Core\Models\Project;
use Core\Models\Role;
use Core\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

function workspaceCompany(string $name): Company
{
    return Company::create([
        'name' => $name,
        'code' => Str::upper(Str::substr(Str::replace(' ', '-', $name), 0, 12)).'-'.Str::random(6),
    ]);
}

it('limits CRM account and lead access to the user company scope', function () {
    $member = User::factory()->create();
    $company = workspaceCompany('CRM Company '.Str::random(5));
    $otherCompany = workspaceCompany('Other CRM '.Str::random(5));
    $member->companies()->attach($company);

    $account = CrmAccount::create(['company_id' => $company->id, 'name' => 'Allowed account']);
    $otherAccount = CrmAccount::create(['company_id' => $otherCompany->id, 'name' => 'Hidden account']);
    $lead = CrmLead::create(['company_id' => $company->id, 'name' => 'Allowed lead']);
    $otherLead = CrmLead::create(['company_id' => $otherCompany->id, 'name' => 'Hidden lead']);

    $accountPolicy = new CrmAccountPolicy();
    $leadPolicy = new CrmLeadPolicy();

    expect($accountPolicy->view($member, $account))->toBeTrue()
        ->and($accountPolicy->view($member, $otherAccount))->toBeFalse()
        ->and($leadPolicy->view($member, $lead))->toBeTrue()
        ->and($leadPolicy->view($member, $otherLead))->toBeFalse();
});

it('limits project tickets to project members', function () {
    $member = User::factory()->create();
    $otherUser = User::factory()->create();
    $company = workspaceCompany('Project Company '.Str::random(5));
    $project = Project::create(['company_id' => $company->id, 'name' => 'Scoped project']);
    $project->members()->attach($member);
    $ticket = Ticket::create(['project_id' => $project->id, 'name' => 'Scoped ticket']);
    $policy = new TicketPolicy();

    expect($policy->view($member, $ticket))->toBeTrue()
        ->and($policy->view($otherUser, $ticket))->toBeFalse();
});

it('shows a safe CRM hub to a CRM role before company assignment', function () {
    $member = User::factory()->create();
    $role = Role::firstOrCreate(['name' => 'crm_member', 'guard_name' => 'web']);
    $member->assignRole($role);

    $this->actingAs($member);

    expect(CrmOverview::canAccess())->toBeTrue()
        ->and($member->companies()->exists())->toBeFalse();
});
