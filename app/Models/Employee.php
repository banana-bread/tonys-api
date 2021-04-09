<?php

namespace App\Models;

use App\Helpers\DayCollection;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Str;
use App\Traits\ReceivesEmails;

class Employee extends BaseModel
{
    use HasUuid, ReceivesEmails;
    
    protected $appends = [
        'name',
        'phone',
        'email'
    ];

    protected $visible = [
        'id',
        'company_id',
        'admin',
        'settings',

        'user',
        'company', 
        'schedules',
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

    public function schedules()
    {
        return $this->hasMany(EmployeeSchedule::class);
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

    public function getLatestScheduleAttribute()
    {
        return $this->schedules()->latest('start_time')->first();
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

    public function createSchedulesForNext(int $numberOfDays): EloquentCollection
    {
        if ($this->hasSchedules())
        {
            $start = $this->latest_schedule->start_time->copy()->addDay();
            $end = $start->copy()->addDays($numberOfDays);
        }
        else
        {
            $start = today();
            $end = today()->addDays($numberOfDays);
        }

        $days = DayCollection::fromRange($start, $end);

        $schedules = $days->map(function ($day) {
            $baseStart = $this->base_schedule[Str::lower($day->englishDayOfWeek)]['start'];
            $baseEnd = $this->base_schedule[Str::lower($day->englishDayOfWeek)]['end'];

            $start = $baseStart ? $day->copy()->addSeconds($baseStart) : $day;
            $end = $baseEnd ? $day->copy()->addSeconds($baseEnd) : $day;

            return new EmployeeSchedule([
                'start_time' => $start,
                'end_time' => $end,
                'weekend' => (!$baseStart && !$baseEnd),
                'holiday' => false,
            ]);
        });

        $this->schedules()->saveMany($schedules);

        return $this->schedules;
    }
}
