<?php

namespace App\Models;

use App\Exceptions\BookingException;
use App\Models\Contracts\UserModel;
use App\Traits\HasUuid;
use Carbon\CarbonInterval;

class Booking extends BaseModel
{
    use HasUuid;

    protected $appends = [
        // 'formatted_duration',
        // 'formatted_total',
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

        'duration',
        'formatted_duration',
        'total',
        'formatted_total',
    ];

    protected $casts = [
        'started_at'   => 'datetime',
        'ended_at'     => 'datetime',
        'cancelled_at' => 'datetime'
    ];

    // RELATIONS

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

    // CUSTOM ATTRIBUTES

    public function getDurationAttribute()
    {
        return $this->ended_at->timestamp - $this->started_at->timestamp;
    }

    public function getFormattedDurationAttribute()
    {
        return CarbonInterval::minutes($this->duration / 60)->forHumans();
    }

    public function getTotalAttribute()
    {
        return $this->services->sum('price');
    }

    public function getFormattedTotalAttribute()
    {
        return '$' . number_format(($this->total/100), 2, '.', ' ');
    }

    // ACTIONS

    public function cancel()
    {
        if (! $this->canBeCancelled())
        {
            throw new BookingException([$this], 'Booking cannot be cancelled'); 
        }

        return $this->update([
            'cancelled_at' => now(),
            'cancelled_by' => auth()->user()->id,
        ]);
    }

    // HELPERS

    public function isCancelled(): bool
    {
        return !!$this->cancelled_at;
    }

    public function isWithinGracePeriod(): bool
    {
        return $this->started_at->greaterThan(
            now()->addSeconds($this->employee->company->booking_grace_period)
        );
    }

    public function canBeCancelled(): bool
    {
        // Bookings client is trying to cancel
        if ($this->client->user_id == auth()->user()->id)
        {
            return $this->isWithinGracePeriod() && !$this->isCancelled();
        }
        // Bookings employee, or admin is trying to cancel
        else if ($this->employee->user_id == auth()->user()->id ||
                 auth()->user()->isAdmin())
        {
            return !$this->isCancelled();
        }

        return false;
    }

    public function wasCancelledBy(UserModel $model)
    {
        return $this->cancelled_by == $model->user->id;
    }
}
