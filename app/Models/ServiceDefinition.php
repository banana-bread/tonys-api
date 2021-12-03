<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceDefinition extends BaseModel
{
    use HasUuid, SoftDeletes;
    
    protected $visible = [
        'id',
        'company_id',
        'name',
        'price',
        'duration'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function scopeForCompany($query, string $companyId)
    {
        return $query->where('company_id', $companyId);
    }
}
