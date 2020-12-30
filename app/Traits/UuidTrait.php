<?php

namespace App\Traits;

trait UuidTrait
{
    /**
     * Override Illuminate\Database\Eloquent\Model to disable incrementing IDs.
     * Always returns false.
     *
     * @return bool
     */
    public function getIncrementing()
    {
        return false;
    }

    /**
     * Boot UuidTrait and register Illuminate\Database\Eloquent\Model creating event to generated uuid.
     *
     * @return void
     */
    protected static function bootUuidTrait()
    {
        static::observe(new UuidObserver);
    }
}
