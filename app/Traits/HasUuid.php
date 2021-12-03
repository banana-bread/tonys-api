<?php

namespace App\Traits;

trait HasUuid
{
    public function getIncrementing()
    {
        return false;
    }

    protected static function bootHasUuid()
    {
        static::observe(new UuidObserver);
    }
}
