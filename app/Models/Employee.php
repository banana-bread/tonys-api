<?php 

namespace App\Models;

use App\Exceptions\BookingException;
use App\Helpers\BaseSchedule;
use App\Models\Contracts\UserModel;
use App\Traits\HasUuid;
use App\Traits\ReceivesEmails;

use Illuminate\Support\Collection;
use App\Jobs\UpdateEmployeeTimeSlots;
use App\Traits\CreatesTimeSlots;
use Illuminate\Support\Facades\DB;

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

    public function updateBaseSchedule(BaseSchedule $newBaseSchedule)
    {   
        // No changes made, exit
        if ($newBaseSchedule->matches($this->base_schedule)) return;

        $this->base_schedule = $newBaseSchedule;
        $this->save();

        UpdateEmployeeTimeSlots::dispatch($this);
    }


    public function createBooking(TimeSlot $startingSlot, Collection $serviceDefinitions)
    {
        return DB::transaction(function () use ($startingSlot, $serviceDefinitions) {

        $duration = $serviceDefinitions->sum('duration');
        $slotsRequired = $startingSlot->company->slotsRequiredFor($duration);

        $allSlots = $slotsRequired > 1
            ? $startingSlot->getNextSlots($slotsRequired)->prepend($startingSlot)
            : collect([$startingSlot]);

        if (TimeSlot::isReserved($allSlots))
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

            return $service;
        });

        $booking->services()->saveMany($services);

        return $booking;
        });
    }

    // TODO: this really should not be public, currently is so UpdateEmployeeTimeSlots 
    //       job can be performed seperately of base schedule update.
    public function updateTimeSlots()
    {
        TimeSlot::query()->update(['employee_working' => false]);

        $weekDays = collect(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']);

        $weekDays->each(function ($day, $key)
        {
            $startTime = $this->base_schedule->start($day);
            $endTime = $this->base_schedule->end($day);

            if (!$startTime || !$endTime) return;

            // TODO: this currently performs up to 7 updates, but 
            //       could be done more performantly in 1
            TimeSlot::where('start_time', '>=', now())
                ->whereRaw("WEEKDAY(start_time) = $key")
                ->whereRaw("TIME_TO_SEC(start_time) >= $startTime")
                ->whereRaw("TIME_TO_SEC(end_time) <= $endTime")
                ->update(['employee_working' => true]);
        });
    }
}
