<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceDefinition extends Model
{
    use HasFactory;

    protected $visible = [
        'id',
        'name',
        'price',
        'duration'
    ];

}
