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
        'description',
        'price',
        'duration',

        'company',
        'employees',

        'employee_ids',
    ];

    // TODO: this is fine for now, we mostly will only pull around 10 services at a time.
    protected $appends = [
        'employee_ids',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function employees()
    {
        return $this->belongsToMany(Employee::class)->withTimestamps();
        // return $this->belongsToMany(Employee::class)->as('dispatch_details')->withDiscarded()->withPivot('last_viewed_at');
    }

    public function scopeForCompany($query, string $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function getEmployeeIdsAttribute()
    {
        return $this->employees()->allRelatedIds();
    }
}
