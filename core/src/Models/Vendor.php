<?php

namespace Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vendor extends Model
{
    use SoftDeletes;

    protected $connection = 'core';

    protected $fillable = ['company_id', 'vendor_company_id', 'name', 'code', 'tax_identifier', 'status'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function vendorCompany()
    {
        return $this->belongsTo(Company::class, 'vendor_company_id');
    }

    public function operationalCompanies()
    {
        return $this->belongsToMany(Company::class, 'vendor_operational_companies', 'vendor_id', 'company_id')->withTimestamps();
    }

    public function documents()
    {
        return $this->hasMany(config('core.models.document'));
    }
}
