<?php

namespace App\Models;

use App\Mail\BookingCreated;
use App\Models\Interfaces\ReceivesBookingNotifications;
use Illuminate\Support\Facades\Mail;

class Booking extends BaseModel
{
    protected $fillable = [
        'client_id',
        'employee_id', 
        'cancelled_at',
        'cancelled_by',
        'started_at',
        'ended_at',
    ];

    protected $visible = [
        'id',
        'client_id',
        'employee_id',
        'cancelled_at',
        'cancelled_by',
        'started_at',
        'ended_at',

        'client',
        'employee',
        'services',
    ];

    protected $casts = [
        'started_at'   => 'datetime',
        'ended_at'     => 'datetime',
        'cancelled_at' => 'datetime'
    ];

    // Relations

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function notify(ReceivesBookingNotifications $model)
    {
        Mail::to($model->user)->queue(new BookingCreated($this, $model));
    }

    public function cancel(User $user)
    {
        return $this->update([
            'cancelled_at' => now(),
            'cancelled_by' => $user->id
        ]);
    }
}
