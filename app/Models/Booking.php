<?php

namespace App\Models;

use App\Mail\BookingCreated;
use App\Models\Interfaces\ReceivesBookingNotifications;
use Illuminate\Support\Facades\Mail;
use phpDocumentor\Reflection\Types\Boolean;

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

    public function getPassedCancellationDeadlineAttribute(): bool
    {
        /*
            TODO:
                - Implement company_settings table
                - implement row 'cancellation_window', specifies cancellation deadline;

            return $this->company->cancellation_window < (now() - $this->started_at);
          
         */
        return false;
    }

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

        // return $booking->past_cancellation_deadline || $booking->cancelled;

        // NOTE: returning false here so tests fail and feature is implemented properly.
        return false;
    }
}
