<?php 

namespace App\Models;

use App\Exceptions\BookingException;
use App\Exceptions\ScheduleException;
use App\Helpers\BaseSchedule;
use App\Models\Contracts\UserModel;
use App\Traits\HasUuid;
use App\Traits\ReceivesEmails;

use Illuminate\Support\Collection;
use App\Jobs\UpdateEmployeeTimeSlots;
use App\Traits\CreatesTimeSlots;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Employee extends BaseModel implements UserModel
{
    use HasUuid, 
        ReceivesEmails, 
        CreatesTimeSlots;
    
    protected $appends = [
        'first_name',
        'last_name',
        'phone',
        'email',
    ];

    protected $visible = [
        'id',
        'user_id',
        'company_id',
        'admin',
        'owner',
        'bookings_enabled',
        'settings',
        'ordinal_position',

        'user',
        'company', 
        'time_slots',
        'bookings',

        'first_name',
        'last_name',
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

    public function getFirstNameAttribute()
    {
        return $this->user->first_name;
    }

    public function getLastNameAttribute()
    {
        return $this->user->last_name;
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

    // ACTIONS



    public function createBooking(TimeSlot $startingSlot, Collection $serviceDefinitions)
    {
        return DB::transaction(function () use ($startingSlot, $serviceDefinitions) {

        $duration = $serviceDefinitions->sum('duration');
        $slotsRequired = $startingSlot->company->slotsRequiredFor($duration);

        $allSlots = $slotsRequired > 1
            ? $startingSlot->getNextSlots($slotsRequired)->prepend($startingSlot)
            : collect([$startingSlot]);

        if (! TimeSlot::isAvailable($allSlots))
        {
            throw new BookingException([], 'Time slot not available.');
        }

        $booking = Booking::create([
            'employee_id' => $this->id,
            'started_at' => $allSlots->first()->start_time,
            'ended_at' => $allSlots->first()->start_time->copy()->addSeconds($duration),
        ]); 

        TimeSlot::lockAndReserve($allSlots, $booking);

        $services = $serviceDefinitions->map(function ($definition) use ($booking) {
            $service = new Service();
            $service->service_definition_id = $definition->id;
            $service->booking_id = $booking->id;
            $service->name = $definition->name;
            $service->price = $definition->price;
            $service->duration = $definition->duration;

            return $service;
        });

        $booking->services()->saveMany($services);

        return $booking;
        });
    }

    public function updateBaseSchedule(BaseSchedule $newBaseSchedule)
    {   
        logger('[Employee] Started updating base schedule');
        // No changes made, exit
        if ($newBaseSchedule->matches($this->base_schedule)) return;

        if (! $newBaseSchedule->hasValidTimes())
        {
            throw new ScheduleException([], 'Start times must be before end times.');
        }

        // When there are about a years worth of slots to update, this update doesn't take 
        // too long.  If we move to creating 5 years worth of timeslots or something,
        // we would want to dispatch a job to do that.  We would then also need to pass
        // the old base schedule to the job, in case the job fails, we would set base schedule 
        // back to its old value.  
        // 
        DB::transaction(function () use ($newBaseSchedule)
        {
            $this->base_schedule = $newBaseSchedule;
            $this->save();

            logger('[Employee] Saved new base schedule');
    
            $this->updateTimeSlots();
        });

        // UpdateEmployeeTimeSlots::dispatch($this);
    }

    // TODO: this really should not be public, currently is so UpdateEmployeeTimeSlots 
    //       job can be performed seperately of base schedule update.
    public function updateTimeSlots()
    {
        $localTimezone = $this->company()->value('timezone');

        TimeSlot::where('start_time', '>=', now($localTimezone)->startOfDay())
            ->where('employee_id', $this->id)
            ->update(['employee_working' => false]);

        $weekDays = collect(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']);

        logger('[Employee] $weekDays: ' . $weekDays);
        logger('[Employee] $today: ' . now($localTimezone)->startOfDay());
        logger('[Employee] $baseSchedule: ' . $this->base_schedule);
        $weekDays->each(function ($day, $key) use ($localTimezone)
        {
            logger('[Employee] Started updating slots');

            $startTime = $this->base_schedule->start($day);
            $endTime = $this->base_schedule->end($day);

            if (!$startTime || !$endTime) return;

            // TODO: Extract these calculations to... somewhere else.  BaseSchedule?
            $startHourInSeconds = ((int) Str::of($startTime)->explode(':')->first()) * 3600;
            $startMinuteInSeconds = ((int) Str::of($startTime)->explode(':')->last()) * 60;
            $startTimeInSeconds = $startHourInSeconds + $startMinuteInSeconds;

            $endHourInSeconds = ((int) Str::of($endTime)->explode(':')->first()) * 3600;
            $endMinuteInSeconds = ((int) Str::of($endTime)->explode(':')->last()) * 60;
            $endTimeInSeconds = $endHourInSeconds + $endMinuteInSeconds;

            // TODO: this currently performs up to 7 updates, but 
            //       could be done more performantly in 1
            TimeSlot::where('start_time', '>=', now($localTimezone)->startOfDay())
                ->where('employee_id', $this->id)
                ->whereRaw("WEEKDAY(CONVERT_TZ(start_time, 'UTC', ?)) = ?", [$localTimezone, $key])
                ->whereRaw("TIME_TO_SEC(CONVERT_TZ(start_time, 'UTC', ?)) >= ?", [$localTimezone, $startTimeInSeconds])
                ->whereRaw("TIME_TO_SEC(CONVERT_TZ(end_time, 'UTC', ?)) <= ?", [$localTimezone, $endTimeInSeconds])
                ->whereRaw("DATE(CONVERT_TZ(start_time, 'UTC', ?)) = DATE(CONVERT_TZ(end_time, 'UTC', ?))", [$localTimezone, $localTimezone])
                ->update(['employee_working' => true]);
        });

        logger('[Employee] Finished updating slots');
    }
}
