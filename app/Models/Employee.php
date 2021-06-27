<?php 

namespace App\Models;

use App\Exceptions\EmployeeException;
use App\Helpers\BaseSchedule;
use App\Helpers\DayCollection;
use App\Models\Contracts\UserModel;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use App\Traits\ReceivesEmails;
use Carbon\Carbon;
use Facade\FlareClient\Time\Time;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Jobs\UpdateEmployeeBaseSchedule;

// TODO: Move time slot creation stuff into a trait.  
//       HasTimeSlots?
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
        'bookings_enabled',
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
        return new BaseSchedule($this->settings['base_schedule']);
    }

    public function setBaseScheduleAttribute(BaseSchedule $schedule)
    {
        $updatedSettings = $this->settings;
        $updatedSettings['base_schedule'] = $schedule->toArray();

        $this->attributes['settings'] = json_encode($updatedSettings);
    }

    public function getLatestTimeSlotAttribute(): ?TimeSlot
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

    public function hasFutureSlots(): bool
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

    public function isActive(): bool
    {
        return $this->bookings_enabled;
    }

    public function createSlotsForNext(int $numberOfDays): Collection
    {
        $start = $this->hasFutureSlots()
            ? $this->latest_time_slot->start_time->copy()->startOfDay()->addDay()
            : today();
        
        $end = $start->copy()->addDays($numberOfDays);

        $days = DayCollection::fromRange($start, $end);

        $timeSlots = new EloquentCollection();
        $singleSlotDuration = $this->company->time_slot_duration;

        $days->each(function ($day) use ($timeSlots, $singleSlotDuration) {
            $baseStart = $this->base_schedule->start($day->englishDayOfWeek);
            $baseEnd = $this->base_schedule->end($day->englishDayOfWeek);

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

    public function updateBaseSchedule(BaseSchedule $newBaseSchedule)
    {   
        if (! $newBaseSchedule->fallsWithin($this->company->base_schedule))
        {
            throw new EmployeeException([], 'New employee schedule does not fall within company schedule');
            // exception(EmployeeScheduleException::class, 12)
            
            // public function exception(string $class, int $code)
            // {
            //     Service container-like class finds exception class and returns the value of provided code.
            //     Should be an array of messages, http codes, ...., keyed by code. 
            // }
        }

        // No changes made, exit

        if ($newBaseSchedule->matches($this->base_schedule)) return;

        UpdateEmployeeBaseSchedule::dispatch($this);

        $this->base_schedule = $newBaseSchedule;
        $this->save();
    }

    // TODO: don't like the name
    public function createSlotsAfterScheduleUpdate()
    {
        $numberOfDays = today()->diffInDays($this->latest_time_slot->start_time);        
        $oldFutureReservedSlots = collect($this->future_reserved_slots->toArray());

        $this->deleteFutureSlots();
        $this->createSlotsForToday();
        $this->createSlotsForNext($numberOfDays);
        $this->reserveSlots($oldFutureReservedSlots);
    }

    protected function createSlotsForToday()
    {
        // - [x] Get company slot duration
        $slotDuration = $this->company->time_slot_duration;

        // - [x] Get end time of today from base schedule
        $baseEnd = $this->base_schedule->end(today()->englishDayOfWeek);

        // - [x] IF end of today is null, exit.
        if (! $baseEnd) return;

        // - [x] Get the number of slots needed to fill rest of day
        $secondsLeftInWorkDay = today()->addSeconds($baseEnd)->timestamp - now()->timestamp;
        $totalSlotsInWorkDay = floor($secondsLeftInWorkDay / $slotDuration);

        // [x] If work day is over, exit.
        if ($secondsLeftInWorkDay <= 0) return;

        // - [x] Create the slots
        $timeSlots = new EloquentCollection();

        for ($i = 0; $i < $totalSlotsInWorkDay; $i++)
        {
            $end = today()->addSeconds($baseEnd - ($i * $slotDuration)); 
            $start = $end->copy()->subSeconds($slotDuration);
            
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

        $this->time_slots()->saveMany($timeSlots);
    }

    protected function deleteFutureSlots()
    {
        $this->time_slots()
            ->where('start_time', '>', now())
            ->delete();
    }

    protected function reserveSlots(Collection $slots)
    {
        $startTimes = $slots->pluck('start_time')->map(function ($startTime) {
            return Carbon::parse($startTime);
        });

        $this->time_slots()
            ->whereIn('start_time', $startTimes)
            ->update(['reserved' => true]);
    }
}
