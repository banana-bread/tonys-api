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
    protected $guarded = [];

    protected $visible = [
        'id',
        'client_id',
        'employee_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'subscribed_to_emails',
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

    // RELATIONS
    
    public function employee()
    {
        return $this->hasOne(Employee::class);
    }

    public function client()
    {
        return $this->hasOne(Client::class);
    }

    // HELPERS

    public function isEmployee(): bool
    {
        return !!$this->employee;
    }

    public function isClient(): bool
    {
        return !!$this->client;
    }

    public function isAdmin(): bool
    {
        return $this->isEmployee() && $this->employee->isAdmin();
    }

    public function isOwner(): bool
    {
        return $this->isEmployee() && $this->employee->isOwner();
    }
}
