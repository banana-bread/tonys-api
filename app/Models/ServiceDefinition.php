<?php

namespace App\Models;
class ServiceDefinition extends BaseModel
{
    protected $visible = [
        'id',
        'name',
        'price',
        'duration'
    ];

    protected $rules = [];
}
