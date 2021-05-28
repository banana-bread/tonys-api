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
        'email'
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

    public function getNameAttribute()
    {
        return $this->user->name;
    }

    public function getPhoneAttribute()
    {
        return $this->user->phone;
    }

    public function getEmailAttribute()
    {
        return $this->user->email;
    }

    public function getBaseScheduleAttribute()
    {
        return $this->settings['base_schedule'];
    }

    // TODO: probably add in setBaseScheduleAttribute eventually

    public function getLatestTimeSlotAttribute()
    {
        return $this->time_slots()->latest('start_time')->first();
    }

    // SCOPES
    public function scopeForCompany($query, string $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    // ACTIONS

    public function upgrade()
    {
        $this->admin = true;
        return $this->save();
    }

    public function downgrade()
    {
        $this->admin = false;
        return $this->save();
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

    public function isAdmin(): bool
    {
        return $this->admin;
    }

    public function isOwner(): bool
    {
        return $this->owner;
    }

    public function createTimeSlotsForNext(int $numberOfDays): Collection
    {
        if ($this->hasTimeSlots())
        {
            $start = $this->latest_time_slot->start_time->copy()->startOfDay()->addDay();
            $end = $start->copy()->addDays($numberOfDays);    
        }
        else
        {
            $start = today();
            $end = today()->addDays($numberOfDays);
        }

        $days = DayCollection::fromRange($start, $end);

        $timeSlots = new EloquentCollection();

        $days->each(function ($day) use ($timeSlots) {

            $baseStart = $this->base_schedule[Str::lower($day->englishDayOfWeek)]['start'];
            $baseEnd = $this->base_schedule[Str::lower($day->englishDayOfWeek)]['end'];
            $singleSlotDuration = $this->company->time_slot_duration;

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
}
