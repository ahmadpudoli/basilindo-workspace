<?php

namespace App\Modules\CRM\Domain\Models;

use Core\Models\Project;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CrmOpportunity extends Model
{
    use HasUuids, SoftDeletes;

    protected $connection = 'crm';
    protected $table = 'opportunities';

    protected $fillable = ['account_id', 'contact_id', 'team_id', 'company_id', 'record_type', 'name', 'email', 'phone', 'source', 'status', 'stage', 'stage_id', 'closing_status_id', 'amount', 'currency', 'expected_close_date', 'owner_user_id', 'project_id', 'notes', 'lost_reason', 'won_at', 'closed_at', 'po_number', 'po_document_id', 'external_source', 'external_id', 'source_system', 'source_database', 'is_integrated', 'source_record_status', 'last_synced_at'];

    protected function casts(): array
    {
        return ['amount' => 'decimal:4', 'expected_close_date' => 'date', 'won_at' => 'datetime', 'closed_at' => 'datetime', 'is_integrated' => 'boolean', 'last_synced_at' => 'datetime'];
    }

    public function account()
    {
        return $this->belongsTo(CrmAccount::class);
    }

    public function company()
    {
        return $this->belongsTo(\Core\Models\Company::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function contact() { return $this->belongsTo(CrmContact::class); }
    public function pipelineStage() { return $this->belongsTo(CrmPipelineStage::class, 'stage_id'); }
    public function closingStatus() { return $this->belongsTo(CrmClosingStatus::class, 'closing_status_id'); }
    public function team() { return $this->belongsTo(CrmTeam::class, 'team_id'); }
}
