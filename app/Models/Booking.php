<?php

namespace App\Models;

use App\Mail\BookingCreated;
use App\Models\Interfaces\ReceivesBookingNotifications;
use Illuminate\Support\Facades\Mail;
use App\Traits\HasUuid;

class Booking extends BaseModel
{
    use HasUuid;
    
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

    // public function getPassedCancellationDeadlineAttribute(): bool
    // {
    //     /*
    //         TODO:
    //             - Implement company_settings table
    //             - implement row 'cancellation_window', specifies cancellation deadline;

    //         return $this->company->cancellation_window < (now() - $this->started_at);
          
    //      */
    //     return false;
    // }

    public function getCancelledAttribute(): bool
    {
        return !!$this->cancelled_at;
    }

    public function cancel()
    {
        return $this->update([
            'cancelled_at' => now(),
            'cancelled_by' => auth()->user()->id,
        ]);
    }

    public function isPassedCancellationDeadline(): bool
    {
        return Company::booking_cancellation_period() < ( now() - $this->started_at );
    }

    public function canBeCancelled(): bool
    {        
        /* TODO: implement rules

            Why it cannot be cancelled:
                - passed cancellation deadline
                - already cancelled
                - ** Policy will not be implemented here, but would be:
                     if ($booking->belongsTo($client) || 
                         $booking->belongsTo($employee) ||
                         $auth()->user()->isAdmin())
        */

        // return !$booking->isPassedCancellationDeadline() || $booking->cancelled;

        // NOTE: returning false here so tests fail and feature is implemented properly.
        return false;
    }

    // ACTIONS
    
    public function notify(ReceivesBookingNotifications $model)
    {
        Mail::to($model->user)->queue(new BookingCreated($this, $model));
    }
}
