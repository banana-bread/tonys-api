<?php

namespace App\Models;

use App\Helpers\DayCollection;
use App\Models\Contracts\UserModel;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use App\Traits\ReceivesEmails;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;

class Employee extends BaseModel implements UserModel
{
    use HasUuid, ReceivesEmails, SoftDeletes;
    
    protected $appends = [
        'name',
        'phone',
        'email',
    ];

    protected $visible = [
        'id',
        'company_id',
        'admin',
        'owner',
        'settings',

        'user',
        'company', 
        'time_slots',
        'bookings',

        'name',
        'phone',
        'email',
        'latest_schedule',
        'base_schedule',
    ];

    protected $casts = [
        'admin'    => 'boolean',
        'owner'    => 'boolean',
        'settings' => 'collection',
    ];

    // RELATIONS

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function time_slots()
    {
        return $this->hasMany(TimeSlot::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    // CUSTOM ATTRIBUTES

    public function getNameAttribute(): string
    {
        return $this->user->name;
    }

    public function getPhoneAttribute(): string
    {
        return $this->user->phone;
    }

    public function getEmailAttribute(): string
    {
        return $this->user->email;
    }

    public function getBaseScheduleAttribute()
    {
        return $this->settings['base_schedule'];
    }

    // TODO: probably add in setBaseScheduleAttribute eventually

    public function getLatestTimeSlotAttribute(): TimeSlot
    {
        return $this->time_slots()->latest('start_time')->first();
    }

    // public function getOldestTimeSlotAttribute(): TimeSlot
    // {
    //     return $this->time_slots()->oldest('start_time')->first();
    // }

    public function getFutureReservedSlotsAttribute(): Collection
    {
        return $this->time_slots()
            ->where('start_time', '>', now())
            ->where('reserved', true)
            ->get();
    }

    // SCOPES
    public function scopeForCompany($query, string $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    // HELPERS

    public function hasSchedules(): bool
    {
        return !!$this->schedules()->count();
    }
    
    public function hasTimeSlots(): bool
    {
        return !!$this->time_slots()->count();
    }

    public function hasFutureTimeSlots(): bool
    {
        return !!$this->time_slots()->where('start_time', '>' ,now())->count();
    }

    public function isAdmin(): bool
    {
        return $this->admin;
    }

    public function isOwner(): bool
    {
        return $this->owner;
    }

    public function isOnlyOwner(): bool
    {
        return $this->isOwner() && Employee::forCompany($this->company_id)->where('owner', true)->count() == 1;
    }

    // TODO: Need to adjust this to account for slots that should be created for the current day.
    //       For example, if an employee updates their base schedule on monday at 3pm (and they work 
    //       until 5pm), all future slots including the remainder of monday's slots would be removed.
    //       
    //       Currently, this would handle that situation by creating new time slots starting on tuesday,
    //       meaning the remaining slots for monday would be not created.  This may be a RE-WRITE
    public function createTimeSlotsForNext(int $numberOfDays, int $daysOffset = 0): Collection
    {
        if ($this->hasFutureTimeSlots())
        {
            $start = $this->latest_time_slot->start_time->copy()->startOfDay()->addDay();
            $end = $start->copy()->addDays($numberOfDays);    
        }
        else
        {
            $start = today()->addDays($daysOffset);
            $end = today()->addDays($numberOfDays);
        }

        $days = DayCollection::fromRange($start, $end);

        $timeSlots = new EloquentCollection();
        $singleSlotDuration = $this->company->time_slot_duration;

        $days->each(function ($day) use ($timeSlots, $singleSlotDuration) {

            $baseStart = $this->base_schedule[Str::lower($day->englishDayOfWeek)]['start'];
            $baseEnd = $this->base_schedule[Str::lower($day->englishDayOfWeek)]['end'];

            if ($baseStart && $baseEnd)
            {
                $totalSecondsInWorkDay = $baseEnd - $baseStart;
                $totalSlotsInWorkDay = floor($totalSecondsInWorkDay / $singleSlotDuration);
                
                for ($i = 0; $i < $totalSlotsInWorkDay; $i++)
                {
                    $start = $day->copy()->addSeconds($baseStart + ($i * $singleSlotDuration));
                    $end = $start->copy()->addSeconds($singleSlotDuration);
                    
                    $timeSlots->push(
                        new TimeSlot([
                            'employee_id' => $this->id,
                            'company_id' => $this->company_id,
                            'reserved' => false,
                            'start_time' => $start,
                            'end_time' => $end,
                        ])
                    );
                }
            }
        });

        $this->time_slots()->saveMany($timeSlots);

        return $timeSlots;
    }

    public function updateBaseSchedule($baseSchedule)
    {   
        // Gather any time slots that have been reserved and make copies.
        $reservedSlotStartTimes = $this->future_reserved_slots->pluck('start_time');

        // Get date of last time slot, determine number of days we need to make slots for.
        $numberOfDays = today()->diffInDays($this->latest_time_slot->start_time);

        $this->deleteFutureSlots();
        $this->createTimeSlotsForNext($numberOfDays);
    }

    public function deleteFutureSlots()
    {
        // Delete remaining slots for today.
        // Delete all future time slots.
        $this->time_slots()
            ->where('start_time', '>', now())
            ->delete();
    }
}
