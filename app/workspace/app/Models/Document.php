<?php

namespace App\Models;

use Core\Models\Company;
use Core\Models\Project;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use HasUuids, SoftDeletes;

    protected $connection = 'documents';
    protected $table = 'documents';


    protected $fillable = [
        'company_id', 'project_id', 'crm_account_id', 'crm_opportunity_id', 'vendor_id', 'document_type_id', 'uploaded_by',
        'title', 'original_filename', 'reference_number', 'normalized_reference_number',
        'document_date', 'amount', 'currency', 'status', 'metadata', 'tags', 'retention_until', 'legal_hold', 'verified_at',
        'rejection_reason', 'quarantined_at', 'released_at', 'rejected_at',
    ];

    protected function casts(): array
    {
        return ['document_date' => 'date', 'amount' => 'decimal:4', 'metadata' => 'array', 'tags' => 'array', 'retention_until' => 'date', 'legal_hold' => 'boolean', 'verified_at' => 'datetime', 'quarantined_at' => 'datetime', 'released_at' => 'datetime', 'rejected_at' => 'datetime'];
    }

    public function company() { return $this->belongsTo(Company::class); }
    public function project() { return $this->belongsTo(Project::class); }
    public function crmAccount() { return $this->belongsTo(CrmAccount::class, 'crm_account_id'); }
    public function crmOpportunity() { return $this->belongsTo(CrmOpportunity::class, 'crm_opportunity_id'); }
    public function vendor() { return $this->belongsTo(Vendor::class); }
    public function documentType() { return $this->belongsTo(DocumentType::class); }
    public function versions() { return $this->hasMany(DocumentVersion::class); }
    public function currentVersion() { return $this->hasOne(DocumentVersion::class)->where('is_current', true); }
    public function relations() { return $this->hasMany(DocumentRelation::class, 'source_document_id'); }

    public function parties() { return $this->hasMany(DocumentParty::class); }
}
