<?php

namespace App\Models;

use App\Traits\HasUuid;

class ServiceDefinition extends BaseModel
{
    use HasUuid;
    
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
}
