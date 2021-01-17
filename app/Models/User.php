<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use App\Traits\HasUuid;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, HasFactory, HasUuid;

    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    protected $visible = [
        'id',
        'client_id',
        'employee_id',
        'name',
        'email',
        'phone'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Relations
    
    public function employee()
    {
        return $this->hasOne(Employee::class);
    }

    public function client()
    {
        return $this->hasOne(Client::class);
    }
}
